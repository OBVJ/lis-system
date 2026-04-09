# LIS Laboratory Information System

A Laravel-based laboratory information system for managing patients, sample collection, result entry, and report generation.

## Features

- Patient registration and lab request management
- Sample collection workflow with automatic status updates
- Result entry for numeric and qualitative values
- Arabic PDF report generation with RTL support
- Role-based access control for Admin, Doctor, Receptionist, and Technician
- Inventory and material usage tracking
- Built-in demo users for quick testing

## Requirements

- PHP 8.1+ with required extensions
- MySQL / MariaDB
- Composer
- Node.js and npm (for frontend assets)
- Apache / Nginx or built-in PHP server

## Installation

1. Clone the repository:

```bash
git clone https://github.com/OBVJ/lis-system.git
cd lis-system
```

2. Install PHP dependencies:

```bash
composer install
```

3. Install frontend dependencies:

```bash
npm install
npm run build
```

4. Copy the environment file and configure:

```bash
cp .env.example .env
php artisan key:generate
```

5. Configure database connection in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lis_system_db
DB_USERNAME=root
DB_PASSWORD=
```

6. Run migrations and seeders:

```bash
php artisan migrate --seed
```

## Running the Application

Run the local development server:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Open the application in your browser at:

```text
http://127.0.0.1:8000
```

## Default Users

The seeded users include:

- Admin: `admin@lis.com` / `123456`
- Doctor: `doctor@lis.com` / `123456`
- Receptionist: `receptionist@lis.com` / `123456`
- Technician: `technician@lis.com` / `123456`

## Important Routes

- Dashboard: `/dashboard`
- Queue board: `/queue`
- Patient management: `/patients`
- Requests: `/requests`
- Lab workbench: `/results`
- Samples: `/samples`
- Reports: `/reports/operational`, `/reports/financial`, `/reports/medical`

## Notes

- Sample collection now updates request status automatically.
- Result entry accepts both numeric values and text values like `Positive` / `Negative`.
- PDF reports support Arabic rendering and RTL layout.
- Remove temporary or test routes before deploying to production.

## Troubleshooting

- If PDF Arabic text appears broken, ensure the report template uses RTL classes and the font is available.
- If login fails, verify the database seeder ran and user roles were created.
- For permission issues, check `config/permission.php` and role assignments.

## License

This project is licensed under the MIT License.
