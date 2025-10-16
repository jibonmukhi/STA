# Company Note Notification - Deployment Checklist

## Before Deployment

### 1. Run Migrations
```bash
php artisan migrate
```
This creates the `company_notes` table.

### 2. Verify Tables Exist
Check that these tables exist:
- `notifications`
- `company_notes`
- `jobs` (for queue)
- `failed_jobs`

## After Deployment - CRITICAL STEPS

### Step 1: Queue Worker (MOST IMPORTANT!)
The notification system uses queues. Without a running queue worker, notifications will NOT be sent.

**On Production Server:**
```bash
# Option 1: Run queue worker continuously (recommended for production)
nohup php artisan queue:work --daemon --tries=3 --timeout=90 > storage/logs/queue.log 2>&1 &

# Option 2: Use supervisor (BEST for production)
# Add to /etc/supervisor/conf.d/laravel-worker.conf:
```

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/artisan queue:work --sleep=3 --tries=3 --daemon
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/your/storage/logs/worker.log
stopwaitsecs=3600
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### Step 2: Verify Company Managers Exist
```bash
php debug_notifications.php
```

This script will check:
- Queue configuration
- Mail configuration
- Database tables
- Company managers with correct roles
- Recent notifications

### Step 3: Check Environment Variables
Ensure `.env` has correct mail settings:
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.deshsoft.net
MAIL_PORT=465
MAIL_USERNAME=sta@deshsoft.net
MAIL_PASSWORD="your-password"
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=sta@deshsoft.net
MAIL_FROM_NAME="${APP_NAME}"

QUEUE_CONNECTION=database
```

### Step 4: Test the System

1. **Send a test note:**
   - Go to: `/users/pending/approvals`
   - Click "Note" button for a user
   - Fill subject and message
   - Submit

2. **Check logs immediately:**
```bash
tail -f storage/logs/laravel.log
```

You should see:
```
[date] local.INFO: === SEND NOTE STARTED ===
[date] local.INFO: Validation passed
[date] local.INFO: Company note created
[date] local.INFO: Company managers query executed
[date] local.INFO: ✓ Notification queued successfully
[date] local.INFO: === SEND NOTE COMPLETED ===
```

3. **Check queue worker is processing:**
```bash
tail -f storage/logs/queue.log
# OR
ps aux | grep "queue:work"
```

4. **Check database:**
```sql
-- Check if note was created
SELECT * FROM company_notes ORDER BY created_at DESC LIMIT 1;

-- Check if notification was saved
SELECT * FROM notifications WHERE type LIKE '%CompanyManagerNote%' ORDER BY created_at DESC LIMIT 5;

-- Check pending jobs
SELECT COUNT(*) FROM jobs;

-- Check failed jobs
SELECT COUNT(*) FROM failed_jobs;
```

## Troubleshooting

### Issue: No emails or notifications received

**Likely Cause:** Queue worker not running

**Solution:**
```bash
# Check if queue worker is running
ps aux | grep "queue:work"

# If not running, start it
php artisan queue:work --daemon
```

### Issue: Jobs are in queue but not processing

**Solution:**
```bash
# Restart queue worker
pkill -f "queue:work"
php artisan queue:work --daemon
```

### Issue: Notifications failing

**Check:**
```bash
# Check failed jobs
php artisan queue:failed

# View details
php artisan queue:failed-table

# Retry failed jobs
php artisan queue:retry all
```

### Issue: No company managers found

**Solution:**
```bash
php artisan tinker
>>> User::role('company_manager')->get()
>>> # Verify users have 'company_manager' role
```

### Issue: Route not found error

**Check logs:**
```bash
tail -f storage/logs/laravel.log | grep "Route"
```

**Solution:** Ensure route `company-users.index` exists:
```bash
php artisan route:list --name=company-users
```

## Daily Monitoring

### Check queue health:
```bash
# Check pending jobs
php artisan queue:monitor database:default --max=100

# Check failed jobs
php artisan queue:failed
```

### Check logs:
```bash
# Application logs
tail -n 100 storage/logs/laravel.log

# Queue worker logs (if using supervisor)
tail -n 100 storage/logs/worker.log
```

## Performance Tips

1. **Use Redis instead of database for queue** (faster):
```env
QUEUE_CONNECTION=redis
```

2. **Run multiple queue workers** for high load:
```bash
php artisan queue:work --queue=high,default --daemon &
php artisan queue:work --queue=default --daemon &
```

3. **Set up queue monitoring:**
```bash
php artisan horizon # If using Laravel Horizon
```

## Emergency Commands

```bash
# Clear all queued jobs
php artisan queue:clear

# Flush all failed jobs
php artisan queue:flush

# Restart queue workers (if using supervisor)
sudo supervisorctl restart laravel-worker:*

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Success Indicators

✅ Queue worker is running
✅ `debug_notifications.php` shows company managers exist
✅ Logs show "Notification queued successfully"
✅ Database `notifications` table has new entries
✅ Company managers receive emails
✅ Company managers see notifications in-app (bell icon)

## Contact for Issues

If issues persist:
1. Run `php debug_notifications.php` and share output
2. Share last 50 lines of `storage/logs/laravel.log`
3. Check queue worker status: `ps aux | grep queue`
