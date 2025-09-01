# Kanma E-commerce Platform

A comprehensive e-commerce platform built with Laravel framework, featuring multi-vendor support, delivery management, subscription plans, and integrated payment processing.

## 🚀 Features

- **Multi-vendor Marketplace** - Shop management and vendor onboarding
- **Order Management** - Complete order lifecycle with status tracking
- **Payment Integration** - Razorpay payment gateway with wallet system
- **Push Notifications** - Firebase Cloud Messaging for real-time updates
- **SMS Integration** - MSG91 for OTP and order notifications
- **Delivery Management** - Delivery boy assignment and tracking
- **Subscription Plans** - Monthly and yearly subscription options
- **Coupon System** - Discount codes and promotional offers
- **Refund System** - Automated refund processing
- **Chat System** - Real-time customer support
- **Admin Dashboard** - Comprehensive admin panel with analytics

## 🛠️ Technology Stack

### Backend
- **Laravel 11.31** - PHP web application framework
- **PHP 8.2+** - Server-side programming language
- **Laravel Sanctum 4.0** - API authentication
- **MySQL/SQLite** - Database management

### Frontend
- **Blade Templates** - Laravel's templating engine
- **Tailwind CSS 3.4.13** - Utility-first CSS framework
- **Vite 6.0.11** - Build tool and development server
- **Axios 1.7.4** - HTTP client for API requests

### Third-Party Integrations
- **Razorpay** - Payment gateway
- **Firebase Cloud Messaging** - Push notifications
- **MSG91** - SMS gateway
- **Google Maps API** - Location services

## 📋 Prerequisites

Before setting up the project, ensure you have the following installed:

- **PHP 8.2 or higher**
- **Composer** (PHP package manager)
- **Node.js 18+** and **npm**
- **MySQL** or **SQLite** database
- **Git**

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd kanma
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the environment file and configure your settings:

```bash
cp .env.example .env
```

Edit the `.env` file with your configuration:

```env
# Application
APP_NAME=Kanma
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# For MySQL (optional)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=kanma
# DB_USERNAME=root
# DB_PASSWORD=

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Payment Gateway (Razorpay)
RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret

# SMS Gateway (MSG91)
MSG91_AUTH_KEY=your_msg91_auth_key
RESET_PASSWORD_TEMPLATE_ID=your_template_id
REGISTRATION_TEMPLATE_ID=your_template_id
MSG91_ORDER_STATUS_FLOW_ID=your_flow_id
MSG91_TEST_MODE=true

# Firebase Configuration
FIREBASE_PROJECT_ID=your_project_id
FIREBASE_SERVICE_ACCOUNT_JSON={"type":"service_account",...}

# Google Maps API
GOOGLE_MAPS_API_KEY=your_google_maps_api_key

# Queue Configuration
QUEUE_CONNECTION=database
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Database Setup

#### Option A: SQLite (Default)
```bash
# Create SQLite database file
touch database/database.sqlite
```

#### Option B: MySQL
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE kanma CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 7. Run Database Migrations

```bash
php artisan migrate
```

### 8. Seed Database (Optional)

```bash
php artisan db:seed
```

### 9. Build Frontend Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 10. Set Up Storage Links

```bash
php artisan storage:link
```

## 🔧 Development Setup

### Start Development Server

```bash
# Using Laravel's built-in server
php artisan serve


### File Permissions

Ensure proper permissions for storage and cache directories:

```bash
chmod -R 775 storage bootstrap/cache
```

## 📱 API Documentation

The project includes comprehensive API documentation:

- **API Documentation**: `API_DOCS.txt`
- **Postman Collections**: `postman/` directory
- **Feature Documentation**: Various `.md` files in root directory

### Key API Endpoints

- **Authentication**: `/api/auth/*`
- **Products**: `/api/products/*`
- **Orders**: `/api/orders/*`
- **Shops**: `/api/shops/*`
- **Users**: `/api/users/*`
- **Notifications**: `/api/notifications/*`

## 🔐 Third-Party Services Setup

### 1. Razorpay Payment Gateway

1. Sign up at [Razorpay](https://razorpay.com)
2. Get your API keys from the dashboard
3. Add keys to `.env` file:
   ```env
   RAZORPAY_KEY=your_razorpay_key
   RAZORPAY_SECRET=your_razorpay_secret
   ```

### 2. Firebase Cloud Messaging

1. Create a Firebase project at [Firebase Console](https://console.firebase.google.com)
2. Download service account JSON file
3. Add configuration to `.env`:
   ```env
   FIREBASE_PROJECT_ID=your_project_id
   FIREBASE_SERVICE_ACCOUNT_JSON={"type":"service_account",...}
   ```

### 3. MSG91 SMS Gateway

1. Sign up at [MSG91](https://msg91.com)
2. Get your authentication key
3. Configure templates for OTP and notifications
4. Add configuration to `.env`:
   ```env
   MSG91_AUTH_KEY=your_msg91_auth_key
   RESET_PASSWORD_TEMPLATE_ID=your_template_id
   REGISTRATION_TEMPLATE_ID=your_template_id
   ```

### 4. Google Maps API

1. Get API key from [Google Cloud Console](https://console.cloud.google.com)
2. Enable Maps JavaScript API and Geocoding API
3. Add key to `.env`:
   ```env
   GOOGLE_MAPS_API_KEY=your_google_maps_api_key
   ```

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter=OrderTest
```

## 📦 Production Deployment

### 1. Environment Configuration

```bash
# Set production environment
APP_ENV=production
APP_DEBUG=false

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Database Migration

```bash
php artisan migrate --force
```

### 3. Build Assets

```bash
npm run build
```

### 4. Queue Worker Setup

For production, set up a supervisor to manage queue workers:

```bash
# Install supervisor
sudo apt-get install supervisor

# Configure queue worker
sudo nano /etc/supervisor/conf.d/kanma-worker.conf
```

## 📚 Additional Documentation

- **Technical Documentation**: `TECHNICAL_DOCUMENTATION.md`
- **API Documentation**: `API_DOCS.txt`
- **Feature Guides**: Various `.md` files in root directory
- **Postman Collections**: `postman/` directory

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:

- Check the documentation files in the root directory
- Review the API documentation
- Check the Postman collections for API examples
- Open an issue on the repository

## 🔄 Updates

To update the project:

```bash
# Pull latest changes
git pull origin main

# Update dependencies
composer install
npm install

# Run migrations
php artisan migrate

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

**Happy coding! 🚀**
