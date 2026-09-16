# Spencer

A web application designed for tracking and managing student personal finances and shared group expenses. The project provides an overview of recurring payments, budget allocations, and event-based costs within student groups.

Developed as a collaborative academic project.

---

## Features

- Transaction logging for income, expenditures, and event-linked payments
- Shared group payment management with automated member notifications
- User authentication with email-based password resets
- Multilingual localization support (Czech, English, German)
- RESTful API facilitating frontend-to-backend communication

---

## Tech Stack

- **Backend:** PHP 8.2+, Laravel
- **Frontend:** TypeScript, Bootstrap, Axios
- **Database:** MySQL / MariaDB (SQLite compatible for local testing)
- **Testing:** PHPUnit

---

## Prerequisites

- PHP >= 8.2 (required extensions: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`)
- Composer
- Node.js (v18+) and npm
- Relational database server (MySQL / MariaDB)

---

## Installation and Setup

### 1. Clone the repository
```bash
git clone [https://github.com/spencerteaminfo/Spencer.git](https://github.com/spencerteaminfo/Spencer.git)
cd Spencer
```

### 2. Install backend dependencies
```bash
composer install
```

### 3. Install frontend dependencies
```bash
npm install
```

### 4. Configure environment variables
Copy the example environment configuration file and generate the application key:
```bash
cp .env.example .env
php artisan key:generate
```

Configure your local database credentials and mail server in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spencer
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

### 5. Run database migrations
Execute migrations to initialize table schemas and relational structures:
```bash
php artisan migrate
```

### 6. Build assets and launch services

In your primary terminal, start asset compilation and watching:
```bash
npm run dev
```

In a separate terminal, launch the local PHP development server:
```bash
php artisan serve
```

The application will be accessible at `http://127.0.0.1:8000`.

---

## Running Tests

Execute the automated test suite via PHPUnit:
```bash
php artisan test
```

Or run the test binary directly:
```bash
./vendor/bin/phpunit
```

---

## Directory Structure

```text
├── app/             # Core business logic, controllers, and Eloquent models
├── bootstrap/       # Framework initialization and autoload scripts
├── config/          # Application configuration files
├── database/        # Database migrations, factories, and seeders
├── lang/            # Localization dictionary files (cs, en, de)
├── public/          # Web server document root and compiled assets
├── resources/       # Blade templates, raw TypeScript files, and stylesheets
├── routes/          # Route declarations (web.php, api.php)
├── tests/           # Feature and unit test suites
├── types/           # Global TypeScript type definitions
└── composer.json    # PHP dependency manifest
```

---

## License

This software is proprietary and source-available. The code is provided solely for personal study, review, and academic testing. Any redistribution, commercial exploitation, modification, or sublicensing without explicit prior written permission from the authors is strictly prohibited. For complete terms, see the [LICENCE](LICENCE) file.
