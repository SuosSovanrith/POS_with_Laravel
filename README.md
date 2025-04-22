<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# POS Project

This is a Point of Sale (POS) system built using the Laravel framework. The project is designed to provide a robust and scalable solution for managing sales, inventory, and other business operations.

## Features

- Built with Laravel 11, leveraging its expressive and elegant syntax.
- Integration with third-party libraries for enhanced functionality:
  - **[Maatwebsite Excel](https://docs.laravel-excel.com/3.1/)** for Excel file handling.
  - **[Picqer Barcode Generator](https://github.com/picqer/php-barcode-generator)** for generating barcodes.
  - **[Laravel Sanctum](https://laravel.com/docs/sanctum)** for API authentication.
- Includes debugging tools like **[Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar)**.
- Unit testing support with **[PHPUnit](https://phpunit.de/)**.
- Real-time charting using **Chart.js**.

## Requirements

- **PHP**: ^8.2
- **Composer**: Dependency management
- **jQuery & Bootstrap**: For frontend

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/SuosSovanrith/POS_with_Laravel
   cd POS_with_Laravel
   ```
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```
3. Set up the environment file:
   ```bash
   cp .env.example .env
   ```
4. Run migrations:
   ```bash
   php artisan migrate
   ```

## Usage

1. Start the development server:
   ```bash
   php artisan serve
   ```
2. Access the application in your browser at `http://localhost:8000`.

## License

This project is licensed under the MIT License. See the LICENSE file for details.