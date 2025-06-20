# Shop Owner Product Image Search Feature

## Overview
This feature allows shop owners to easily search for and select existing product images when adding or editing products. This helps reduce duplicate image uploads and makes it easier to reuse images across similar products.

## Features

### 1. Image Search Functionality
- **Search by Product Name**: Shop owners can search for existing images by entering product names
- **Flexible Matching**: Searches for both exact phrases and individual words (e.g., "fresh eggs" will find products with "eggs")
- **Auto-search**: Automatically searches for images when the product name field is updated
- **Real-time Results**: Shows matching images in a grid layout with previews

### 2. Image Selection
- **Multiple Selection**: Shop owners can select multiple images for a single product
- **Visual Feedback**: Selected images are highlighted and shown in a separate section
- **Easy Removal**: Selected images can be removed with a single click

### 3. Image Sources
The search functionality looks for images from:
- **Existing Products**: Images from other products in the same shop
- **Upload Directory**: Images stored in the `public/uploads/products` directory
- **Product Images Table**: Images stored in the `product_images` table

## Implementation Details

### Backend Changes

#### 1. ProductController Updates
- Added `searchImages()` method to handle AJAX image search requests
- Updated `store()` and `update()` methods to handle selected images
- Added validation for `selected_images` array

#### 2. Routes
- Added new route: `POST /shop-owner/products/search-images`

#### 3. Database
- Uses existing `product_images` table
- Supports multiple images per product with sort order

### Frontend Changes

#### 1. Create Product View (`create.blade.php`)
- Added image search section with search input and button
- Added search results grid
- Added selected images display
- Added JavaScript for interactive functionality

#### 2. Edit Product View (`edit.blade.php`)
- Same functionality as create view
- Pre-loads existing product images
- Allows modification of existing image selections

#### 3. JavaScript Features
- AJAX image search with debouncing
- Dynamic image selection and removal
- Form integration with hidden inputs
- Visual feedback and hover effects

## Usage Instructions

### For Shop Owners

1. **Adding a New Product**:
   - Fill in the product name
   - The system will automatically search for matching images
   - Click on images to select them
   - Selected images will appear in the "Selected Images" section
   - Complete the form and submit

2. **Editing an Existing Product**:
   - Current images are displayed at the top
   - Search for additional images using the search box
   - Select new images or remove existing ones
   - Save changes

3. **Image Search Tips**:
   - Enter at least 2 characters to search
   - Search is based on product names, not image filenames
   - Results include images from similar products
   - **Flexible matching**: Searching for "fresh eggs" will also find products with just "eggs"
   - Individual words in your search are matched separately for better results

### Technical Notes

- **File Size Limit**: 2MB per image
- **Supported Formats**: JPG, PNG, GIF
- **Storage Location**: `public/uploads/products/`
- **CSRF Protection**: All AJAX requests include CSRF tokens
- **Responsive Design**: Works on desktop and mobile devices

## Security Considerations

- Only shop owners can access their own shop's images
- Images are validated before storage
- CSRF protection is enabled for all requests
- File type and size validation is enforced

## Technical Fixes

### Slug Uniqueness
- **Issue**: Duplicate slug errors when creating products with similar names
- **Solution**: Implemented `generateUniqueSlug()` method that appends numbers to ensure uniqueness
- **Example**: "Fresh Eggs" → "fresh-eggs", "Fresh Eggs" → "fresh-eggs-1", "Fresh Eggs" → "fresh-eggs-2"
- **Applied to**: Both Shop Owner and Admin ProductControllers

## Future Enhancements

Potential improvements for future versions:
- Image tagging system for better search
- Bulk image upload and selection
- Image cropping and editing tools
- Image optimization and compression
- Advanced search filters (by category, date, etc.) 