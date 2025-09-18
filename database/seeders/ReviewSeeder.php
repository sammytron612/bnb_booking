<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Review;
use Carbon\Carbon;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, let's create some bookings if they don't exist
        $this->createBookingsIfNeeded();

        // Create reviews for light-house venue
        $this->createLightHouseReviews();

        // Create reviews for saras venue
        $this->createSarasReviews();
    }

    private function createBookingsIfNeeded()
    {
        // Check if we have bookings for each venue
        $lightHouseBookings = Booking::where('venue', 'light-house')->count();
        $sarasBookings = Booking::where('venue', 'saras')->count();

        if ($lightHouseBookings < 10) {
            for ($i = $lightHouseBookings; $i < 10; $i++) {
                Booking::create([
                    'name' => fake()->name(),
                    'email' => fake()->email(),
                    'phone' => fake()->phoneNumber(),
                    'check_in' => Carbon::now()->subDays(rand(30, 365)),
                    'check_out' => Carbon::now()->subDays(rand(1, 29)),
                    'venue' => 'light-house',
                    'nights' => rand(2, 7),
                    'total_price' => rand(200, 800),
                    'status' => 'confirmed',
                    'is_paid' => true,
                ]);
            }
        }

        if ($sarasBookings < 10) {
            for ($i = $sarasBookings; $i < 10; $i++) {
                Booking::create([
                    'name' => fake()->name(),
                    'email' => fake()->email(),
                    'phone' => fake()->phoneNumber(),
                    'check_in' => Carbon::now()->subDays(rand(30, 365)),
                    'check_out' => Carbon::now()->subDays(rand(1, 29)),
                    'venue' => 'saras',
                    'nights' => rand(2, 7),
                    'total_price' => rand(150, 600),
                    'status' => 'confirmed',
                    'is_paid' => true,
                ]);
            }
        }
    }

    private function createLightHouseReviews()
    {
        $lightHouseBookings = Booking::where('venue', 'light-house')->get();

        $lightHouseReviews = [
            [
                'name' => 'Sarah Johnson',
                'review' => 'Absolutely stunning property! The ocean views from every window were breathtaking. The lighthouse theme is beautifully executed throughout the house. We loved waking up to the sound of waves and spending evenings on the balcony watching the sunset. The kitchen was well-equipped and the beds were incredibly comfortable. Will definitely be back!',
                'rating' => 5,
            ],
            [
                'name' => 'Michael Chen',
                'review' => 'Perfect getaway for our anniversary! The Light House exceeded all our expectations. The attention to detail in the decor and the cleanliness of the property was outstanding. The location is ideal - peaceful yet close enough to local attractions. The host was very responsive and helpful. Highly recommended!',
                'rating' => 5,
            ],
            [
                'name' => 'Emma Williams',
                'review' => 'Beautiful property with amazing sea views! We stayed for a week and loved every moment. The house is spacious, modern, and has everything you need. The balcony is perfect for morning coffee and evening drinks. The beds are super comfortable and the bathroom is luxurious. Only minor issue was the WiFi was a bit slow, but honestly, it was nice to disconnect a bit!',
                'rating' => 4,
            ],
            [
                'name' => 'David Thompson',
                'review' => 'Fantastic location and beautiful house! We came with our family of four and there was plenty of space for everyone. The kids loved watching the boats from the windows and we enjoyed the peaceful setting. The kitchen had everything we needed to cook meals. Great value for money and would definitely stay again.',
                'rating' => 5,
            ],
            [
                'name' => 'Lisa Rodriguez',
                'review' => 'The Light House is a gem! From the moment we walked in, we were impressed by the stunning decor and cleanliness. The ocean views are incredible and the sound of the waves is so relaxing. The host provided excellent recommendations for local restaurants and activities. This place is perfect for a romantic getaway or family vacation.',
                'rating' => 5,
            ],
            [
                'name' => 'James Mitchell',
                'review' => 'Great property overall! The house is well-maintained and the lighthouse theme is charming. We particularly enjoyed the spacious living area and the modern amenities. The location offers beautiful walks along the coast. The only downside was that the heating took a while to warm up the house, but once it did, it was very comfortable.',
                'rating' => 4,
            ],
            [
                'name' => 'Rachel Green',
                'review' => 'Wonderful stay at The Light House! The property is exactly as described and the photos don\'t do justice to the actual beauty of the place. We loved the peaceful atmosphere and the stunning sunrise views. The house is well-equipped with modern appliances and comfortable furniture. Perfect for our girls\' weekend getaway!',
                'rating' => 5,
            ],
            [
                'name' => 'Tom Anderson',
                'review' => 'Really enjoyed our stay! The house has character and the sea views are spectacular. We spent most of our time on the balcony just taking in the scenery. The beds were comfortable and the shower pressure was excellent. The kitchen had all the basics we needed. Would recommend to anyone looking for a peaceful coastal retreat.',
                'rating' => 4,
            ],
            [
                'name' => 'Sophie Martin',
                'review' => 'The Light House is absolutely magical! We celebrated our 10th wedding anniversary here and it couldn\'t have been more perfect. The romantic atmosphere, stunning views, and luxurious amenities made our stay unforgettable. The host was wonderful and even left us a bottle of champagne as a surprise. Five stars all the way!',
                'rating' => 5,
            ],
            [
                'name' => 'Mark Wilson',
                'review' => 'Solid property with great views! The house is clean, well-maintained, and has everything you need for a comfortable stay. We enjoyed the peaceful location and the easy access to the beach. The lighthouse theme is tastefully done without being overwhelming. Good value for the price and we would consider staying again.',
                'rating' => 4,
            ],
        ];

        foreach ($lightHouseReviews as $index => $reviewData) {
            if (isset($lightHouseBookings[$index])) {
                Review::create([
                    'name' => $reviewData['name'],
                    'review' => $reviewData['review'],
                    'rating' => $reviewData['rating'],
                    'booking_id' => $lightHouseBookings[$index]->id,
                    'created_at' => Carbon::now()->subDays(rand(1, 180)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 180)),
                ]);
            }
        }
    }

    private function createSarasReviews()
    {
        $sarasBookings = Booking::where('venue', 'saras')->get();

        $sarasReviews = [
            [
                'name' => 'Jennifer Adams',
                'review' => 'Sara\'s is a delightful coastal retreat! The cottage has so much character and charm. We loved the cozy atmosphere and the beautiful garden. The kitchen is well-equipped and perfect for preparing meals with local ingredients. The host was incredibly welcoming and provided great local tips. A perfect place to unwind and relax.',
                'rating' => 5,
            ],
            [
                'name' => 'Robert Clarke',
                'review' => 'Charming property with excellent service! Sara\'s exceeded our expectations with its attention to detail and comfort. The cottage feels like a home away from home with all the modern amenities you could need. The location is perfect for exploring the local area, and the sea views are stunning. We had a fantastic stay!',
                'rating' => 5,
            ],
            [
                'name' => 'Amanda Foster',
                'review' => 'Lovely little cottage by the sea! We stayed at Sara\'s for our honeymoon and it was perfect. The romantic setting, beautiful decor, and peaceful location made our stay magical. The bed was incredibly comfortable and we loved having breakfast on the patio while listening to the waves. Highly recommend for couples!',
                'rating' => 5,
            ],
            [
                'name' => 'Paul Harrison',
                'review' => 'Great value and wonderful location! Sara\'s offers everything you need for a relaxing coastal holiday. The cottage is clean, comfortable, and has a real homey feel. We particularly enjoyed the outdoor space and the proximity to local walks. The host was very helpful and responsive. Would definitely stay again!',
                'rating' => 4,
            ],
            [
                'name' => 'Catherine Lee',
                'review' => 'Beautiful cottage with stunning views! Sara\'s is perfectly positioned to enjoy the best of coastal living. The interior is tastefully decorated and very comfortable. We loved cooking in the well-equipped kitchen and dining while overlooking the sea. The whole experience was relaxing and rejuvenating. Perfect for a peaceful getaway!',
                'rating' => 5,
            ],
            [
                'name' => 'Steven Wright',
                'review' => 'Excellent stay at Sara\'s! The cottage has everything you could want for a comfortable holiday. The location is ideal for exploring the coastline and the local area. We appreciated the thoughtful touches throughout the property and the quality of the furnishings. The garden is lovely and perfect for evening relaxation.',
                'rating' => 4,
            ],
            [
                'name' => 'Helen Murphy',
                'review' => 'Sara\'s is a hidden gem! We had the most wonderful time staying in this charming cottage. The sea views are incredible and the sound of the waves is so soothing. The cottage is beautifully maintained and has all the modern conveniences while retaining its character. Perfect for a romantic weekend or peaceful retreat.',
                'rating' => 5,
            ],
            [
                'name' => 'Andrew Scott',
                'review' => 'Really enjoyed our time at Sara\'s! The cottage is cozy and well-appointed with everything we needed. The location offers great access to coastal walks and local attractions. We loved the peaceful setting and the beautiful sunrise views. The host was great to deal with and very accommodating. Good value for money.',
                'rating' => 4,
            ],
            [
                'name' => 'Natalie Cooper',
                'review' => 'Absolutely loved Sara\'s! This cottage is the perfect blend of comfort and character. We felt completely at home from the moment we arrived. The sea views are breathtaking and the location is ideal for both relaxation and exploration. The cottage is immaculately clean and beautifully decorated. We\'re already planning our next visit!',
                'rating' => 5,
            ],
            [
                'name' => 'Gary Phillips',
                'review' => 'Solid choice for a coastal getaway! Sara\'s offers good value and a comfortable stay. The cottage has character and the location is great for accessing local amenities and attractions. We enjoyed the peaceful atmosphere and the quality of the accommodation. The host was helpful and the check-in process was smooth. Would recommend to others.',
                'rating' => 4,
            ],
        ];

        foreach ($sarasReviews as $index => $reviewData) {
            if (isset($sarasBookings[$index])) {
                Review::create([
                    'name' => $reviewData['name'],
                    'review' => $reviewData['review'],
                    'rating' => $reviewData['rating'],
                    'booking_id' => $sarasBookings[$index]->id,
                    'created_at' => Carbon::now()->subDays(rand(1, 180)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 180)),
                ]);
            }
        }
    }
}
