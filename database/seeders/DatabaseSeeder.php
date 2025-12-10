<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Resepsionis;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Facilities;
use App\Models\FacilityHotel;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // ===== ADMIN DATA =====
        Admin::create([
            'name' => 'Admin Super',
            'email' => 'admin@bookingstay.com',
            'password' => Hash::make('password123'),
        ]);

        // ===== RESEPSIONIS DATA =====
        Resepsionis::create([
            'name' => 'Budi Resepsionis',
            'email' => 'resepsionis@bookingstay.com',
            'password' => Hash::make('password123'),
        ]);

        Resepsionis::create([
            'name' => 'Siti Resepsionis',
            'email' => 'siti@bookingstay.com',
            'password' => Hash::make('password123'),
        ]);

        // ===== CUSTOMER DATA =====
        Customer::create([
            'name' => 'Andi Wijaya',
            'email' => 'andi@customer.com',
            'phone' => '081234567890',
            'password' => Hash::make('password123'),
        ]);

        Customer::create([
            'name' => 'Dewi Putri',
            'email' => 'dewi@customer.com',
            'phone' => '082345678901',
            'password' => Hash::make('password123'),
        ]);

        for ($i = 0; $i < 3; $i++) {
            Customer::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'password' => Hash::make('password123'),
            ]);
        }

        // ===== FACILITIES DATA =====
        $facilities = [
            ['name' => 'WiFi Gratis', 'description' => 'Internet berkecepatan tinggi tersedia di seluruh hotel'],
            ['name' => 'Kolam Renang', 'description' => 'Kolam renang outdoor yang indah dengan pemandangan kota'],
            ['name' => 'Gym', 'description' => 'Fasilitas fitness lengkap dengan peralatan modern'],
            ['name' => 'Restoran', 'description' => 'Restoran dengan menu internasional dan lokal'],
            ['name' => 'Spa', 'description' => 'Layanan spa dan massage profesional'],
            ['name' => 'Parking', 'description' => 'Parkir gratis untuk semua tamu'],
            ['name' => 'AC', 'description' => 'Pendingin ruangan yang nyaman'],
            ['name' => 'TV Kabel', 'description' => 'Saluran TV kabel lengkap'],
        ];

        foreach ($facilities as $facility) {
            Facilities::create($facility);
        }

        // ===== FACILITY HOTEL DATA =====
        $facilityHotels = [
            ['name' => 'Swimming Pool', 'description' => 'Olympic size swimming pool dengan jacuzzi'],
            ['name' => 'Business Center', 'description' => 'Fasilitas meeting room dan conference'],
            ['name' => 'Concierge Service', 'description' => 'Layanan konsierge 24 jam'],
            ['name' => 'Valet Parking', 'description' => 'Layanan parkir dengan valet profesional'],
            ['name' => 'Room Service', 'description' => 'Layanan kamar 24 jam non-stop'],
        ];

        foreach ($facilityHotels as $fh) {
            FacilityHotel::create($fh);
        }

        // ===== HOTEL DATA =====
        $hotels = [
            [
                'name' => 'Grand Hotel Jakarta',
                'city' => 'Jakarta',
                'address' => 'Jl. Jenderal Sudirman No. 1, Jakarta Pusat',
                'description' => 'Hotel bintang lima dengan pemandangan kota yang spektakuler',
            ],
            [
                'name' => 'Nusa Dua Resort Bali',
                'city' => 'Bali',
                'address' => 'Jl. Pantai Mutiara No. 1, Nusa Dua',
                'description' => 'Resort mewah dengan pantai pribadi dan pemandangan laut yang indah',
            ],
            [
                'name' => 'Bandung City Hotel',
                'city' => 'Bandung',
                'address' => 'Jl. Diponegoro No. 55, Bandung',
                'description' => 'Hotel nyaman di pusat kota Bandung dengan harga terjangkau',
            ],
            [
                'name' => 'Surabaya Waterfront',
                'city' => 'Surabaya',
                'address' => 'Jl. Pemuda No. 100, Surabaya',
                'description' => 'Hotel modern dengan view ke sungai Kalimas',
            ],
        ];

        foreach ($hotels as $hotel) {
            Hotel::create($hotel);
        }

        // ===== ROOM TYPE DATA =====
        $roomTypeData = [
            [
                'name' => 'Standard Room',
                'price' => 500000,
                'capacity' => 2,
                'description' => 'Kamar standar yang nyaman untuk 2 tamu dengan fasilitas lengkap',
            ],
            [
                'name' => 'Deluxe Room',
                'price' => 800000,
                'capacity' => 3,
                'description' => 'Kamar deluxe yang luas dengan pemandangan kota',
            ],
            [
                'name' => 'Suite Room',
                'price' => 1500000,
                'capacity' => 4,
                'description' => 'Suite mewah dengan ruang terpisah dan jacuzzi pribadi',
            ],
            [
                'name' => 'Presidential Suite',
                'price' => 3000000,
                'capacity' => 6,
                'description' => 'Suite presidensial dengan fasilitas bintang lima lengkap',
            ],
        ];

        $hotels = Hotel::all();
        foreach ($hotels as $hotel) {
            foreach ($roomTypeData as $rt) {
                $roomType = RoomType::create(array_merge($rt, ['hotel_id' => $hotel->id]));
                
                // Attach random facilities to room type
                $facilityIds = Facilities::inRandomOrder()->limit(3)->pluck('id');
                $roomType->facilities()->attach($facilityIds);
            }
        }

        // ===== ROOM DATA =====
        $hotels = Hotel::all();
        $roomTypes = RoomType::all();
        
        foreach ($hotels as $hotel) {
            foreach ($roomTypes as $roomType) {
                for ($i = 1; $i <= 5; $i++) {
                    Room::create([
                        'room_type_id' => $roomType->id,
                        'number' => $hotel->id . '-' . $roomType->id . '-' . $i,
                        'status' => $faker->randomElement(['available', 'booked']),
                    ]);
                }
            }
        }

        // ===== BOOKING DATA =====
        $customers = Customer::all();
        $rooms = Room::all();
        
        foreach ($customers->take(5) as $customer) {
            for ($i = 0; $i < 2; $i++) {
                $checkIn = Carbon::now()->addDays(rand(1, 30));
                $checkOut = $checkIn->copy()->addDays(rand(1, 5));
                $room = $rooms->random();
                $roomType = $room->roomType;
                $nights = $checkOut->diffInDays($checkIn);
                $quantity = rand(1, 3);
                
                $booking = Booking::create([
                    'customer_id' => $customer->id,
                    'room_type_id' => $roomType->id,
                    'booking_code' => 'BK' . strtoupper(uniqid()),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'number_of_rooms' => $quantity,
                    'total_price' => $roomType->price * $nights * $quantity,
                    'status' => $faker->randomElement(['pending', 'confirmed', 'checked_in', 'cancelled']),
                    'special_requests' => $faker->sentence(5),
                ]);

                // Create transaction for confirmed/checked_in bookings
                if (in_array($booking->status, ['confirmed', 'checked_in'])) {
                    Transaction::create([
                        'booking_id' => $booking->id,
                        'payment_method' => $faker->randomElement(['credit_card', 'debit_card', 'bank_transfer', 'e_wallet', 'cash']),
                        'total' => $booking->total_price,
                        'status' => $faker->randomElement(['pending', 'paid']),
                        'payment_date' => $booking->status === 'checked_in' ? Carbon::now() : null,
                    ]);
                }
            }
        }
    }
}
