#!/bin/bash

# Configuration Script for Large File Uploads (Staging)
# Target: 20MB per file, 20 files per batch => ~500MB total

PHP_INI="/etc/php/8.4/fpm/php.ini"
NGINX_CONF="/etc/nginx/nginx.conf" # Or specific site config if possible, but global is easier for client_max_body
SITE_CONF="/etc/nginx/sites-available/florence-egi.157.245.20.197.sslip.io"

echo "Configuring PHP 8.4 FPM..."

# Backup php.ini
if [ ! -f "$PHP_INI.bak" ]; then
    sudo cp $PHP_INI $PHP_INI.bak
fi

# Update php.ini using sed
sudo sed -i 's/^post_max_size = .*/post_max_size = 500M/' $PHP_INI
sudo sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 25M/' $PHP_INI
sudo sed -i 's/^max_file_uploads = .*/max_file_uploads = 25/' $PHP_INI

echo "PHP Config Updated:"
grep -E 'post_max_size|upload_max_filesize|max_file_uploads' $PHP_INI

echo "Configuring Nginx..."

# Update Site Config for client_max_body_size if it exists, otherwise add it to http block in global
if [ -f "$SITE_CONF" ]; then
    # Check if client_max_body_size exists
    if grep -q "client_max_body_size" $SITE_CONF; then
        sudo sed -i 's/client_max_body_size .*/client_max_body_size 500M;/' $SITE_CONF
    else
        # Insert inside 'server {' block (simplified approach: after 'server_name')
        sudo sed -i '/server_name/a \    client_max_body_size 500M;' $SITE_CONF
    fi
else
    echo "Site config not found at expected path: $SITE_CONF"
    echo "Please check Forge Nginx settings manually."
fi

echo "Restarting Services..."
sudo service php8.4-fpm restart
sudo service nginx restart

echo "Done! Upload limits increased."
