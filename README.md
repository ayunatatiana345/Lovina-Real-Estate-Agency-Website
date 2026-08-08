# PT Lovina North Bali Real Estate Agency — Full-Stack Web Application

Full-stack web application built with **Laravel (PHP, Blade Template, Vanilla CSS/JS, MySQL)** for **PT Lovina North Bali Real Estate Agency**, featuring both a responsive **Public Website** and an **Admin Dashboard**.

---

## Key Features & Architecture

- **Stack**: Laravel 11/12 (MVC), PHP 8.3+, Blade Templates, Vanilla CSS, Vanilla JavaScript, Chart.js, SortableJS, MySQL Database.
- **Strict No-Animation Design System**: Fast, instant UI state transitions tailored for users aged 30+.
- **Design System Tokens**:
  - Primary Navy: `#1E3A8A`
  - Medium Blue: `#5A86C5`
  - Light Blue: `#D6E6F7`
  - Accent Lavender: `#F4F1FA`
  - Secondary Gold: `#C7A86D`
  - Typography: Exclusively **Poppins** font family across all headings, body, and labels.
- **Public Website**:
  - Sticky Navbar & Brand Header
  - Homepage with Hero Search, Featured Properties (max 3), Latest Properties, Property Categories, Popular Locations, Why Choose Us, Company Stats, Contact CTA.
  - Properties Listing with Filters (Keyword, Type, Location, Price Range, Date Uploaded) & mandatory Empty State with shortcut suggestion chips.
  - Property Detail Page with Specs grid, description, location overview, contact form, and **JSON-LD Schema `RealEstateListing`**.
  - Locations Page with stats, popular location cards, and "Explore North Bali" map layout.
  - About Us Page with Story, Vision, Mission points, Benefits, and Stats.
  - Contact Us Page with 4 Get In Touch cards (Office Address, Phone, Email, Business Hours), Office Photo, Google Maps embed, Social Links, and Inquiry Form.
  - Modal confirmation popup "Message Sent Successfully!" upon inquiry submission.
- **Admin Dashboard** (`/admin/login`):
  - Native session-based authentication (Admin User: `admin@lovinanorthbali.com` / `password`).
  - Overview Analytics: Total properties (published/draft), Total inquiries (new/read/replied), Line chart (views vs visitors via Chart.js), Donut charts, Recent inquiries table, Top properties, Quick actions.
  - Website CMS (Homepage & About Us tabs) with dual-column layout and real-time live preview panel.
  - Company Settings: Company info, branding logos, contact info, business hours, social media links, Google Maps embeds.
  - Properties Management: CRUD with tabs (General Info, Specs, Gallery), featured toggles, categories management.
  - Locations Management: Table view, popular star toggle, slide-in "Add New Location" panel from right with character counter.
  - Inquiries Management: Detail view, status updates (New, In Progress, Responded, Closed), internal admin notes.

---

## Installation & Local Development Guide

### 1. Requirements
- PHP 8.2 or higher
- Composer 2.x
- MySQL Server (e.g. via Laragon, XAMPP, or Native MySQL)
- Node.js & npm

### 2. Setup Environment
```bash
# Clone or navigate to the project directory
cd PT-Lovina-North-Bali-Real-Estate

# Install PHP dependencies
composer install

# Install Node dependencies & build assets
npm install
npm run build
```

### 3. Environment Configuration
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```
Ensure your database credentials in `.env` match your local MySQL configuration:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lovina_realestate
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database Setup & Seeding
Run migrations and populate seed data:
```bash
php artisan migrate:fresh --seed
```

This will automatically create:
- Admin Account: `admin@lovinanorthbali.com` / Password: `password`
- 6 Categories (Villa, House, Land, Hotel, Restaurant, Commercial)
- 6 North Bali Locations (Lovina, Temukus, Singaraja, Seririt, Banjar, Gerokgak)
- Realistic Property Listings & Inquiries
- Company Settings & CMS Default Content

### 5. Storage Symbolic Link
```bash
php artisan storage:link
```

### 6. Run Local Server
```bash
php artisan serve
```
Access the application at:
- **Public Website**: [http://127.0.0.1:8000](http://127.0.0.1:8000)
- **Admin Dashboard**: [http://127.0.0.1:8000/admin/login](http://127.0.0.1:8000/admin/login)
