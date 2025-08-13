<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

# Service Booking System

A Laravel-based application for managing service bookings with support for multiple service providers, real-time availability, and automated notifications.

## Features

- **User Management**
  - Multi-role support (Admin, Provider, Customer)
  - Role-based access control
  - User timezone support

- **Service Management**
  - Service creation and management
  - Duration and pricing configuration
  - Service categories
  - Publishing control

- **Booking System**
  - Real-time availability checking
  - Automated slot blocking
  - Double-booking prevention
  - Status management (pending, confirmed, cancelled, completed)
  - Email notifications

- **Administrative Features**
  - Comprehensive reporting
  - Provider management
  - Booking analytics

## Requirements

- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js & NPM
- Docker (optional)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/service-booking-system.git
cd service-booking-system
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Update database configuration in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run migrations:
```bash
php artisan migrate --seed
```

## Docker Setup

1. Build and start containers:
```bash
docker-compose up -d
```

2. Install dependencies:
```bash
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
```

3. Run migrations:
```bash
docker-compose exec app php artisan migrate --seed
```

## Testing

Run the test suite:
```bash
php artisan test
```

With coverage report:
```bash
php artisan test --coverage
```

## Scheduled Tasks

The application includes scheduled tasks for:
- Cleaning expired bookings
- Sending reminder notifications
- Generating reports

Configure your cron job or Windows Task Scheduler:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## API Documentation

API documentation is available at `/api/documentation` when running the application.

To regenerate API documentation:
```bash
php artisan l5-swagger:generate
```

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
