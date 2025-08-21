# Tour Booking API Documentation

## Overview
This API provides comprehensive tour booking functionality including search, filtering, booking management, and user profile features.

## Base URL
```
http://your-domain.com/api
```

## Authentication
Most endpoints require authentication using Laravel Sanctum. Include the Bearer token in the Authorization header:
```
Authorization: Bearer {your-token}
```

## Endpoints

### 1. Home Page & Categories

#### Get All Categories
```
GET /allcategory
```
Returns basic category information.

#### Get Categories with Tour Counts
```
GET /categories-with-count
```
Returns categories with the number of tours in each category.

#### Get Categories with Recommended Tours
```
GET /categories-with-recommended-tours
```
Returns categories with their top 3 recommended tours.

#### Get Home Page Data
```
GET /home-page
```
Returns both categories and recommended tours for the home page.

### 2. Recommended Tours

#### Get Recommended Tours
```
GET /recommendedtour
```
Returns top 5 recommended tours by rating.

#### Get Top Rated Tours
```
GET /top-rated-tours
```
Returns top 10 tours with rating >= 4.0.

#### Get Most Viewed Tours
```
GET /most-viewed-tours
```
Returns top 10 tours by view count.

#### Get Trending Tours
```
GET /trending-tours
```
Returns top 8 tours based on rating and views combination.

#### Get Recommended Tours by Category
```
GET /recommended-tours-by-category/{categoryId}
```
Returns top 5 recommended tours in a specific category.

### 3. Tour Search & Filtering

#### Get All Tours with Filters
```
GET /tours
```
**Query Parameters:**
- `search`: Search by keyword (title, description, location)
- `category_id`: Filter by category
- `min_price`: Minimum price filter
- `max_price`: Maximum price filter
- `min_rating`: Minimum rating filter
- `location`: Filter by location
- `sort_by`: Sort by (price, rating, views, title, created_at)
- `sort_order`: Sort order (asc, desc)
- `per_page`: Results per page (default: 15)

#### Get Tour Details
```
GET /tours/{tour}
```
Returns detailed tour information with availability slots.

#### Get Tours by Category
```
GET /tours-by-category/{category}
```
Returns all tours in a specific category.

#### Get Top Rated Tours
```
GET /top-rated-tours
```
Returns tours with rating >= 4.0.

#### Get Most Viewed Tours
```
GET /most-viewed-tours
```
Returns tours ordered by view count.

#### Get Available Tours
```
GET /available-tours
```
Returns tours that have available time slots.

### 4. Tour Availability Slots

#### Get Available Slots for a Tour
```
GET /tours/{tour}/slots
```
Returns available time slots for a specific tour.

#### Get Slots by Date Range
```
GET /tours/{tour}/slots-by-date-range
```
**Query Parameters:**
- `start_date`: Start date (YYYY-MM-DD)
- `end_date`: End date (YYYY-MM-DD)

#### Get All Slots (Admin)
```
GET /tours/{tour}/all-slots
```
Returns all slots including those with bookings.

#### Create New Slot (Admin)
```
POST /tours/{tour}/slots
```
**Body:**
```json
{
    "start_time": "2025-08-15 09:00:00",
    "end_time": "2025-08-15 12:00:00",
    "available_seats": 20,
    "max_seats": 25
}
```

#### Bulk Create Slots (Admin)
```
POST /tours/{tour}/bulk-create-slots
```
**Body:**
```json
{
    "slots": [
        {
            "start_time": "2025-08-15 09:00:00",
            "end_time": "2025-08-15 12:00:00",
            "available_seats": 20,
            "max_seats": 25
        }
    ]
}
```

#### Update Slot (Admin)
```
PUT /slots/{slot}
```
**Body:** Same as create, but fields are optional.

#### Delete Slot (Admin)
```
DELETE /slots/{slot}
```

### 5. Tour Bookings

#### Create Booking
```
POST /tour-bookings
```
**Body:**
```json
{
    "tour_slot_id": 1,
    "seats_count": 2,
    "notes": "Special request"
}
```

#### Get Booking Details
```
GET /tour-bookings/{id}
```
Returns detailed booking information.

#### Update Booking
```
PUT /tour-bookings/{id}
```
**Body:** Same as create, but fields are optional.

#### Cancel Booking
```
POST /tour-bookings/{id}/cancel 
```

#### Delete Booking
```
DELETE /tour-bookings/{id}
```

#### Get My Bookings
```
GET /my-tour-bookings
```
Returns all bookings for the authenticated user.

### 6. User Profile

#### Get Profile
```
GET /profile
```
Returns user profile with statistics.

#### Update Profile
```
POST /profile
```
**Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "country": "USA"
}
```

#### Update Password
```
POST /profile/update-password
```
**Body:**
```json
{
    "current_password": "oldpassword",
    "new_password": "newpassword",
    "new_password_confirmation": "newpassword"
}
```

#### Delete Account
```
POST /profile/delete-account
```

#### Get Booking History
```
GET /profile/booking-history
```
Returns user's booking history across all services.

### 7. Authentication

#### Register
```
POST /register
```
**Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

#### Login
```
POST /login
```
**Body:**
```json
{
    "email": "john@example.com",
    "password": "password"
}
```

#### Logout
```
POST /logout
```

## Response Format

All successful responses follow this format:
```json
{
    "status": true,
    "message": "Success message",
    "data": { ... }
}
```

Error responses:
```json
{
    "status": false,
    "message": "Error message",
    "errors": { ... }
}
```

## Status Codes

- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Validation Error
- `500`: Server Error

## Tour Booking Statuses

- `pending`: Booking is pending confirmation
- `confirmed`: Booking is confirmed
- `cancelled`: Booking is cancelled
- `completed`: Tour has been completed

## Search & Filter Examples

### Search by Keyword
```
GET /tours?search=pyramids
```

### Filter by Price Range
```
GET /tours?min_price=200&max_price=500
```

### Filter by Rating
```
GET /tours?min_rating=4.0
```

### Sort by Price (Low to High)
```
GET /tours?sort_by=price&sort_order=asc
```

### Filter by Category and Location
```
GET /tours?category_id=1&location=cairo
```

## Notes

1. **Admin Routes**: Some routes are marked as admin-only and should be protected with appropriate middleware.
2. **Image Storage**: All images are stored in the public storage disk and URLs are generated automatically.
3. **Pagination**: List endpoints support pagination with customizable page sizes.
4. **Search**: Full-text search is available for tours using Laravel Scout.
5. **Validation**: All input is validated according to Laravel validation rules.
6. **Relationships**: Most endpoints include related data to minimize API calls.

## Testing

You can test the API using tools like:
- Postman
- Insomnia
- cURL
- Laravel Telescope (if enabled)

## Support

For technical support or questions about the API, please contact the development team.
