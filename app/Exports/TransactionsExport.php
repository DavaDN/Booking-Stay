<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $transactions;

    public function __construct($transactions = null)
    {
        $this->transactions = $transactions ?: Transaction::with('booking.customer')->get();
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Booking Code',
            'Customer',
            'Payment Method',
            'Total',
            'Status',
            'Created At',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->booking->booking_code ?? 'N/A',
            $transaction->booking->customer->name ?? $transaction->booking->customer_name ?? 'N/A',
            ucfirst(str_replace('_', ' ', $transaction->payment_method)),
            $transaction->total,
            ucfirst($transaction->status),
            $transaction->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
