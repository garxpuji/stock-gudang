# Panduan Deployment Server VM (Debian 13 & Ubuntu 26)
## Sistem Informasi Inventaris Gudang ISP — PT Gayuh Media Informatika

Panduan ini ditujukan untuk deployment produksi (*production deployment*) pada Virtual Machine (VM) berbasis **Debian 13 (Trixie)** dan **Ubuntu 26 (26.04 LTS)** menggunakan arsitektur stack:
- **Web Server**: Nginx
- **PHP Process Manager**: PHP 8.4-FPM (atau PHP 8.3-FPM)
- **Database Engine**: MariaDB Server
- **Process & Scheduler**: Systemd & Linux Crontab
- **SSL / TLS**: Let's Encrypt Certbot (HTTPS)

---

## 1. Topologi & Rencana Direktori

| Komponen | Lokasi / Konfigurasi |
| :--- | :--- |
| **Direktori Proyek** | `/var/www/stock-gudang` |
| **Web Root Nginx** | `/var/www/stock-gudang/public` |
| **User & Group Web** | `www-data:www-data` |
| **Socket PHP-FPM** | `/run/php/php8.4-fpm.sock` (atau `php8.3-fpm.sock`) |
| **Port Database** | `3306` (atau `3307` jika menggunakan multi-instance) |
| **Nama Database** | `stock_gudang` |

---

## 2. Persiapan Server & Update Sistem

Login ke VM via SSH sebagai user `root` atau user dengan hak akses `sudo`:
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl wget gnupg2 ca-certificates lsb-release apt-transport-https git unzip zip ufw software-properties-common
```

---

## 3. Instalasi PHP 8.4 / 8.3 & Ekstensi yang Dibutuhkan

### Untuk Ubuntu 26:
Ubuntu biasanya telah menyediakan PHP versi modern, atau gunakan repositori PPA Ondřej Surý:
```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.4 php8.4-fpm php8.4-cli php8.4-common php8.4-mysql php8.4-mbstring \
    php8.4-xml php8.4-bcmath php8.4-curl php8.4-zip php8.4-intl php8.4-gd
```

### Untuk Debian 13 (Trixie):
Tambahkan repositori resmi Sury untuk Debian:
```bash
sudo curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg
sudo sh -c 'echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
sudo apt update
sudo apt install -y php8.4 php8.4-fpm php8.4-cli php8.4-common php8.4-mysql php8.4-mbstring \
    php8.4-xml php8.4-bcmath php8.4-curl php8.4-zip php8.4-intl php8.4-gd
```

Verifikasi versi PHP dan status layanan PHP-FPM:
```bash
php -v
sudo systemctl status php8.4-fpm --no-pager
```

---

## 4. Instalasi & Konfigurasi MariaDB Server

### Instalasi MariaDB
```bash
sudo apt install -y mariadb-server mariadb-client
sudo systemctl enable --now mariadb
```

### Amankan Instalasi MariaDB
Jalankan wizard pengamanan:
```bash
sudo mariadb-secure-installation
```
*(Ikuti instruksi: tetapkan root password, hapus anonymous user, larang remote root login, dan hapus test database).*

### Buat Database & User Aplikasi
Masuk ke prompt MariaDB:
```bash
sudo mariadb -u root -p
```

Eksekusi perintah SQL berikut untuk membuat database dan user aplikasi:
```sql
CREATE DATABASE IF NOT EXISTS stock_gudang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'gudang_user'@'localhost' IDENTIFIED BY '<your_secure_password>';
GRANT ALL PRIVILEGES ON stock_gudang.* TO 'gudang_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

> [!NOTE]
> Jika server MariaDB Anda dikonfigurasi pada port non-standar (misalnya `3307`), pastikan baris `port = 3307` ada pada `/etc/mysql/mariadb.conf.d/50-server.cnf` dan restart dengan `sudo systemctl restart mariadb`. Jika menggunakan port standar Linux `3306`, sesuaikan nilai `DB_PORT=3306` pada file `.env`.

---

## 5. Instalasi Composer & Node.js

### Instalasi Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

### Instalasi Node.js (Node 20 / 22 LTS)
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
node -v
npm -v
```

---

## 6. Instalasi Web Server Nginx

```bash
sudo apt install -y nginx
sudo systemctl enable --now nginx
```

---

## 7. Clone Repositori & Setup Aplikasi

### 1. Clone Source Code ke `/var/www/stock-gudang`
```bash
sudo git clone https://github.com/garxpuji/stock-gudang.git /var/www/stock-gudang
cd /var/www/stock-gudang
```

### 2. Atur File Konfigurasi Environment (`.env`)
```bash
sudo cp .env.example .env
sudo nano .env
```
Sesuaikan parameter berikut pada file `.env`:
```env
APP_NAME="Stock Gudang GMI"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://gudang.gayuhmedia.id  # Ganti dengan IP VM atau domain Anda

DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306                        # Sesuaikan port MariaDB Anda (3306 / 3307)
DB_DATABASE=stock_gudang
DB_USERNAME=gudang_user
DB_PASSWORD=<your_secure_password>
```

### 3. Install Dependensi PHP (Optimasi Produksi)
```bash
sudo composer install --no-dev --optimize-autoloader --no-interaction
```

### 4. Generate Application Key
```bash
sudo php artisan key:generate --force
```

### 5. Kompilasi Asset Frontend Lokal (Vite & Alpine.js)
```bash
sudo npm install
sudo npm run build
```

### 6. Jalankan Migrasi Database & Seeder
```bash
sudo php artisan migrate --force --seed
```

### 7. Buat Symbolic Link Storage Foto Bukti
```bash
sudo php artisan storage:link
```

### 8. Optimasi Cache Konfigurasi & Rute Laravel
```bash
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
```

### 9. Atur Hak Akses File & Ownership (Permissions)
Wajib menetapkan user `www-data` sebagai pemilik file agar upload dokumen, log, dan cache dapat ditulis dengan aman:
```bash
sudo chown -R www-data:www-data /var/www/stock-gudang
sudo find /var/www/stock-gudang -type f -exec chmod 644 {} \;
sudo find /var/www/stock-gudang -type d -exec chmod 755 {} \;

# Berikan hak write khusus pada storage dan bootstrap/cache
sudo chmod -R 775 /var/www/stock-gudang/storage
sudo chmod -R 775 /var/www/stock-gudang/bootstrap/cache
```

---

## 8. Konfigurasi VirtualHost Nginx

Buat file konfigurasi server block Nginx:
```bash
sudo nano /etc/nginx/sites-available/stock-gudang.conf
```

Isikan konfigurasi berikut:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name gudang.gayuhmedia.id 192.168.1.100; # Ganti domain atau IP VM Anda
    root /var/www/stock-gudang/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php index.html;
    charset utf-8;

    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock; # Sesuaikan jika menggunakan php8.3-fpm.sock
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }
}
```

Aktifkan konfigurasi dan restart Nginx:
```bash
sudo ln -sf /etc/nginx/sites-available/stock-gudang.conf /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl restart nginx
```

---

## 9. Konfigurasi Cron Scheduler & Queue Worker

### 1. Cron Scheduler Laravel
Laravel memerlukan scheduler yang berjalan setiap menit untuk sinkronisasi inventaris dan peringatan stok:
```bash
sudo crontab -u www-data -e
```
Tambahkan baris berikut di akhir file:
```cron
* * * * * cd /var/www/stock-gudang && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Systemd Service untuk Queue Worker (Opsional & Disarankan)
Buat service file systemd agar proses antrean berjalan di background secara andal:
```bash
sudo nano /etc/systemd/system/stock-gudang-worker.service
```

Isikan:
```ini
[Unit]
Description=Stock Gudang Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/stock-gudang/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

Aktifkan service:
```bash
sudo systemctl daemon-reload
sudo systemctl enable --now stock-gudang-worker
sudo systemctl status stock-gudang-worker --no-pager
```

---

## 10. Konfigurasi Firewall (UFW)

Amankan akses VM Anda dengan firewall UFW:
```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable
sudo ufw status
```

---

## 11. Pengamanan SSL / HTTPS (Let's Encrypt Certbot)

Jika VM Anda memiliki IP publik dan nama domain yang sudah diarahkan (DNS Record A):
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d gudang.gayuhmedia.id
```
Certbot akan otomatis memodifikasi konfigurasi Nginx untuk mengaktifkan enkripsi HTTPS dan auto-renewal sertifikat SSL.

---

## 12. Prosedur Update Kode di Server Produksi

Saat ada pembaruan kode baru dari GitHub, eksekusi perintah pembaruan berikut pada VM:

```bash
cd /var/www/stock-gudang
sudo git pull origin main
sudo composer install --no-dev --optimize-autoloader
sudo npm install
sudo npm run build
sudo php artisan migrate --force
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
sudo chown -R www-data:www-data /var/www/stock-gudang/storage /var/www/stock-gudang/bootstrap/cache
sudo systemctl restart php8.4-fpm
sudo systemctl restart stock-gudang-worker
```

---

## 13. Akun Pengguna Awal

Akun pengguna awal dikonfigurasi melalui seeder database internal aplikasi (`database/seeders/UserSeeder.php`). 

Silakan hubungi Administrator Sistem / Lead NOC internal Anda untuk memperoleh akses akun, atau Anda dapat mereset dan mengelola kredensial akun pengguna secara langsung melalui Artisan CLI tinker di server:

```bash
cd /var/www/stock-gudang
sudo php artisan tinker
```
