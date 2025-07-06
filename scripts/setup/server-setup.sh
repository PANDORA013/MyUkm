#!/bin/bash
# Otomatisasi setup server
echo "🔥 Memulai persiapan server..."

# Update sistem
sudo apt-get update -y

# Install Git
sudo apt-get install git -y
git config --global user.name "MyUKM Deploy"
git config --global user.email "deploy@myukm.id"

# Install PHP 8.2 & Ekstensi
sudo apt-get install -y php8.2 php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml php8.2-zip php8.2-curl php8.2-gd

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Install Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"

# Setup folder project
sudo mkdir -p /var/www/myukm
sudo chown -R $USER:www-data /var/www/myukm
sudo chmod -R 775 /var/www/myukm/storage

echo "✅ Server siap deploy! Jalankan command berikut:"
echo "1. cd /var/www/myukm"
echo "2. git clone https://github.com/PANDORA013/MyUkm.git ."
