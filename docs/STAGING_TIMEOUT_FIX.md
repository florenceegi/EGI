# ⏱️ Fix Timeout 504 su Staging

## 🎯 CONFIGURAZIONI DA AUMENTARE

### **1. PHP-FPM (`/etc/php/8.2/fpm/php.ini`)**

```ini
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
```

### **2. PHP-FPM Pool (`/etc/php/8.2/fpm/pool.d/www.conf`)**

```ini
request_terminate_timeout = 300
```

### **3. Nginx (`/etc/nginx/sites-available/egi` o nel server block)**

```nginx
server {
    # ... existing config ...

    location ~ \.php$ {
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
        # ... other fastcgi settings ...
    }
}
```

### **4. Restart Services**

```bash
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

---

## 🚀 COMANDI RAPIDI PER STAGING

```bash
# 1. Modifica PHP
sudo nano /etc/php/8.2/fpm/php.ini
# Cerca: max_execution_time
# Cambia a: 300

# 2. Modifica PHP-FPM Pool
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
# Cerca: request_terminate_timeout
# Cambia a: 300

# 3. Modifica Nginx
sudo nano /etc/nginx/sites-available/default
# Aggiungi nella location ~ \.php$:
# fastcgi_read_timeout 300;

# 4. Restart
sudo systemctl restart php8.2-fpm nginx

# 5. Verifica
php -i | grep max_execution_time
```
