<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Resepsionis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingPaidMail;

class TransactionObserver
{
    public function updated(Transaction $transaction)
    {
        $original = $transaction->getOriginal('status');
        $current = $transaction->status;

        // If status didn't change, nothing to do
        if ($original === $current) return;

        $booking = $transaction->booking;
        if (!$booking) return;

        // Map transaction status to booking status
        if ($current === 'paid') {
            $booking->status = 'paid';
        } elseif ($current === 'pending') {
            $booking->status = 'pending';
        } else {
            $booking->status = 'cancelled';
        }
        $booking->save();

        // If transaction became paid now, allocate rooms and notify resepsionis
        if ($current === 'paid' && $original !== 'paid') {
            try {
                // allocate rooms preferring booking.room_ids
                if (!empty($booking->room_ids) && is_array($booking->room_ids)) {
                    $roomIds = $booking->room_ids;
                    DB::transaction(function () use ($roomIds, $booking) {
                            foreach ($roomIds as $rid) {
                                $room = Room::lockForUpdate()->find($rid);
                                if ($room) {
                                    $overlap = Reservation::where('room_id', $room->id)
                                        ->where('status', '!=', 'cancelled')
                                        ->where(function ($q) use ($booking) {
                                            $q->where('check_in_date', '<', $booking->check_out->format('Y-m-d'))
                                              ->where('check_out_date', '>', $booking->check_in->format('Y-m-d'));
                                        })->exists();

                                    if (!$overlap) {
                                        Reservation::create([
                                            'customer_id' => $booking->customer_id,
                                            'room_id' => $room->id,
                                            'check_in_date' => $booking->check_in->format('Y-m-d'),
                                            'check_in_time' => $booking->check_in->format('H:i:s'),
                                            'check_out_date' => $booking->check_out->format('Y-m-d'),
                                            'check_out_time' => $booking->check_out->format('H:i:s'),
                                            'status' => 'paid',
                                        ]);
                                    } else {
                                        Log::warning('Preferred room not available (observer)', ['booking_id' => $booking->id, 'room_id' => $rid]);
                                    }
                                } else {
                                    Log::warning('Preferred room not found (observer)', ['booking_id' => $booking->id, 'room_id' => $rid]);
                                }
                            }
                    }, 5);
                } else {
                    $roomTypeId = $booking->room_type_id ?? null;
                    $roomsNeeded = (int) ($booking->number_of_rooms ?? 1);
                    if ($roomTypeId && $roomsNeeded > 0) {
                        DB::transaction(function () use ($roomTypeId, $roomsNeeded, $booking) {
                            $candidates = Room::where('room_type_id', $roomTypeId)->lockForUpdate()->get();

                            $allocated = 0;
                            foreach ($candidates as $room) {
                                if ($allocated >= $roomsNeeded) break;

                                $overlap = Reservation::where('room_id', $room->id)
                                    ->where('status', '!=', 'cancelled')
                                    ->where(function ($q) use ($booking) {
                                        $q->where('check_in_date', '<', $booking->check_out->format('Y-m-d'))
                                          ->where('check_out_date', '>', $booking->check_in->format('Y-m-d'));
                                    })->exists();

                                if (!$overlap) {
                                    Reservation::create([
                                        'customer_id' => $booking->customer_id,
                                        'room_id' => $room->id,
                                        'check_in_date' => $booking->check_in->format('Y-m-d'),
                                        'check_in_time' => $booking->check_in->format('H:i:s'),
                                        'check_out_date' => $booking->check_out->format('Y-m-d'),
                                        'check_out_time' => $booking->check_out->format('H:i:s'),
                                        'status' => 'paid',
                                    ]);
                                    $allocated++;
                                }
                            }
                        }, 5);
                    }
                }

                // notify resepsionis
                $rooms = collect();
                if (!empty($booking->room_ids) && is_array($booking->room_ids)) {
                    $rooms = Room::whereIn('id', $booking->room_ids)->get();
                } else {
                    $resRoomIds = Reservation::where('check_in_date', $booking->check_in->format('Y-m-d'))
                        ->where('check_out_date', $booking->check_out->format('Y-m-d'))
                        ->whereHas('room', function($q) use ($booking) {
                            $q->where('room_type_id', $booking->room_type_id);
                        })->where('status', 'paid')->pluck('room_id')->toArray();

                    if (!empty($resRoomIds)) {
                        $rooms = Room::whereIn('id', $resRoomIds)->get();
                    }
                }

                $hotelId = optional($booking->roomType)->hotel_id ?? null;
                if ($hotelId) {
                    $reseps = Resepsionis::where('hotel_id', $hotelId)->get();
                    foreach ($reseps as $r) {
                        try {
                            Mail::to($r->email)->send(new BookingPaidMail($booking, $rooms));
                        } catch (\Exception $e) {
                            Log::warning('Failed sending booking paid mail to resepsionis (observer) ' . $r->email . ': ' . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('TransactionObserver post-paid processing failed: ' . $e->getMessage(), ['transaction_id' => $transaction->id]);
            }
        }
    }
}
