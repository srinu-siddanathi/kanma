# Admin Shop Edit Feature with Latitude and Longitude

## Overview
This feature adds the ability for administrators to edit shop details including latitude and longitude coordinates manually through the admin panel.

## Features Added

### 1. Edit Functionality
- **New Routes**: Added edit and update routes for shops in admin panel
- **Controller Methods**: Added `edit()` and `update()` methods to `Admin\ShopController`
- **Edit View**: Created comprehensive edit form with all shop fields

### 2. Latitude and Longitude Fields
- **Input Fields**: Added latitude and longitude input fields with validation
- **Validation Rules**: 
  - Latitude: numeric, between -90 and 90
  - Longitude: numeric, between -180 and 180
- **Placeholder Values**: Example coordinates provided (12.9716, 77.5946 for Bangalore)

### 3. Enhanced Shop Information
- **Working Hours**: Full working hours management for all days of the week
- **Status Controls**: Active and Verified status toggles
- **Basic Information**: Name, email, phone, address, description

### 4. User Interface Improvements
- **Edit Button**: Added "Edit Shop" button to shop show page
- **Location Display**: Shows current latitude/longitude on shop details page
- **Google Maps Link**: Direct link to view shop location on Google Maps
- **Working Hours Display**: Shows formatted working hours on shop details page

### 5. JavaScript Enhancements
- **Coordinate Validation**: Real-time validation of latitude/longitude inputs
- **Map Picker**: Button to open Google Maps for coordinate selection
- **Help Text**: Instructions for getting coordinates from Google Maps

## Files Modified

### Controllers
- `app/Http/Controllers/Admin/ShopController.php`
  - Added `edit()` method
  - Added `update()` method with comprehensive validation

### Routes
- `routes/web.php`
  - Added `GET /admin/shops/{shop}/edit` route
  - Added `PUT /admin/shops/{shop}` route

### Views
- `resources/views/admin/shops/edit.blade.php` (New)
  - Complete edit form with all shop fields
  - Latitude and longitude input fields
  - Working hours management
  - Status controls
  - JavaScript for validation and map picker

- `resources/views/admin/shops/show.blade.php` (Modified)
  - Added "Edit Shop" button
  - Added latitude/longitude display
  - Added Google Maps link
  - Added working hours display

## Usage Instructions

### For Administrators

1. **Accessing Edit Page**:
   - Go to Admin Panel → Shops
   - Click on any shop to view details
   - Click "Edit Shop" button

2. **Setting Coordinates**:
   - **Manual Entry**: Type latitude and longitude directly
   - **Map Picker**: Click "Pick from Map" button to open Google Maps
   - **Google Maps Method**: 
     - Right-click on desired location
     - Select "What's here?"
     - Copy coordinates from the info panel

3. **Working Hours**:
   - Set open and close times for each day
   - Leave empty for closed days
   - Times are in 24-hour format

4. **Status Management**:
   - Toggle "Active" to enable/disable shop
   - Toggle "Verified" to mark shop as verified

### Validation Rules

- **Name**: Required, max 255 characters
- **Email**: Required, valid email, unique
- **Phone**: Required, max 20 characters
- **Address**: Required
- **Latitude**: Optional, numeric, -90 to 90
- **Longitude**: Optional, numeric, -180 to 180
- **Working Hours**: Optional, valid time format

## Benefits

1. **Location Accuracy**: Admins can manually set precise coordinates for shops
2. **Better User Experience**: Accurate coordinates improve nearby shop functionality
3. **Flexible Management**: Full control over shop details and status
4. **Data Integrity**: Comprehensive validation ensures data quality
5. **User-Friendly**: Map picker makes coordinate selection easy

## Technical Details

### Database Fields Used
- `latitude` (decimal 10,8)
- `longitude` (decimal 11,8)
- `working_hours` (json)
- `is_active` (boolean)
- `is_verified` (boolean)
- All other shop fields

### JavaScript Features
- Real-time coordinate validation
- Google Maps integration
- Form validation feedback
- User-friendly help text

### Security
- Admin middleware protection
- Input validation and sanitization
- CSRF protection
- Proper authorization checks

## Future Enhancements

1. **Interactive Map**: Embed Google Maps directly in the edit form
2. **Address Geocoding**: Auto-fill coordinates from address
3. **Bulk Operations**: Edit multiple shops at once
4. **Audit Trail**: Track changes to shop details
5. **Image Upload**: Allow updating shop images 