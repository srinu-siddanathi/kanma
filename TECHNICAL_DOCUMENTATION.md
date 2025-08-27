# Technical Documentation - Kanma E-commerce Platform

## Project Overview
Kanma is a comprehensive e-commerce platform built with Laravel framework, featuring multi-vendor support, delivery management, subscription plans, and integrated payment processing.

---

## 🛠️ Technology Stack

### Backend Framework
- **Laravel 11.31** - PHP web application framework
- **PHP 8.2+** - Server-side programming language
- **Laravel Sanctum 4.0** - API authentication
- **Laravel Tinker 2.9** - REPL for Laravel

### Frontend Technologies
- **Blade Templates** - Laravel's templating engine
- **Tailwind CSS 3.4.13** - Utility-first CSS framework
- **Vite 6.0.11** - Build tool and development server
- **Axios 1.7.4** - HTTP client for API requests
- **jQuery 1.11.0** - JavaScript library (legacy support)

### Database
- **MySQL/MariaDB** - Primary database (configurable)
- **SQLite** - Development database (default)
- **PostgreSQL** - Alternative database option
- **Redis** - Caching and session storage

### Development Tools
- **Laravel Pint 1.13** - PHP code style fixer
- **Laravel Sail 1.26** - Docker development environment
- **PHPUnit 11.0.1** - Testing framework
- **Faker 1.23** - Data generation for testing

---

## 🔌 Third-Party Integrations

### Payment Gateway
**Razorpay**
- **Package**: `razorpay/razorpay: ^2.9`
- **Features**: 
  - Order creation and payment processing
  - Payment verification with signature
  - Refund processing
  - Subscription payments
  - Wallet deposits
- **Configuration Keys**:
  ```env
  RAZORPAY_KEY=your_razorpay_key
  RAZORPAY_SECRET=your_razorpay_secret
  ```

### SMS Gateway
**MSG91**
- **Service**: `App\Services\Msg91Service`
- **Features**:
  - OTP sending and verification
  - Order status notifications
  - Registration confirmations
  - Password reset notifications
- **Configuration Keys**:
  ```env
  MSG91_AUTH_KEY=your_msg91_auth_key
  RESET_PASSWORD_TEMPLATE_ID=your_template_id
  REGISTRATION_TEMPLATE_ID=your_template_id
  MSG91_ORDER_STATUS_FLOW_ID=your_flow_id
  MSG91_TEST_MODE=true
  ```

### Push Notifications
**Firebase Cloud Messaging (FCM)**
- **Service**: `App\Services\FirebaseNotificationService`
- **Features**:
  - Cross-platform push notifications
  - Device token management
  - Batch notification sending
  - Topic-based notifications
- **Configuration Keys**:
  ```env
  FIREBASE_PROJECT_ID=your_project_id
  FIREBASE_SERVICE_ACCOUNT_JSON={"type":"service_account",...}
  FIREBASE_SERVICE_ACCOUNT_PATH=firebase-credentials.json
  ```

### Maps & Geocoding
**Google Maps API**
- **Service**: `App\Services\GeocodingService`
- **Features**:
  - Address geocoding
  - Pincode to coordinates conversion
  - Distance calculations
  - Location-based services
- **Configuration Keys**:
  ```env
  GOOGLE_MAPS_API_KEY=your_google_maps_api_key
  ```

### Alternative Payment Gateway
**Juspay**
- **Service**: `App\Http\Controllers\Api\PaymentController`
- **Features**:
  - Payment session initialization
  - Payment status tracking
  - Saved payment methods
- **Configuration Keys**:
  ```env
  JUSPAY_BASE_URL=https://api.juspay.in
  JUSPAY_API_KEY=your_juspay_api_key
  JUSPAY_CLIENT_ID=your_client_id
  JUSPAY_MERCHANT_ID=your_merchant_id
  ```

---

## 📱 API Architecture

### Authentication
- **Laravel Sanctum** for API token authentication
- **Bearer token** authentication for protected endpoints
- **Role-based access control** with Spatie Laravel Permission

### API Endpoints Structure
```
/api/
├── auth/           # Authentication endpoints
├── products/       # Product management
├── orders/         # Order processing
├── payments/       # Payment processing
├── notifications/  # Push notifications
├── addresses/      # Address management
├── cart/          # Shopping cart
├── checkout/      # Checkout process
├── wallet/        # Wallet management
├── subscriptions/ # Subscription plans
└── admin/         # Admin panel APIs
```

### Key API Features
- **RESTful design** with standard HTTP methods
- **JSON responses** with consistent structure
- **Input validation** using Laravel Form Requests
- **Error handling** with proper HTTP status codes
- **Rate limiting** for API protection

---

## 🗄️ Database Schema

