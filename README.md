# Monitoring Kendaraan

This project is an **enhancement** of the original repository [here](https://github.com/gilangmaulana1405/monitoring-kendaraan-fln), now improved with **real-time monitoring** using **Laravel Reverb**.

## Installation

### 1. Clone the Repository
### 2. Configure Environment

```bash
php composer.phar install
cp .env.example .env
php artisan key:generate
```

### 3. Install Laravel Reverb

```bash
php composer.phar require laravel/reverb
php artisan reverb:install
```

### 4. Run Migrations and Seed the Database

```bash
php artisan migrate:fresh --seed
```

> [!NOTE]
> Allows Laravel to write to the storage folder by setting proper ownership and permissions.
```bash
sudo chown -R www-data:www-data /var/www/monitoring-kendaraan/storage
sudo chmod -R 775 /var/www/monitoring-kendaraan/storage
```
### 5. Running the Application
Use the following commands to start the application in development mode:

```bash
php artisan serve
php artisan reverb:start
```