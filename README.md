# Enegence — Technical Test (Laravel 13)

## Requirements

- PHP 8.3 or higher, with the `pdo_mysql` extension
- Composer
- Node.js 22.12 or higher (minimum required by Vite 8; latest LTS recommended), with npm
- Docker and Docker Compose (to run MySQL locally)

## Installation from scratch

```bash
# 1. Clone the repository
git clone git@github.com:robertodelgadogonzalez/enegence.git
cd enegence

# 2. Install PHP dependencies
composer install

# 3. Copy the environment file (already has the MySQL config set)
cp .env.example .env
php artisan key:generate

# 4. Start MySQL in Docker (port 3308, to avoid clashing with other local MySQL instances)
docker compose up -d mysql

# 5. Run the migrations
php artisan migrate

# 6. Install frontend dependencies and build assets
npm install
npm run build

# 7. Start the development server
php artisan serve
```

Open [http://localhost:8000](http://localhost:8000).
