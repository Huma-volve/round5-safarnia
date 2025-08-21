# Tour Booking System - Setup Guide

## Overview
This tour booking system provides comprehensive functionality for managing tours, availability slots, bookings, and user profiles. It includes advanced search and filtering capabilities, real-time availability management, and a robust booking system.

## Features Implemented

### ✅ Home Page Display
- Categories with images, titles, and descriptions
- Recommended tours based on rating and popularity
- Tour counts per category
- Home page API endpoint with combined data

### ✅ Tour Booking System
- Complete tour booking workflow
- Time slot selection and availability management
- Seat management and booking validation
- Booking status tracking (pending, confirmed, cancelled, completed)

### ✅ Tour Availability Slots
- Admin can create multiple time slots for each tour
- Date and time-based availability
- Seat capacity management
- Bulk slot creation for efficiency

### ✅ Tour Management
- Comprehensive tour CRUD operations
- Category-based organization
- Rating and review system
- View tracking and popularity metrics

### ✅ Search and Filtering
- Keyword search across tour titles, descriptions, and locations
- Category filtering
- Price range filtering
- Rating filtering
- Location-based filtering
- Multiple sorting options (price, rating, views, title, date)

### ✅ User Profile Management
- Complete user profile CRUD operations
- Profile image management
- Password update functionality
- Booking history across all services
- Account deletion

### ✅ Booking Management
- Create, read, update, and delete bookings
- Booking status management
- Seat allocation and deallocation
- Booking validation and error handling

## Installation & Setup

### 1. Prerequisites
- PHP 8.1+
- Laravel 10+
- MySQL/PostgreSQL
- Composer
- Node.js & NPM (for frontend assets)

### 2. Clone and Install Dependencies
```bash
git clone <repository-url>
cd round5-safarnia
composer install
npm install
```

### 3. Environment Configuration
Copy the `.env.example` file and configure your database:
```bash
cp .env.example .env
```

Update the `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Run Migrations
```bash
php artisan migrate
```

### 6. Seed the Database
```bash
php artisan db:seed
```

This will create:
- Sample categories (Historical, Adventure, Cultural, Nature, Religious)
- Sample tours with realistic data
- Availability slots for the next 30 days
- Sample user accounts
- Sample bookings

### 7. Storage Setup
```bash
php artisan storage:link
```

### 8. Configure Search (Optional)
If you want to use Laravel Scout for advanced search:
```bash
composer require laravel/scout
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

## Database Structure

### Key Tables
- `categories`: Tour categories
- `tours`: Tour information
- `tour_availability_slots`: Available time slots
- `tour_bookings`: User bookings
- `users`: User accounts and profiles

### Relationships
- Category → Tours (One-to-Many)
- Tour → Availability Slots (One-to-Many)
- Tour → Bookings (One-to-Many through slots)
- User → Bookings (One-to-Many)

## API Endpoints

### Public Endpoints
- `GET /api/allcategory` - Get all categories
- `GET /api/home-page` - Get home page data
- `GET /api/recommendedtour` - Get recommended tours
- `GET /api/tours` - Search and filter tours
- `GET /api/tours/{id}` - Get tour details
- `GET /api/tours/{tour}/slots` - Get available slots

### Protected Endpoints (Require Authentication)
- `POST /api/tour-bookings` - Create booking
- `GET /api/my-tour-bookings` - Get user bookings
- `PUT /api/tour-bookings/{id}` - Update booking
- `DELETE /api/tour-bookings/{id}` - Delete booking
- `GET /api/profile` - Get user profile
- `POST /api/profile` - Update profile

### Admin Endpoints
- `POST /api/tours/{tour}/slots` - Create availability slot
- `POST /api/tours/{tour}/bulk-create-slots` - Bulk create slots
- `PUT /api/slots/{id}` - Update slot
- `DELETE /api/slots/{id}` - Delete slot

## Usage Examples

### 1. Search for Tours
```bash
# Search by keyword
GET /api/tours?search=pyramids

# Filter by price range
GET /api/tours?min_price=200&max_price=500

# Filter by category and rating
GET /api/tours?category_id=1&min_rating=4.0

# Sort by price (low to high)
GET /api/tours?sort_by=price&sort_order=asc
```

### 2. Book a Tour
```bash
POST /api/tour-bookings
Authorization: Bearer {your-token}

{
    "tour_slot_id": 1,
    "seats_count": 2,
    "notes": "Special request"
}
```

### 3. Get User Bookings
```bash
GET /api/my-tour-bookings
Authorization: Bearer {your-token}
```

### 4. Update Profile
```bash
POST /api/profile
Authorization: Bearer {your-token}

{
    "name": "John Doe",
    "phone": "+1234567890",
    "country": "USA"
}
```

## Testing

### Run Tests
```bash
php artisan test
```

### Test API Endpoints
Use tools like Postman or Insomnia to test the API endpoints. The API documentation provides detailed examples for each endpoint.

## Customization

### Adding New Tour Categories
1. Add the category to the `CategorySeeder`
2. Update the `TourSeeder` with tours in the new category
3. The system will automatically handle the new category

### Modifying Tour Fields
1. Update the `tours` table migration
2. Modify the `Tour` model
3. Update the `TourResource` for API responses
4. Adjust the seeders accordingly

### Adding New Booking Statuses
1. Add the status to the `TourBooking` model constants
2. Update the status management methods
3. Modify the booking validation rules

## Performance Considerations

### Database Indexing
Consider adding indexes on frequently queried fields:
- `tours.category_id`
- `tours.rating`
- `tours.price`
- `tour_availability_slots.start_time`
- `tour_bookings.user_id`

### Caching
Implement caching for:
- Category lists
- Popular tours
- Search results
- User profile data

### API Response Optimization
- Use eager loading for relationships
- Implement pagination for large datasets
- Consider API response compression

## Security Features

### Authentication
- Laravel Sanctum for API authentication
- Token-based authentication
- Automatic token expiration

### Validation
- Comprehensive input validation
- SQL injection protection
- XSS protection

### Authorization
- User can only access their own bookings
- Admin-only routes for slot management
- Proper middleware protection

## Troubleshooting

### Common Issues

1. **Migration Errors**
   - Ensure database credentials are correct
   - Check if all required tables exist
   - Run `php artisan migrate:fresh` if needed

2. **Seeding Errors**
   - Ensure migrations have run successfully
   - Check if required models exist
   - Verify database connections

3. **API Authentication Issues**
   - Ensure Bearer token is included in headers
   - Check if token is valid and not expired
   - Verify user exists in database

4. **Image Storage Issues**
   - Run `php artisan storage:link`
   - Check storage directory permissions
   - Verify image paths in seeders

### Debug Mode
Enable debug mode in `.env` for detailed error messages:
```env
APP_DEBUG=true
```

## Support

For technical support or questions:
1. Check the API documentation
2. Review the error logs
3. Contact the development team
4. Check Laravel documentation for framework-specific issues

## Contributing

When contributing to this system:
1. Follow Laravel coding standards
2. Add proper validation and error handling
3. Update tests for new functionality
4. Update API documentation
5. Follow the existing code structure

## License

This project is proprietary software. All rights reserved.
