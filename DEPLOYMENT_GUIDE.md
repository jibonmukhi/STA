# STA Management System - Deployment Guide

## Pre-Deployment Checklist ✅

### 1. Server Requirements
- [ ] PHP >= 8.2
- [ ] MySQL >= 8.0 or MariaDB >= 10.3
- [ ] Composer 2.x
- [ ] Node.js >= 18.x
- [ ] NPM >= 9.x
- [ ] Required PHP Extensions:
  - [ ] BCMath
  - [ ] Ctype
  - [ ] JSON
  - [ ] Mbstring
  - [ ] OpenSSL
  - [ ] PDO
  - [ ] PDO_MySQL
  - [ ] Tokenizer
  - [ ] XML
  - [ ] cURL
  - [ ] GD or ImageMagick
  - [ ] ZIP

### 2. Security Configuration
- [ ] SSL Certificate installed
- [ ] Firewall configured
- [ ] Database access restricted
- [ ] File permissions set correctly
- [ ] Disable directory listing in web server

## Deployment Steps

### Step 1: Upload Files
```bash
# Clone repository or upload files to server
git clone [repository-url] /var/www/sta
cd /var/www/sta
```

### Step 2: Set File Permissions
```bash
# Set proper ownership
chown -R www-data:www-data /var/www/sta

# Set directory permissions
find /var/www/sta -type d -exec chmod 755 {} \;

# Set file permissions
find /var/www/sta -type f -exec chmod 644 {} \;

# Set storage and cache permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Step 3: Install Dependencies
```bash
# Install PHP dependencies (production)
composer install --optimize-autoloader --no-dev

# Install Node dependencies and build assets
npm install
npm run build
```

### Step 4: Environment Configuration
```bash
# Copy production environment file
cp .env.production.example .env

# Generate application key
php artisan key:generate

# Edit .env file with your production settings
nano .env
```

**Important .env settings to configure:**
- `APP_URL` - Your domain URL
- `DB_*` - Database credentials
- `MAIL_*` - Email server settings
- `SESSION_SECURE_COOKIE=true` (for HTTPS)

### Step 5: Database Setup
```bash
# Run database migrations
php artisan migrate --force

# Seed initial data (if needed)
php artisan db:seed --force

# Create first admin user (if not seeded)
php artisan tinker
>>> \App\Models\User::create([
>>>     'name' => 'Admin',
>>>     'email' => 'admin@yourdomain.com',
>>>     'password' => \Hash::make('your-secure-password'),
>>>     'status' => 'active'
>>> ]);
>>> $user = \App\Models\User::where('email', 'admin@yourdomain.com')->first();
>>> $user->assignRole('sta_manager');
```

### Step 6: Optimize Application
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Cache events
php artisan event:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### Step 7: Storage Setup
```bash
# Create storage link for public files
php artisan storage:link

# Ensure storage directories exist
mkdir -p storage/app/public/user-photos
mkdir -p storage/app/public/company-logos
mkdir -p storage/app/public/certificates
mkdir -p storage/app/public/transcripts
```

### Step 8: Queue Configuration (Optional)
```bash
# If using queues, set up supervisor
sudo apt-get install supervisor

# Create supervisor configuration
sudo nano /etc/supervisor/conf.d/sta-worker.conf
```

Supervisor configuration:
```ini
[program:sta-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sta/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/sta/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Start supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start sta-worker:*
```

### Step 9: Web Server Configuration

#### Apache Configuration
```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /var/www/sta/public

    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key
    SSLCertificateChainFile /path/to/chain.crt

    <Directory /var/www/sta/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/sta-error.log
    CustomLog ${APACHE_LOG_DIR}/sta-access.log combined
</VirtualHost>

# HTTP to HTTPS redirect
<VirtualHost *:80>
    ServerName yourdomain.com
    Redirect permanent / https://yourdomain.com/
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    root /var/www/sta/public;

    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

# HTTP to HTTPS redirect
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}
```

### Step 10: Cron Jobs
```bash
# Add Laravel scheduler to crontab
crontab -e
```

Add this line:
```
* * * * * cd /var/www/sta && php artisan schedule:run >> /dev/null 2>&1
```

## Post-Deployment Tasks

### 1. Testing
- [ ] Test user registration
- [ ] Test login/logout
- [ ] Test password reset
- [ ] Test file uploads (photos, certificates)
- [ ] Test email sending
- [ ] Test audit log creation
- [ ] Test Data Vault functionality
- [ ] Test all CRUD operations

### 2. Monitoring Setup
- [ ] Set up application monitoring (New Relic, Sentry, etc.)
- [ ] Configure log rotation
- [ ] Set up backup scripts
- [ ] Configure health checks

### 3. Backup Configuration
Create backup script `/usr/local/bin/sta-backup.sh`:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/sta"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u [username] -p[password] sta_production | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup uploaded files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/sta/storage/app/public

# Keep only last 30 days of backups
find $BACKUP_DIR -name "*.gz" -mtime +30 -delete
```

Add to crontab:
```
0 2 * * * /usr/local/bin/sta-backup.sh
```

## Security Hardening

### 1. Environment File
```bash
# Ensure .env is not accessible
chmod 600 .env
```

### 2. Disable Debug Mode
Ensure in `.env`:
```
APP_DEBUG=false
APP_ENV=production
```

### 3. Security Headers
Add to `.htaccess` or nginx config:
```
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
```

### 4. Rate Limiting
Configure in `app/Http/Kernel.php`:
```php
'api' => [
    'throttle:60,1',
],
```

## Maintenance Mode

### Enable Maintenance Mode
```bash
php artisan down --message="Upgrading Database" --retry=60
```

### Disable Maintenance Mode
```bash
php artisan up
```

## Troubleshooting

### Common Issues

1. **500 Error**
   - Check logs: `tail -f storage/logs/laravel.log`
   - Check permissions on storage and bootstrap/cache
   - Verify .env file exists and is configured
   - Run `php artisan config:clear`

2. **Database Connection Error**
   - Verify database credentials in .env
   - Check if database server is running
   - Verify database exists

3. **File Upload Issues**
   - Check `post_max_size` and `upload_max_filesize` in php.ini
   - Verify storage permissions
   - Check if storage link exists: `php artisan storage:link`

4. **Email Not Sending**
   - Verify SMTP settings in .env
   - Check firewall allows outbound connections on SMTP port
   - Test with `php artisan tinker` and Mail facade

5. **Assets Not Loading**
   - Run `npm run build`
   - Check if public/build directory exists
   - Clear browser cache

## Performance Optimization

1. **Enable OPcache**
   ```ini
   opcache.enable=1
   opcache.memory_consumption=256
   opcache.interned_strings_buffer=16
   opcache.max_accelerated_files=10000
   opcache.validate_timestamps=0
   ```

2. **Configure Redis (Optional)**
   ```bash
   apt-get install redis-server
   # Update .env to use Redis for cache and sessions
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   ```

3. **Enable HTTP/2**
   - Configure in web server

4. **Enable Gzip Compression**
   - Configure in web server

## Rollback Plan

If deployment fails:
1. Restore previous code version
2. Restore database backup
3. Clear all caches
4. Check error logs
5. Document issues for resolution

## Contact Information

For deployment support:
- Technical Lead: [contact email]
- System Administrator: [contact email]
- Emergency Contact: [phone number]

## Final Checklist

- [ ] Application accessible via HTTPS
- [ ] All features tested and working
- [ ] Backups configured and tested
- [ ] Monitoring active
- [ ] Documentation updated
- [ ] Team notified of deployment completion

---

**Last Updated:** January 2025
**Version:** 1.0