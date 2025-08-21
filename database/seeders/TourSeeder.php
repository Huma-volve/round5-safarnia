<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\Category;
use Illuminate\Database\Seeder;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create categories
        $categories = $this->getCategories();

        $tours = [
            // Historical Tours
            [
                'category_id' => $categories['historical']->id,
                'title' => 'Pyramids of Giza & Sphinx',
                'description' => 'Explore the ancient wonders of Egypt including the Great Pyramid, Sphinx, and Valley Temple. Learn about the fascinating history of the Old Kingdom.',
                'price' => 450.00,
                'rating' => 4.8,
                'image' => 'pyramids.jpg',
                'views' => 2500,
                'location' => 'Giza, Egypt',
                'is_recommended' => true,
            ],
            [
                'category_id' => $categories['historical']->id,
                'title' => 'Luxor Temple & Valley of the Kings',
                'description' => 'Discover the magnificent temples of Luxor and explore the royal tombs in the Valley of the Kings. Experience the grandeur of ancient Thebes.',
                'price' => 380.00,
                'rating' => 4.7,
                'image' => 'luxor.jpg',
                'views' => 1800,
                'location' => 'Luxor, Egypt',
                'is_recommended' => true,
            ],
            [
                'category_id' => $categories['historical']->id,
                'title' => 'Islamic Cairo & Citadel',
                'description' => 'Walk through the historic Islamic quarter of Cairo, visit the magnificent Citadel of Saladin, and explore ancient mosques and markets.',
                'price' => 220.00,
                'rating' => 4.5,
                'image' => 'citadel.jpg',
                'views' => 1200,
                'location' => 'Cairo, Egypt',
                'is_recommended' => false,
            ],

            // Adventure Tours
            [
                'category_id' => $categories['adventure']->id,
                'title' => 'Sinai Desert Safari',
                'description' => 'Experience the thrill of desert adventure with 4x4 vehicles, camel rides, and overnight camping under the stars in the Sinai Peninsula.',
                'price' => 320.00,
                'rating' => 4.6,
                'image' => 'sinai.jpg',
                'views' => 950,
                'location' => 'Sinai Peninsula, Egypt',
                'is_recommended' => true,
            ],
            [
                'category_id' => $categories['adventure']->id,
                'title' => 'White Desert Camping',
                'description' => 'Camp in the surreal White Desert with its unique chalk rock formations. Enjoy stargazing and traditional Bedouin hospitality.',
                'price' => 280.00,
                'rating' => 4.4,
                'image' => 'white-desert.jpg',
                'views' => 750,
                'location' => 'Farafra, Egypt',
                'is_recommended' => false,
            ],

            // Cultural Tours
            [
                'category_id' => $categories['cultural']->id,
                'title' => 'Nubian Village Experience',
                'description' => 'Immerse yourself in Nubian culture with traditional music, local cuisine, and authentic village life along the Nile River.',
                'price' => 180.00,
                'rating' => 4.3,
                'image' => 'nubian.jpg',
                'views' => 600,
                'location' => 'Aswan, Egypt',
                'is_recommended' => false,
            ],
            [
                'category_id' => $categories['cultural']->id,
                'title' => 'Coptic Cairo & Churches',
                'description' => 'Explore the ancient Coptic Christian heritage of Cairo, including the Hanging Church and Coptic Museum.',
                'price' => 150.00,
                'rating' => 4.2,
                'image' => 'coptic.jpg',
                'views' => 450,
                'location' => 'Cairo, Egypt',
                'is_recommended' => false,
            ],

            // Nature Tours
            [
                'category_id' => $categories['nature']->id,
                'title' => 'Red Sea Diving Adventure',
                'description' => 'Dive into the crystal-clear waters of the Red Sea and discover vibrant coral reefs and marine life.',
                'price' => 420.00,
                'rating' => 4.9,
                'image' => 'red-sea.jpg',
                'views' => 2100,
                'location' => 'Hurghada, Egypt',
                'is_recommended' => true,
            ],
            [
                'category_id' => $categories['nature']->id,
                'title' => 'Fayoum Oasis & Waterfalls',
                'description' => 'Visit the beautiful Fayoum Oasis, see the stunning Wadi El-Rayan waterfalls, and explore the unique landscape.',
                'price' => 260.00,
                'rating' => 4.4,
                'image' => 'fayoum.jpg',
                'views' => 800,
                'location' => 'Fayoum, Egypt',
                'is_recommended' => false,
            ],

            // Religious Tours
            [
                'category_id' => $categories['religious']->id,
                'title' => 'Mount Sinai & St. Catherine',
                'description' => 'Climb Mount Sinai at sunrise and visit the historic St. Catherine Monastery, a UNESCO World Heritage site.',
                'price' => 350.00,
                'rating' => 4.7,
                'image' => 'mount-sinai.jpg',
                'views' => 1400,
                'location' => 'Sinai Peninsula, Egypt',
                'is_recommended' => true,
            ],
        ];

        foreach ($tours as $tour) {
            Tour::create($tour);
        }

        $this->command->info('Tours seeded successfully.');
    }

    /**
     * Get or create categories
     */
    private function getCategories()
    {
        $categories = [];

        // Historical Tours
        $categories['historical'] = Category::firstOrCreate(
            ['title' => 'Historical Tours'],
            [
                'description' => 'Explore ancient civilizations and historical landmarks',
                'image' => 'historical.jpg'
            ]
        );

        // Adventure Tours
        $categories['adventure'] = Category::firstOrCreate(
            ['title' => 'Adventure Tours'],
            [
                'description' => 'Thrilling outdoor adventures and desert experiences',
                'image' => 'adventure.jpg'
            ]
        );

        // Cultural Tours
        $categories['cultural'] = Category::firstOrCreate(
            ['title' => 'Cultural Tours'],
            [
                'description' => 'Immerse yourself in local culture and traditions',
                'image' => 'cultural.jpg'
            ]
        );

        // Nature Tours
        $categories['nature'] = Category::firstOrCreate(
            ['title' => 'Nature Tours'],
            [
                'description' => 'Discover natural wonders and wildlife',
                'image' => 'nature.jpg'
            ]
        );

        // Religious Tours
        $categories['religious'] = Category::firstOrCreate(
            ['title' => 'Religious Tours'],
            [
                'description' => 'Visit sacred sites and religious landmarks',
                'image' => 'religious.jpg'
            ]
        );

        return $categories;
    }
}