### Core Tables
- **users** - User accounts and profiles
- **products** - Product catalog
- **orders** - Order management
- **payments** - Payment transactions
- **addresses** - User addresses
- **categories** - Product categories
- **shops** - Vendor/shop information
- **branches** - Branch locations
- **subscriptions** - Subscription plans
- **wallet_transactions** - Wallet operations

### Key Relationships
- Users can have multiple addresses, orders, and wallet transactions
- Products belong to categories and shops
- Orders contain multiple order items
- Payments are linked to orders and users

---

## 🔧 Configuration & Environment

### Required Environment Variables
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kanma_db
DB_USERNAME=root
DB_PASSWORD=

# Application
APP_NAME="Kanma"
APP_ENV=production
APP_KEY=base64:your_app_key
APP_DEBUG=false
APP_URL=https://your-domain.com

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Cache & Sessions
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Queue
QUEUE_CONNECTION=sync
```

### Service-Specific Configuration
```env
# Razorpay
RAZORPAY_KEY=rzp_test_your_key
RAZORPAY_SECRET=your_secret_key

# MSG91
MSG91_AUTH_KEY=your_auth_key
MSG91_TEST_MODE=true

# Firebase
FIREBASE_PROJECT_ID=your_project_id
FIREBASE_SERVICE_ACCOUNT_JSON={"type":"service_account",...}

# Google Maps
GOOGLE_MAPS_API_KEY=your_maps_api_key
```

---

## 🚀 Deployment Requirements

### Server Requirements
- **PHP**: 8.2 or higher
- **Extensions**: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- **Web Server**: Apache/Nginx
- **Database**: MySQL 8.0+ / MariaDB 10.3+
- **Memory**: Minimum 512MB RAM
- **Storage**: Minimum 1GB available space

### Recommended Hosting
- **Shared Hosting**: Compatible with most shared hosting providers
- **VPS/Dedicated**: For better performance and control
- **Cloud Platforms**: AWS, DigitalOcean, Vultr, etc.

### Deployment Steps
1. **Code Deployment**: Upload Laravel files to server
2. **Dependencies**: Run `composer install --optimize-autoloader --no-dev`
3. **Environment**: Configure `.env` file with production settings
4. **Database**: Run migrations with `php artisan migrate`
5. **Storage**: Set up storage links with `php artisan storage:link`
6. **Cache**: Optimize with `php artisan config:cache`
7. **Permissions**: Set proper file permissions

---

## 🔒 Security Features

### Authentication & Authorization
- **Multi-factor authentication** support
- **Role-based access control** (Admin, Shop Owner, Customer, Delivery Boy)
- **API token authentication** with Sanctum
- **Password hashing** with bcrypt

### Data Protection
- **CSRF protection** for web forms
- **Input sanitization** and validation
- **SQL injection prevention** with Eloquent ORM
- **XSS protection** with Blade templating

### Payment Security
- **Payment signature verification** (Razorpay)
- **Encrypted sensitive data** storage
- **PCI DSS compliance** through payment gateways

---

## 📊 Monitoring & Logging

### Logging
- **Laravel Logging** with configurable channels
- **Error tracking** and debugging
- **API request/response logging**
- **Payment transaction logging**

### Performance Monitoring
- **Database query optimization**
- **Caching strategies** (Redis/File)
- **Image optimization** for product images
- **CDN support** for static assets

---

## 🔄 Maintenance & Updates

### Regular Tasks
- **Database backups** (daily/weekly)
- **Log rotation** and cleanup
- **Security updates** for dependencies
- **Performance monitoring**

### Update Procedures
1. **Backup** database and files
2. **Update** codebase
3. **Run** `composer install`
4. **Execute** database migrations
5. **Clear** application caches
6. **Test** functionality

---

## 📞 Support & Documentation

### API Documentation
- **Postman Collections** available in `/postman/` directory
- **API endpoints** documented with examples
- **Error codes** and response formats

### Additional Resources
- **Laravel Documentation**: https://laravel.com/docs
- **Razorpay API Docs**: https://razorpay.com/docs/api/
- **Firebase Documentation**: https://firebase.google.com/docs
- **MSG91 API Docs**: https://msg91.com/api-documentation

---

## 📋 License & Compliance

### Open Source Licenses
- **Laravel**: MIT License
- **Tailwind CSS**: MIT License
- **Other packages**: Various open source licenses

### Third-Party Compliance
- **Razorpay**: PCI DSS compliant
- **Firebase**: GDPR compliant
- **MSG91**: TRAI compliant (India)

---

*This documentation provides a comprehensive overview of the technical architecture and integrations used in the Kanma e-commerce platform. For specific implementation details or troubleshooting, please refer to the individual service documentation or contact the development team.*









