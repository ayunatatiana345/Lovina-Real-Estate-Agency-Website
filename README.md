# PT Lovina North Bali Real Estate Agency Website
This project is a web application developed for PT Lovina North Bali Real Estate Agency. The website provides company information, property listings, property details, search and filter features, an inquiry form for customers, and an admin dashboard for website management.

## Team Members

1. **A A Ngurah Aragon Udayana E2400070**  
   Module: Property Catalog & Media Processing

2. **Putu Ayu Tatiana Putri E2400090**  
   Module: Discovery Engine & Customer Relations

3. **Ni Putu Tara Lunisia Putri Mardika E2400088**  
   Module: Company Website & Content Management

## Technologies
- Laravel
- PHP
- MySQL
- Blade
- HTML
- CSS
- Vanilla JavaScript

## Requirements
- PHP
- Composer
- MySQL (via Laragon or XAMPP)
- Node.js and npm
- Laragon

## Installation

Follow these steps to set up and run the project locally:

1. Clone or download the project folder to your local machine.
2. Open the project folder in your terminal or code editor.
3. Install PHP dependencies:
   ```bash
   composer install
   ```
4. Install Node.js dependencies:
   ```bash
   npm install
   ```
5. Create the environment file:
   - Copy `.env.example` and rename it to `.env` (or run `cp .env.example .env` in Git Bash/Terminal).
6. Configure your database settings inside `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=lovina_realestate
   DB_USERNAME=root
   DB_PASSWORD=
   ```
7. Generate the Laravel application key:
   ```bash
   php artisan key:generate
   ```
8. Build frontend assets:
   ```bash
   npm run build
   ```

## Database Setup
1. Open your local MySQL database manager (Laragon / phpMyAdmin).
2. Create a new database named `lovina_realestate`.
3. Run the migrations and database seeders to populate initial data:
   ```bash
   php artisan migrate:fresh --seed
   ```

## Running the Project
1. Start the Laravel development server:
   ```bash
   php artisan serve
   ```
2. Open your web browser and navigate to:
   `http://127.0.0.1:8000`

If you are using Laragon, you can also place the project folder inside Laragon's `www` directory and access it through your local Laragon URL.
