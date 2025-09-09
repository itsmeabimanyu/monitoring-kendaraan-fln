# Monitoring Kendaraan
This project is an **enhancement** of the original repository [here](https://github.com/gilangmaulana1405/monitoring-kendaraan-fln), now improved with **real-time monitoring** using **Laravel Reverb**.

## Installation
### 1. Configure Environment

```bash
php composer.phar install
cp .env.example .env
```

### 2. Generate Application Key

```bash
php artisan key:generate
```

### 3. Set Folder Permissions

> [!NOTE]
> Allows Laravel to write to the storage folder by setting proper ownership and permissions.
```bash
sudo chown -R www-data:www-data /var/www/site-name/storage
sudo chmod -R 775 /var/www/site-name/storage
```

### 4. Install Laravel Reverb

```bash
php composer.phar require laravel/reverb
php artisan reverb:install
```
### 5. Edit the `.env` File

```bash
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST="127.0.0.1"
REVERB_PORT=6001
REVERB_SCHEME=http
```

### 6. Run Database Migrations & Seed Data

```bash
php artisan migrate:fresh --seed
```

### 7. Move the `mobil` Folder

```bash
sudo mv /var/www/site-site/storage/app/public/img/mobil /var/www/site-site/storage/app/public/mobil
```

### 8. Create a Symbolic Link to Make Storage Publicly Accessible

```bash
php artisan storage:link
```

### 9. Serve the Application (Development Mode)
Use the following commands to start the application in development mode:

```bash
php artisan serve
php artisan reverb:start
```

### 10. (Optional) Clear Cached Configuration

If you've made changes to .env or config files, run:
```bash
php artisan config:clear
php artisan cache:clear
```