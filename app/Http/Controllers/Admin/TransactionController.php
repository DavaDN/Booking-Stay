<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Exports\TransactionsExport;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Transaction::with('booking.customer');

        if ($search) {
            $query->whereHas('booking', function ($q) use ($search) {
                $q->where('booking_code', 'like', "%$search%");
            });
        }
        $transaction = $query->paginate(5)->appends($request->query());


        return view('admin.transaction.index', compact('transaction'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required|string',
            'total' => 'required|numeric'
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        $transaction = Transaction::create([
            'booking_id' => $booking->id,
            'payment_method' => $request->payment_method,
            'total' => $request->total,
            'status' => 'pending'
        ]);

        return response()->json($transaction, 201);
    }

    public function show($id)
    {
        return Transaction::with('booking.customer')->findOrFail($id);
    }

    public function updateStatus(Request $request, $id)
    {
        // Admin are not allowed to mark transactions as 'paid' manually
        $request->validate([
            'status' => 'required|in:pending,failed'
        ]);

        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            'status' => $request->status,
            'payment_date' => $request->status === 'paid' ? now() : null
        ]);

        return response()->json($transaction);
    }

    public function destroy($id)
    {
        Transaction::destroy($id);
        return response()->json(['message' => 'Transaction deleted']);
    }

    /**
     * Export transactions as PDF.
     */
    public function exportPdf(Request $request)
    {
        $transactions = Transaction::with('booking.customer')->get();

        // If Barryvdh DomPDF facade is available, use it
        if (class_exists('\\Barryvdh\\DomPDF\\Facade\\Pdf')) {
            try {
                return \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.transaction.export_pdf', compact('transactions'))
                    ->download('transactions.pdf');
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
            }
        }

        // If dompdf library is installed (but facade not configured), use it directly
        if (class_exists('Dompdf\\Dompdf')) {
            try {
                $html = view('admin.transaction.export_pdf', compact('transactions'))->render();
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();
                $output = $dompdf->output();
                return response($output, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="transactions.pdf"',
                ]);
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
            }
        }

        // Fallback: return HTML view with instructions to install PDF package
        return response()->view('admin.transaction.export_pdf', compact('transactions'))
            ->header('Content-Type', 'text/html');
    }

    /**
     * Export transactions as Excel (XLSX). Falls back to CSV if Excel package missing.
     */
    public function exportExcel(Request $request)
    {
        $transactions = Transaction::with('booking.customer')->get();

        // If Maatwebsite Excel is installed, use it
        if (class_exists('Maatwebsite\\Excel\\Facades\\Excel')) {
            try {
                return \Maatwebsite\Excel\Facades\Excel::download(new TransactionsExport($transactions), 'transactions.xlsx');
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal membuat Excel: ' . $e->getMessage());
            }
        }

        // Fallback to CSV
        $filename = 'transactions.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = ['ID','Booking Code','Customer','Payment Method','Total','Status','Created At'];

        $callback = function() use ($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->id,
                    $t->booking->booking_code ?? 'N/A',
                    $t->booking->customer->name ?? 'N/A',
                    ucfirst(str_replace('_', ' ', $t->payment_method)),
                    $t->total,
                    ucfirst($t->status),
                    $t->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
