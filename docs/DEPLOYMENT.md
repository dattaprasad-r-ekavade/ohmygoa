# OhMyGoa - Production Deployment Guide

## Table of Contents
1. [Server Requirements](#server-requirements)
2. [Initial Server Setup](#initial-server-setup)
3. [Installation Steps](#installation-steps)
4. [SSL Certificate Setup](#ssl-certificate-setup)
5. [Database Configuration](#database-configuration)
6. [Queue Workers Setup](#queue-workers-setup)
7. [Cron Jobs Configuration](#cron-jobs-configuration)
8. [Monitoring Setup](#monitoring-setup)
9. [Security Hardening](#security-hardening)
10. [Deployment Process](#deployment-process)
11. [Rollback Procedure](#rollback-procedure)
12. [Backup Strategy](#backup-strategy)
13. [Troubleshooting](#troubleshooting)

---

## Server Requirements

### Minimum Specifications
- **OS:** Ubuntu 22.04 LTS (recommended) or CentOS 8+
- **RAM:** 4GB minimum, 8GB recommended
- **CPU:** 2 cores minimum, 4 cores recommended
- **Storage:** 50GB SSD minimum, 100GB recommended
- **Bandwidth:** 100 Mbps minimum

### Software Requirements
- PHP 8.2 or higher
- Nginx 1.18+ or Apache 2.4+
- MySQL 8.0+ or PostgreSQL 14+
- Redis 6.0+ (for cache, sessions, queues)
- Composer 2.x
- Node.js 18+ and NPM (for frontend assets)
- Supervisor (for queue workers)
- Git

### PHP Extensions Required
```bash
php8.2-cli
php8.2-fpm
php8.2-mysql
php8.2-redis
php8.2-mbstring
php8.2-xml
php8.2-bcmath
php8.2-curl
php8.2-gd
php8.2-zip
php8.2-intl
php8.2-soap
```

---

## Initial Server Setup

### 1. Update System Packages
```bash
sudo apt update && sudo apt upgrade -y
```

### 2. Install PHP 8.2
```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-mysql php8.2-redis \
    php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-gd \
    php8.2-zip php8.2-intl php8.2-soap
```

### 3. Install MySQL
```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

### 4. Install Redis
```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

### 5. Install Nginx
```bash
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

### 6. Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### 7. Install Supervisor
```bash
sudo apt install -y supervisor
sudo systemctl enable supervisor
sudo systemctl start supervisor
```

### 8. Install Node.js and NPM
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

---

## Installation Steps

### 1. Create Application Directory
```bash
sudo mkdir -p /var/www/ohmygoa
sudo chown -R www-data:www-data /var/www/ohmygoa
```

### 2. Clone Repository
```bash
cd /var/www/ohmygoa
sudo -u www-data git clone https://github.com/dattaprasad-r-ekavade/ohmygoa.git .
```

### 3. Install Dependencies
```bash
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm install
sudo -u www-data npm run build
```

### 4. Configure Environment
```bash
sudo -u www-data cp .env.production .env
sudo -u www-data nano .env
```

Update the following values in `.env`:
- `APP_KEY` (generate with `php artisan key:generate`)
- `APP_URL`
- `DB_*` (database credentials)
- `REDIS_*` (Redis configuration)
- `MAIL_*` (email configuration)
- `RAZORPAY_*` (payment gateway credentials)

### 5. Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/ohmygoa
sudo chmod -R 775 /var/www/ohmygoa/storage
sudo chmod -R 775 /var/www/ohmygoa/bootstrap/cache
```

### 6. Run Migrations
```bash
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan db:seed --force
```

### 7. Optimize Application
```bash
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan event:cache
```

---

## SSL Certificate Setup

### Using Let's Encrypt (Free)
```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d ohmygoa.com -d www.ohmygoa.com

# Auto-renewal is configured automatically
# Test renewal: sudo certbot renew --dry-run
```

### Manual Certificate Installation
1. Place certificate files in `/etc/ssl/certs/`
2. Update Nginx configuration with certificate paths
3. Reload Nginx: `sudo systemctl reload nginx`

---

## Database Configuration

### 1. Create Database
```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE ohmygoa_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ohmygoa_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON ohmygoa_production.* TO 'ohmygoa_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 2. Configure MySQL for Performance
Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:
```ini
[mysqld]
innodb_buffer_pool_size = 2G
innodb_log_file_size = 512M
max_connections = 200
query_cache_type = 1
query_cache_size = 64M
```

Restart MySQL:
```bash
sudo systemctl restart mysql
```

---

## Queue Workers Setup

### 1. Install Supervisor Configuration
```bash
sudo cp supervisor-ohmygoa.conf /etc/supervisor/conf.d/ohmygoa.conf
```

### 2. Update Supervisor
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start ohmygoa-worker:*
```

### 3. Check Worker Status
```bash
sudo supervisorctl status
```

### 4. View Worker Logs
```bash
tail -f /var/www/ohmygoa/storage/logs/worker.log
```

---

## Cron Jobs Configuration

### Install Crontab
```bash
sudo crontab -u www-data -e
```

Paste contents from `crontab-ohmygoa.txt` or:
```bash
sudo crontab -u www-data crontab-ohmygoa.txt
```

### Verify Cron Jobs
```bash
sudo crontab -u www-data -l
```

---

## Monitoring Setup

### 1. Laravel Telescope (Development Only)
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

Access at: `https://ohmygoa.com/telescope`

### 2. Log Monitoring
```bash
# Install Papertrail
sudo apt install -y rsyslog-gnutls
# Configure rsyslog with Papertrail endpoint
```

### 3. Uptime Monitoring
- Sign up for UptimeRobot or Pingdom
- Add monitoring for: `https://ohmygoa.com/health`
- Configure alert emails

### 4. Error Tracking with Sentry
```bash
composer require sentry/sentry-laravel
php artisan sentry:publish --dsn=YOUR_SENTRY_DSN
```

### 5. Application Performance Monitoring
**New Relic:**
```bash
sudo apt install -y newrelic-php5
sudo newrelic-install install
```

**Laravel Debugbar (Development Only):**
```bash
composer require barryvdh/laravel-debugbar --dev
```

---

## Security Hardening

### 1. Firewall Configuration (UFW)
```bash
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow 22/tcp   # SSH
sudo ufw allow 80/tcp   # HTTP
sudo ufw allow 443/tcp  # HTTPS
sudo ufw enable
```

### 2. Fail2Ban Setup
```bash
sudo apt install -y fail2ban
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
sudo nano /etc/fail2ban/jail.local
```

Add Nginx jail:
```ini
[nginx-http-auth]
enabled = true
filter = nginx-http-auth
port = http,https
logpath = /var/log/nginx/error.log
```

Restart Fail2Ban:
```bash
sudo systemctl restart fail2ban
```

### 3. SSH Key Authentication
```bash
# On local machine, generate key
ssh-keygen -t rsa -b 4096

# Copy to server
ssh-copy-id user@server_ip

# Disable password authentication
sudo nano /etc/ssh/sshd_config
# Set: PasswordAuthentication no
sudo systemctl restart sshd
```

### 4. Secure PHP Configuration
Edit `/etc/php/8.2/fpm/php.ini`:
```ini
expose_php = Off
display_errors = Off
log_errors = On
error_log = /var/log/php-fpm/error.log
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 60
memory_limit = 256M
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

---

## Deployment Process

### Automated Deployment
```bash
# Make deploy script executable
chmod +x deploy.sh

# Run deployment
./deploy.sh
```

### Manual Deployment Steps
```bash
# 1. Enable maintenance mode
php artisan down --retry=60

# 2. Pull latest code
git pull origin master

# 3. Install dependencies
composer install --no-dev --optimize-autoloader

# 4. Run migrations
php artisan migrate --force

# 5. Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Restart queue workers
php artisan queue:restart

# 7. Disable maintenance mode
php artisan up
```

---

## Rollback Procedure

### Using Rollback Script
```bash
# List available backups
ls -lt /var/backups/ohmygoa

# Rollback to specific backup
./rollback.sh 20241201_143000
```

### Manual Rollback
```bash
# 1. Enable maintenance mode
php artisan down

# 2. Restore database
mysql -u ohmygoa_user -p ohmygoa_production < /var/backups/ohmygoa/db_backup_TIMESTAMP.sql

# 3. Rollback Git repository
git reset --hard PREVIOUS_COMMIT_HASH

# 4. Reinstall dependencies
composer install --no-dev --optimize-autoloader

# 5. Clear caches
php artisan config:clear
php artisan cache:clear

# 6. Rebuild caches
php artisan config:cache
php artisan route:cache

# 7. Disable maintenance mode
php artisan up
```

---

## Backup Strategy

### Automated Backups
The `backup.sh` script runs daily at 2:00 AM via cron and:
- Creates compressed database backup
- Backs up storage files
- Backs up configuration files
- Uploads to S3 (if configured)
- Retains backups for 30 days
- Sends email notification

### Manual Backup
```bash
# Run backup script
./backup.sh

# Or use Laravel backup command
php artisan backup:run
```

### Restore from Backup
```bash
# Database restore
gunzip < /var/backups/ohmygoa/database/db_backup_TIMESTAMP.sql.gz | mysql -u ohmygoa_user -p ohmygoa_production

# Storage restore
tar -xzf /var/backups/ohmygoa/files/storage_backup_TIMESTAMP.tar.gz -C /var/www/ohmygoa/
```

---

## Troubleshooting

### Application Errors

**500 Internal Server Error:**
```bash
# Check PHP-FPM logs
tail -f /var/log/php8.2-fpm.log

# Check Nginx error logs
tail -f /var/log/nginx/ohmygoa-error.log

# Check Laravel logs
tail -f /var/www/ohmygoa/storage/logs/laravel.log

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

**Queue Workers Not Processing:**
```bash
# Check supervisor status
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart ohmygoa-worker:*

# Check worker logs
tail -f /var/www/ohmygoa/storage/logs/worker.log

# Check failed jobs
php artisan queue:failed
```

**Database Connection Errors:**
```bash
# Test database connection
mysql -u ohmygoa_user -p ohmygoa_production

# Check MySQL status
sudo systemctl status mysql

# Check MySQL error logs
tail -f /var/log/mysql/error.log
```

### Performance Issues

**High Memory Usage:**
```bash
# Check PHP-FPM processes
ps aux | grep php-fpm

# Adjust PHP-FPM pool settings
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
# Adjust: pm.max_children, pm.start_servers
```

**Slow Database Queries:**
```bash
# Enable slow query log
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
# Add:
# slow_query_log = 1
# slow_query_log_file = /var/log/mysql/slow.log
# long_query_time = 2

# Restart MySQL
sudo systemctl restart mysql

# Analyze slow queries
sudo mysqldumpslow /var/log/mysql/slow.log
```

### SSL Certificate Issues
```bash
# Test SSL certificate
openssl s_client -connect ohmygoa.com:443

# Renew certificate manually
sudo certbot renew --force-renewal

# Check certificate expiry
sudo certbot certificates
```

---

## Health Check Endpoints

- **Application Health:** `https://ohmygoa.com/health`
- **Database Health:** `https://ohmygoa.com/api/health/database`
- **Queue Health:** `https://ohmygoa.com/api/health/queue`
- **Redis Health:** `https://ohmygoa.com/api/health/redis`

---

## Support & Maintenance

### Regular Maintenance Tasks
- **Daily:** Check error logs, monitor disk space
- **Weekly:** Review failed jobs, check backup integrity
- **Monthly:** Update dependencies, security patches, database optimization

### Getting Help
- **Documentation:** `docs/` directory
- **Repository:** https://github.com/dattaprasad-r-ekavade/ohmygoa
- **Email Support:** admin@ohmygoa.com

---

## Deployment Checklist

- [ ] Server meets minimum requirements
- [ ] All required software installed
- [ ] Database created and configured
- [ ] Environment variables set in `.env`
- [ ] SSL certificate installed
- [ ] Nginx/Apache configured
- [ ] Queue workers running via Supervisor
- [ ] Cron jobs configured
- [ ] File permissions set correctly
- [ ] Backups configured and tested
- [ ] Monitoring and alerts configured
- [ ] Security hardening applied
- [ ] Application tested on production
- [ ] DNS records configured
- [ ] Email delivery tested
- [ ] Payment gateway tested (sandbox mode first)

---

**Last Updated:** December 2024  
**Version:** 1.0
