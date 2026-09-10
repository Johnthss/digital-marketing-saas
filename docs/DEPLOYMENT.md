# Deployment Guide

## Server Requirements

### Minimum Requirements

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| **PHP** | 8.4 | 8.4+ |
| **MySQL** | 8.0 | 8.0+ |
| **Redis** | 6.0 | 7.0+ |
| **Node.js** | 18 LTS | 20 LTS |
| **Composer** | 2.x | 2.x |
| **Storage** | 10 GB | 50+ GB SSD |
| **RAM** | 2 GB | 4+ GB |
| **CPU** | 2 cores | 4+ cores |

### PHP Extensions

Ensure the following PHP extensions are installed:

```bash
# Required
php-bcmath
php-ctype
php-curl
php-dom
php-fileinfo
php-gd
php-intl
php-json
php-mbstring
php-mysql
php-openssl
php-pdo
php-pdo_mysql
php-simplexml
php-tokenizer
php-xml
php-xmlreader
php-zip
php-zlib

# Recommended
php-redis
php-imagick
```

### Web Server

- **Nginx** (recommended) or Apache 2.4+
- SSL/TLS certificate (Let's Encrypt or commercial)
- HTTP/2 support

## Deployment Steps

### 1. Server Preparation

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install PHP 8.4
sudo apt install php8.4-fpm php8.4-cli php8.4-common php8.4-mysql \
    php8.4-zip php8.4-gd php8.4-mbstring php8.4-curl php8.4-xml \
    php8.4-bcmath php8.4-intl php8.4-redis php8.4-imagick -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install nodejs -y

# Install Redis
sudo apt install redis-server -y
sudo systemctl enable redis-server

# Install Nginx
sudo apt install nginx -y
sudo systemctl enable nginx
```

### 2. Database Setup

```bash
# Create MySQL database and user
mysql -u root -p
```

```sql
CREATE DATABASE digital_marketing_saas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dms_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON digital_marketing_saas.* TO 'dms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Application Deployment

```bash
# Clone repository
cd /var/www
git clone https://github.com/webbixray/digital-marketing-saas.git
cd digital-marketing-saas

# Set permissions
sudo chown -R www-data:www-data /var/www/digital-marketing-saas
sudo chmod -R 775 storage bootstrap/cache

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Configure environment
cp .env.example .env
php artisan key:generate
```

### 4. Environment Configuration

Edit `.env` with production values:

```env
APP_NAME="Digital Marketing SaaS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=digital_marketing_saas
DB_USERNAME=dms_user
DB_PASSWORD=strong_password_here

CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379

LOG_CHANNEL=stack
LOG_LEVEL=warning

# Stripe (Live Mode)
STRIPE_KEY=pk_live_xxxxxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_live_xxxxxxxxxxxxxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxxxxxx

# AI Providers
OPENAI_API_KEY=sk-xxxxxxxxxxxxxxxxxxxxxxxx
ANTHROPIC_API_KEY=sk-ant-xxxxxxxxxxxxxxxx
GOOGLE_AI_API_KEY=xxxxxxxxxxxxxxxx
```

### 5. Database Migration

```bash
# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed --force

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 6. Nginx Configuration

Create `/etc/nginx/sites-available/digital-marketing-saas`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;
    root /var/www/digital-marketing-saas/public;

    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;
    ssl_prefer_server_ciphers off;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'nonce-{RANDOM}'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self'";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to sensitive files
    location ~ /\.(env|git|htaccess|htpasswd) {
        deny all;
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript application/xml+rss application/atom+xml image/svg+xml;
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/digital-marketing-saas /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 7. SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
sudo systemctl enable certbot.timer
```

## Environment Variables Reference

### Application

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_NAME` | Application name | Laravel |
| `APP_ENV` | Environment (local/production) | production |
| `APP_DEBUG` | Enable debug mode | false |
| `APP_URL` | Application URL | http://localhost:8000 |

### Database

| Variable | Description | Default |
|----------|-------------|---------|
| `DB_CONNECTION` | Database driver | sqlite |
| `DB_HOST` | Database host | 127.0.0.1 |
| `DB_PORT` | Database port | 3306 |
| `DB_DATABASE` | Database name | laravel |
| `DB_USERNAME` | Database username | root |
| `DB_PASSWORD` | Database password | (empty) |

### Cache & Session

| Variable | Description | Default |
|----------|-------------|---------|
| `CACHE_DRIVER` | Cache driver | redis |
| `SESSION_DRIVER` | Session driver | redis |
| `SESSION_LIFETIME` | Session lifetime (minutes) | 120 |
| `SESSION_ENCRYPT` | Encrypt session data | true |
| `SESSION_SECURE_COOKIE` | Secure cookie flag | true |

### Queue

| Variable | Description | Default |
|----------|-------------|---------|
| `QUEUE_CONNECTION` | Queue driver | redis |

### Redis

| Variable | Description | Default |
|----------|-------------|---------|
| `REDIS_HOST` | Redis host | 127.0.0.1 |
| `REDIS_PASSWORD` | Redis password | null |
| `REDIS_PORT` | Redis port | 6379 |

### Mail

| Variable | Description | Default |
|----------|-------------|---------|
| `MAIL_MAILER` | Mail driver | smtp |
| `MAIL_HOST` | SMTP host | 127.0.0.1 |
| `MAIL_PORT` | SMTP port | 587 |
| `MAIL_USERNAME` | SMTP username | null |
| `MAIL_PASSWORD` | SMTP password | null |
| `MAIL_FROM_ADDRESS` | From address | hello@example.com |
| `MAIL_FROM_NAME` | From name | ${APP_NAME} |

### Stripe

| Variable | Description | Default |
|----------|-------------|---------|
| `STRIPE_KEY` | Publishable key | pk_test_... |
| `STRIPE_SECRET` | Secret key | sk_test_... |
| `STRIPE_WEBHOOK_SECRET` | Webhook signing secret | whsec_... |
| `STRIPE_CURRENCY` | Default currency | usd |

### AI Providers

| Variable | Description |
|----------|-------------|
| `OPENAI_API_KEY` | OpenAI API key |
| `OPENAI_ORGANIZATION` | OpenAI organization ID |
| `ANTHROPIC_API_KEY` | Anthropic API key |
| `GOOGLE_AI_API_KEY` | Google AI API key |

### Telegram

| Variable | Description | Default |
|----------|-------------|---------|
| `TELEGRAM_BOT_TOKEN` | Bot token from BotFather | (empty) |
| `TELEGRAM_BOT_USERNAME` | Bot username | (empty) |
| `TELEGRAM_ENABLED` | Enable Telegram integration | false |

### Twitter/X

| Variable | Description |
|----------|-------------|
| `TWITTER_API_KEY` | API Key |
| `TWITTER_API_SECRET` | API Secret |
| `TWITTER_ACCESS_TOKEN` | Access Token |
| `TWITTER_ACCESS_SECRET` | Access Token Secret |
| `TWITTER_BEARER_TOKEN` | Bearer Token |

## Queue Setup

### Supervisor Configuration

Install Supervisor to manage queue workers:

```bash
sudo apt install supervisor -y
```

Create `/etc/supervisor/conf.d/digital-marketing-saas.conf`:

```ini
[program:dms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/digital-marketing-saas/artisan queue:work redis --queue=default,ai,reports,social --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/dms-worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start dms-worker:*
```

### Queue Priorities

Jobs are dispatched to specific queues:

| Queue | Purpose |
|-------|---------|
| `default` | General background jobs |
| `ai` | AI content generation |
| `reports` | Report generation and export |
| `social` | Social media publishing |

### Horizon (Optional)

For advanced queue monitoring, install Laravel Horizon:

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

## Scheduler Setup

### Cron Entry

Add to crontab (`crontab -e`):

```cron
* * * * * cd /var/www/digital-marketing-saas && php artisan schedule:run >> /dev/null 2>&1
```

### Scheduled Tasks

The following tasks run automatically:

| Task | Frequency | Command |
|------|-----------|---------|
| Process scheduled posts | Every minute | `social:process-scheduled` |
| Run workflow automations | Every minute | `workflows:run` |
| Fetch platform metrics | Every 15 minutes | `social:fetch-metrics` |
| Retry failed posts | Every 30 minutes | `social:retry-failed` |
| Generate scheduled reports | Daily at 6 AM | `reports:generate-scheduled` |
| Process GDPR deletion requests | Daily at 2 AM | `gdpr:process-deletions` |
| Check for updates | Daily at 3 AM | `system:check-updates` |
| Clean up old logs | Weekly | `system:cleanup-logs` |

### Custom Schedule Definition

```php
// routes/console.php
Schedule::command('social:process-scheduled')->everyMinute();
Schedule::command('workflows:run')->everyMinute();
Schedule::command('social:fetch-metrics')->everyFifteenMinutes();
Schedule::command('social:retry-failed')->everyThirtyMinutes();
Schedule::command('reports:generate-scheduled')->dailyAt('06:00');
Schedule::command('gdpr:process-deletions')->dailyAt('02:00');
```

## Monitoring Setup

### Application Logging

Configure logging channels in `config/logging.php`:

```env
LOG_CHANNEL=stack
LOG_STACK=single,daily,slack
```

### Log Rotation

Create `/etc/logrotate.d/digital-marketing-saas`:

```
/var/www/digital-marketing-saas/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
```

### Health Checks

Create a health check endpoint:

```php
// routes/web.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'database' => DB::connection()->getPdo() ? 'connected' : 'error',
        'cache' => Cache::store()->get('health-check') !== false ? 'connected' : 'error',
        'queue' => Queue::size() < 1000 ? 'healthy' : 'backlogged',
        'storage' => is_writable(storage_path()) ? 'writable' : 'error',
    ]);
});
```

### Uptime Monitoring

Recommended monitoring services:
- **UptimeRobot** — Free tier, 5-minute intervals
- **Laravel Pulse** — Application-specific monitoring
- **Sentry** — Error tracking and performance monitoring

### Laravel Pulse (Recommended)

```bash
composer require laravel/pulse
php artisan pulse:install
php artisan migrate
```

Access at `/pulse` for real-time metrics:
- Queue throughput
- Slow requests
- Memory usage
- Exception tracking

### Sentry Error Tracking

```bash
composer require sentry/sentry-laravel
```

```env
SENTRY_LARAVEL_DSN=https://xxxxxxxxxxxxxxxx@sentry.io/123456
SENTRY_TRACES_SAMPLE_RATE=0.1
```

### Prometheus Metrics (Optional)

For advanced monitoring with Grafana:

```bash
composer require promphp/prometheus_client_php
```

Expose metrics at `/metrics` endpoint for Prometheus scraping.

## Backup Strategy

### Database Backups

```bash
# Daily database backup
0 2 * * * mysqldump -u dms_user -p'db_password' digital_marketing_saas | gzip > /backups/db/$(date +\%Y\%m\%d).sql.gz
```

### File Backups

```bash
# Weekly file backup
0 3 * * 0 tar -czf /backups/files/$(date +\%Y\%m\%d).tar.gz /var/www/digital-marketing-saas/storage/app
```

### Automated Backup with Laravel

```php
// routes/console.php
Schedule::command('backup:run --only-db')->daily()->at('02:00');
Schedule::command('backup:run')->weekly()->at('03:00');
```

## Scaling Considerations

### Horizontal Scaling

1. **Load Balancer** — Use Nginx or AWS ALB
2. **Shared Storage** — S3 or NFS for file uploads
3. **Database** — Read replicas for reporting queries
4. **Cache** — Redis Cluster for session/cache
5. **Queue** — Multiple queue workers across servers

### Performance Optimization

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Cache events
php artisan event:cache

# Optimize Composer autoloader
composer dump-autoload --optimize

# Enable OPcache (php.ini)
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
```

### CDN Integration

Configure CloudFront or Cloudflare for static assets:

```env
ASSET_URL=https://cdn.your-domain.com
AWS_CLOUDFRONT_URL=https://cdn.your-domain.com
```

## Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| 500 errors | Check `storage/logs/laravel.log` |
| Queue not processing | Verify Supervisor is running: `sudo supervisorctl status` |
| Scheduler not running | Verify cron entry: `crontab -l` |
| Permission denied | Run: `sudo chown -R www-data:www-data storage bootstrap/cache` |
| CSS/JS not loading | Run: `npm run build` |
| Database connection refused | Verify MySQL is running: `sudo systemctl status mysql` |

### Debug Mode

Never enable `APP_DEBUG=true` in production. For temporary debugging:

```bash
# Enable temporarily
php artisan tinker
>>> config(['app.debug' => true]);
```

### Log Monitoring

```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log

# Search for errors
grep -i "error\|exception\|fatal" storage/logs/laravel.log
```

---

*For developer documentation, see [DEVELOPER.md](DEVELOPER.md).*
*For user-facing documentation, see [USER_GUIDE.md](USER_GUIDE.md).*
