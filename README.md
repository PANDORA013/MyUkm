# MyUKM - Laravel Application

Aplikasi manajemen UKM (Unit Kegiatan Mahasiswa) yang dibangun dengan Laravel.

## Requirements

- **PHP 8.2+**
- **Composer**
- **MySQL/MariaDB**
- **Node.js & NPM** (untuk asset compilation)

## Local Development Setup

1. **Clone repository:**
   ```bash
   git clone https://github.com/PANDORA013/MyUkm.git
   cd MyUkm
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup:**
   - Buat database MySQL dengan nama `myukm`
   - Update konfigurasi database di file `.env`
   - Jalankan migrasi dan seeder:
   ```bash
   php artisan migrate --seed
   ```

5. **Start development server:**
   ```bash
   php artisan serve
   ```

   Aplikasi akan berjalan di `http://localhost:8000`

## Testing

Jalankan test suite:
```bash
php artisan test
```

## Features

- User authentication dan authorization
- Manajemen grup UKM
- Real-time chat menggunakan Pusher
- Dashboard admin dan user
- Responsive design
