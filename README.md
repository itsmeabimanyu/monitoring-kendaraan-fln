# Monitoring Kendaraan

This project is an **enhancement** of the original repository [here](https://github.com/gilangmaulana1405/monitoring-kendaraan-fln), now improved with **real-time monitoring** using **Laravel Reverb**.

## Installation

### 1. Clone the Repository
### 2. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Install Laravel Reverb

```bash
composer require laravel/reverb
php artisan reverb:install
```

### 4. Run Migrations and Seed the Database

```bash
php artisan migrate:fresh --seed
```

### 5. Running the Application
Use the following commands to start the application in development mode:

```bash
php artisan serve
php artisan queue:work
php artisan reverb:start
```