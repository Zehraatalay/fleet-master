Fleet-Master

A full-stack fleet management application built with Laravel 12, Nuxt 4, Vue 3, and PostgreSQL.

Fleet-Master provides a centralized dashboard for managing vehicles, drivers, and trips, with RESTful APIs and token-based authentication.

Features

* Dashboard: Fleet statistics and recent trip activity.
* Vehicle Management: Create, view, update, and delete vehicles.
* Driver Management: Manage driver information and availability.
* Trip Management: Track trips with status filtering and pagination.
* Authentication: Secure API access using Laravel Sanctum.
* Responsive UI: Dark-themed interface built with Tailwind CSS.

Tech Stack

Backend	Frontend	Database
Laravel 12	Nuxt 4	PostgreSQL
PHP 8.2+	Vue 3
Laravel Sanctum	Tailwind CSS

Screenshots

Dashboard

Vehicles

Drivers

Trips

Getting Started

Prerequisites

* PHP 8.2+
* Composer
* Node.js and npm
* PostgreSQL

1. Clone the Repository

git clone https://github.com/Zehraatalay/fleet-master.git
cd fleet-master

2. Backend Setup

cd backend
composer install
cp .env.example .env
php artisan key:generate

Configure your PostgreSQL credentials in backend/.env, then run:

php artisan migrate
php artisan db:seed --class=DemoSeeder
php artisan serve

The backend runs at http://127.0.0.1:8000.

3. Create a Login User

In a separate terminal, from the backend directory:

php artisan tinker

Run:

\App\Models\User::factory()->create([
    'name' => 'Demo User',
    'email' => 'demo@example.com',
    'password' => bcrypt('your-local-password'),
]);

Use these credentials to sign in. Replace the example password with your own local password.

4. Frontend Setup

Open another terminal:

cd frontend
npm ci
cp .env.example .env
npm run dev

The frontend runs at http://localhost:3000.

Testing

Run backend tests:

cd backend
php artisan test

Build the frontend:

cd frontend
npm run build

Project Structure

fleet-master/
├── backend/          # Laravel REST API
├── frontend/         # Nuxt application
├── docs/screenshots/ # Application screenshots
└── README.md