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
                'image' => 'https://c4.wallpaperflare.com/wallpaper/756/599/630/man-made-udaipur-hotel-hotel-india-wallpaper-preview.jpg',
                'views' => 2500,
                'location' => 'Giza, Egypt',
                'is_recommended' => true,
                'duration_hours' => 8,
                'max_group_size' => 25,
                'min_age' => 8,
                'difficulty_level' => 'moderate',
                'highlights' => ['Great Pyramid', 'Sphinx', 'Valley Temple', 'Historical insights'],
                'included_services' => ['Transportation', 'Expert guide', 'Lunch', 'Entry fees', 'Water'],
                'excluded_services' => ['Tips', 'Personal expenses', 'Optional activities'],
                'what_to_bring' => ['Comfortable shoes', 'Sunscreen', 'Camera', 'Hat', 'Light clothes'],
                'cancellation_policy' => 'Free cancellation up to 24 hours before the tour',
            ],
            [
                'category_id' => $categories['historical']->id,
                'title' => 'Luxor Temple & Valley of the Kings',
                'description' => 'Discover the magnificent temples of Luxor and explore the royal tombs in the Valley of the Kings. Experience the grandeur of ancient Thebes.',
                'price' => 380.00,
                'rating' => 4.7,
                'image' => 'https://c4.wallpaperflare.com/wallpaper/219/894/458/5bd329b2b5293-wallpaper-preview.jpg',
                'views' => 1800,
                'location' => 'Luxor, Egypt',
                'is_recommended' => true,
                'duration_hours' => 10,
                'max_group_size' => 20,
                'min_age' => 10,
                'difficulty_level' => 'moderate',
                'highlights' => ['Luxor Temple', 'Valley of the Kings', 'Ancient tombs', 'Historical narration'],
                'included_services' => ['Transportation', 'Expert guide', 'Lunch', 'Entry fees', 'Air conditioning'],
                'excluded_services' => ['Tips', 'Personal expenses', 'Optional activities'],
                'what_to_bring' => ['Comfortable walking shoes', 'Light clothes', 'Camera', 'Water bottle'],
                'cancellation_policy' => 'Free cancellation up to 48 hours before the tour',
            ],
            [
                'category_id' => $categories['historical']->id,
                'title' => 'Islamic Cairo & Citadel',
                'description' => 'Walk through the historic Islamic quarter of Cairo, visit the magnificent Citadel of Saladin, and explore ancient mosques and markets.',
                'price' => 220.00,
                'rating' => 4.5,
                'image' => 'https://c4.wallpaperflare.com/wallpaper/373/217/146/man-made-rajasthan-wallpaper-preview.jpg',
                'views' => 1200,
                'location' => 'Cairo, Egypt',
                'is_recommended' => false,
                'duration_hours' => 6,
                'max_group_size' => 15,
                'min_age' => 6,
                'difficulty_level' => 'easy',
                'highlights' => ['Islamic architecture', 'Citadel of Saladin', 'Ancient mosques', 'Historic markets'],
                'included_services' => ['Transportation', 'Local guide', 'Entry fees', 'Refreshments'],
                'excluded_services' => ['Tips', 'Personal expenses', 'Shopping'],
                'what_to_bring' => ['Modest clothing', 'Comfortable shoes', 'Camera', 'Respectful attitude'],
                'cancellation_policy' => 'Free cancellation up to 12 hours before the tour',
            ],

            // Adventure Tours
            [
                'category_id' => $categories['adventure']->id,
                'title' => 'Sinai Desert Safari',
                'description' => 'Experience the thrill of desert adventure with 4x4 vehicles, camel rides, and overnight camping under the stars in the Sinai Peninsula.',
                'price' => 320.00,
                'rating' => 4.6,
                'image' => 'https://c4.wallpaperflare.com/wallpaper/871/418/390/city-cityscape-dubai-united-arab-emirates-wallpaper-preview.jpg',
                'views' => 950,
                'location' => 'Sinai Peninsula, Egypt',
                'is_recommended' => true,
                'duration_hours' => 24,
                'max_group_size' => 12,
                'min_age' => 12,
                'difficulty_level' => 'challenging',
                'highlights' => ['4x4 desert driving', 'Camel rides', 'Overnight camping', 'Stargazing', 'Bedouin experience'],
                'included_services' => ['4x4 vehicle', 'Camel ride', 'Camping equipment', 'Dinner & breakfast', 'Guide'],
                'excluded_services' => ['Personal items', 'Sleeping bag', 'Tips'],
                'what_to_bring' => ['Warm clothes', 'Sleeping bag', 'Personal toiletries', 'Camera', 'Comfortable shoes'],
                'cancellation_policy' => 'Free cancellation up to 72 hours before the tour',
            ],
            [
                'category_id' => $categories['adventure']->id,
                'title' => 'White Desert Camping',
                'description' => 'Camp in the surreal White Desert with its unique chalk rock formations. Enjoy stargazing and traditional Bedouin hospitality.',
                'price' => 280.00,
                'rating' => 4.4,
                'image' => 'https://c4.wallpaperflare.com/wallpaper/159/675/616/man-made-village-house-landscape-wallpaper-thumb.jpg',
                'views' => 750,
                'location' => 'Farafra, Egypt',
                'is_recommended' => false,
                'duration_hours' => 36,
                'max_group_size' => 15,
                'min_age' => 14,
                'difficulty_level' => 'challenging',
                'highlights' => ['White Desert', 'Rock formations', 'Stargazing', 'Bedouin hospitality', 'Desert camping'],
                'included_services' => ['Transportation', 'Camping equipment', 'Meals', 'Guide', 'Safety equipment'],
                'excluded_services' => ['Personal items', 'Sleeping bag', 'Tips'],
                'what_to_bring' => ['Warm clothes', 'Sleeping bag', 'Personal toiletries', 'Camera', 'Hiking boots'],
                'cancellation_policy' => 'Free cancellation up to 48 hours before the tour',
            ],

            // Cultural Tours
            [
                'category_id' => $categories['cultural']->id,
                'title' => 'Nubian Village Experience',
                'description' => 'Immerse yourself in Nubian culture with traditional music, local cuisine, and authentic village life along the Nile River.',
                'price' => 180.00,
                'rating' => 4.3,
                'image' => 'https://c4.wallpaperflare.com/wallpaper/747/10/416/dubai-united-arab-emirates-evening-burj-khalifa-wallpaper-preview.jpg',
                'views' => 600,
                'location' => 'Aswan, Egypt',
                'is_recommended' => false,
                'duration_hours' => 8,
                'max_group_size' => 18,
                'min_age' => 5,
                'difficulty_level' => 'easy',
                'highlights' => ['Nubian culture', 'Traditional music', 'Local cuisine', 'Village life', 'Nile views'],
                'included_services' => ['Transportation', 'Local guide', 'Traditional meal', 'Cultural activities', 'Boat ride'],
                'excluded_services' => ['Tips', 'Personal purchases', 'Optional activities'],
                'what_to_bring' => ['Comfortable clothes', 'Camera', 'Open mind', 'Respectful attitude'],
                'cancellation_policy' => 'Free cancellation up to 24 hours before the tour',
            ],
            [
                'category_id' => $categories['cultural']->id,
                'title' => 'Coptic Cairo & Churches',
                'description' => 'Explore the ancient Coptic Christian heritage of Cairo, including the Hanging Church and Coptic Museum.',
                'price' => 150.00,
                'rating' => 4.2,
                'image' => 'https://c1.wallpaperflare.com/preview/549/480/26/taj-mahal-palace-hotel-5-star-hotel-mumbai.jpg',
                'views' => 450,
                'location' => 'Cairo, Egypt',
                'is_recommended' => false,
                'duration_hours' => 5,
                'max_group_size' => 12,
                'min_age' => 8,
                'difficulty_level' => 'easy',
                'highlights' => ['Coptic heritage', 'Hanging Church', 'Coptic Museum', 'Religious history', 'Ancient architecture'],
                'included_services' => ['Transportation', 'Expert guide', 'Entry fees', 'Refreshments'],
                'excluded_services' => ['Tips', 'Personal expenses', 'Donations'],
                'what_to_bring' => ['Modest clothing', 'Comfortable shoes', 'Camera', 'Respectful attitude'],
                'cancellation_policy' => 'Free cancellation up to 12 hours before the tour',
            ],

            // Nature Tours
            [
                'category_id' => $categories['nature']->id,
                'title' => 'Red Sea Diving Adventure',
                'description' => 'Dive into the crystal-clear waters of the Red Sea and discover vibrant coral reefs and marine life.',
                'price' => 420.00,
                'rating' => 4.9,
                'image' => 'https://c4.wallpaperflare.com/wallpaper/653/585/146/life-resort-house-architecture-wallpaper-preview.jpg',
                'views' => 2100,
                'location' => 'Hurghada, Egypt',
                'is_recommended' => true,
                'duration_hours' => 8,
                'max_group_size' => 8,
                'min_age' => 12,
                'difficulty_level' => 'moderate',
                'highlights' => ['Coral reefs', 'Marine life', 'Professional diving', 'Crystal clear water', 'Underwater photography'],
                'included_services' => ['Diving equipment', 'Professional instructor', 'Boat trip', 'Lunch', 'Safety briefing'],
                'excluded_services' => ['Personal insurance', 'Tips', 'Personal items'],
                'what_to_bring' => ['Swimsuit', 'Towel', 'Sunscreen', 'Change of clothes', 'Camera'],
                'cancellation_policy' => 'Free cancellation up to 48 hours before the tour',
            ],
            [
                'category_id' => $categories['nature']->id,
                'title' => 'Fayoum Oasis & Waterfalls',
                'description' => 'Visit the beautiful Fayoum Oasis, see the stunning Wadi El-Rayan waterfalls, and explore the unique landscape.',
                'price' => 260.00,
                'rating' => 4.4,
                'image' => 'https://c4.wallpaperflare.com/wallpaper/1000/327/737/man-made-devil-s-bridge-bridge-germany-wallpaper-thumb.jpg',
                'views' => 800,
                'location' => 'Fayoum, Egypt',
                'is_recommended' => false,
                'duration_hours' => 10,
                'max_group_size' => 20,
                'min_age' => 6,
                'difficulty_level' => 'easy',
                'highlights' => ['Fayoum Oasis', 'Wadi El-Rayan', 'Waterfalls', 'Natural landscape', 'Bird watching'],
                'included_services' => ['Transportation', 'Local guide', 'Lunch', 'Entry fees', 'Refreshments'],
                'excluded_services' => ['Tips', 'Personal expenses', 'Optional activities'],
                'what_to_bring' => ['Comfortable shoes', 'Light clothes', 'Camera', 'Hat', 'Water bottle'],
                'cancellation_policy' => 'Free cancellation up to 24 hours before the tour',
            ],

            // Religious Tours
            [
                'category_id' => $categories['religious']->id,
                'title' => 'Mount Sinai & St. Catherine',
                'description' => 'Climb Mount Sinai at sunrise and visit the historic St. Catherine Monastery, a UNESCO World Heritage site.',
                'price' => 350.00,
                'rating' => 4.7,
                'image' => 'https://c1.wallpaperflare.com/preview/985/72/873/israel-dome-of-the-rock-jerusalem-temple-mount-thumbnail.jpg',
                'views' => 1400,
                'location' => 'Sinai Peninsula, Egypt',
                'is_recommended' => true,
                'duration_hours' => 16,
                'max_group_size' => 25,
                'min_age' => 14,
                'difficulty_level' => 'challenging',
                'highlights' => ['Mount Sinai climb', 'Sunrise views', 'St. Catherine Monastery', 'Religious significance', 'UNESCO site'],
                'included_services' => ['Transportation', 'Expert guide', 'Breakfast', 'Entry fees', 'Safety equipment'],
                'excluded_services' => ['Personal equipment', 'Tips', 'Personal expenses'],
                'what_to_bring' => ['Warm clothes', 'Hiking shoes', 'Water', 'Camera', 'Personal items'],
                'cancellation_policy' => 'Free cancellation up to 72 hours before the tour',
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
                'image' => 'https://c1.wallpaperflare.com/preview/652/179/664/udaipur-palace-lake-rajasthan.jpg'
            ]
        );

        // Adventure Tours
        $categories['adventure'] = Category::firstOrCreate(
            ['title' => 'Adventure Tours'],
            [
                'description' => 'Thrilling outdoor adventures and desert experiences',
                'image' => 'https://c4.wallpaperflare.com/wallpaper/889/969/207/las-vegas-hotel-fountain-cityscape-wallpaper-preview.jpg'
            ]
        );

        // Cultural Tours
        $categories['cultural'] = Category::firstOrCreate(
            ['title' => 'Cultural Tours'],
            [
                'description' => 'Immerse yourself in local culture and traditions',
                'image' => 'https://c4.wallpaperflare.com/wallpaper/373/217/146/man-made-rajasthan-wallpaper-preview.jpg'
            ]
        );

        // Nature Tours
        $categories['nature'] = Category::firstOrCreate(
            ['title' => 'Nature Tours'],
            [
                'description' => 'Discover natural wonders and wildlife',
                'image' => 'https://c4.wallpaperflare.com/wallpaper/295/606/441/bali-bungalow-cities-hotel-wallpaper-preview.jpg'
            ]
        );

        // Religious Tours
        $categories['religious'] = Category::firstOrCreate(
            ['title' => 'Religious Tours'],
            [
                'description' => 'Visit sacred sites and religious landmarks',
                'image' => 'https://c1.wallpaperflare.com/preview/216/392/890/israel-dome-of-the-rock-jerusalem-temple-mount.jpg'
            ]
        );

        return $categories;
    }
}
