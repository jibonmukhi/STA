# cPanel Deployment & Notification Setup Guide

## Step 1: Upload Files to cPanel

1. **Upload via File Manager or FTP:**
   - Upload all files to `public_html` or your domain folder
   - Make sure `.env` file is uploaded and configured

2. **Set Permissions:**
   ```
   storage/ - 755
   bootstrap/cache/ - 755
   ```

## Step 2: Configure .env File

Edit `.env` in cPanel File Manager:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Mail Settings
MAIL_MAILER=smtp
MAIL_HOST=mail.deshsoft.net
MAIL_PORT=465
MAIL_USERNAME=sta@deshsoft.net
MAIL_PASSWORD="your-password-here"
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=sta@deshsoft.net
MAIL_FROM_NAME="STA"

# Queue Configuration - NO QUEUE NEEDED!
# Notifications are sent instantly (synchronous)
QUEUE_CONNECTION=sync
```

## Step 3: Run Migrations via cPanel Terminal

If you have Terminal access in cPanel:
```bash
cd public_html
php artisan migrate
```

If NO terminal access:
- Use phpMyAdmin to import the migration SQL manually
- Or ask hosting provider to run migrations

## Step 4: ✅ NO QUEUE WORKER NEEDED!

**IMPORTANT:** This notification system sends emails **instantly** when you click "Send Note".

❌ **You DO NOT need:**
- Cron jobs
- Queue workers
- Background processes

✅ **What happens:**
- STA Manager clicks "Send Note"
- Email is sent immediately
- Notification is saved to database instantly
- Company manager receives everything in real-time

**Why this is better:**
- No server configuration needed
- No cron job maintenance
- Works on ANY hosting (shared, VPS, cPanel)
- Instant delivery guaranteed
- Simpler deployment

## Step 5: Access Debug Page

After deployment, login as STA Manager and go to:

```
https://yourdomain.com/debug/notifications
```

This page will show you:
- ✅ Queue configuration (should show "sync" - instant mode)
- ✅ Mail settings
- ✅ Database tables status
- ✅ Company managers list
- ✅ Recent notifications
- ✅ Recent logs

## Step 6: Send Test Notification

On the debug page:
1. Select a company from dropdown
2. Click "Send Test Notification"
3. Check if company manager receives:
   - Email notification (should arrive within seconds)
   - In-app notification (bell icon)

## Troubleshooting on cPanel

### Issue: "No emails received"

**Check on debug page:**
1. Are company managers listed? (Check "Company Managers" section)
2. Are there error messages in logs? (Check "Recent Logs" section)
3. Is mail configuration correct? (Check "Mail Configuration" section)

**Solutions:**
- Check `.env` mail settings (host, port, username, password)
- Verify SMTP credentials with your hosting provider
- Test email settings using the debug page test button
- Check spam folder

### Issue: "Notifications not in database"

**Check:**
1. Has migration been run? (`company_notes` and `notifications` tables exist?)
2. Any errors in logs?

**Solution:**
- Run migrations via terminal or phpMyAdmin
- Check "Database Tables" section on debug page

### Issue: "Route [company-users.index] not defined"

**Solution:**
- Clear route cache:
  - Via terminal: `php artisan route:clear`
  - Or delete: `bootstrap/cache/routes-v7.php`

### Issue: "Email takes too long to send"

**This is normal!** Since we're sending instantly:
- SMTP connection takes 2-5 seconds
- Email delivery takes 5-10 seconds
- User will see a brief loading indicator
- But email is guaranteed to be sent

**If this is a problem:**
- You can switch back to queue mode
- But then you'll need cron job setup

## Recommended cPanel Settings

### File Manager Settings:
```
.env - 644
storage/ - 755
bootstrap/cache/ - 755
```

### PHP Version:
- Use PHP 8.1 or higher

### PHP Extensions Required:
- pdo_mysql
- mbstring
- xml
- curl
- zip
- gd
- fileinfo

## Production Checklist

Before going live:

- [ ] `.env` has correct database credentials
- [ ] `.env` has correct mail settings
- [ ] `.env` has `QUEUE_CONNECTION=sync`
- [ ] Migrations have been run
- [ ] Debug page shows no errors
- [ ] Test notification works
- [ ] Company managers exist with correct role
- [ ] Email arrives within 10 seconds

## Performance Notes

**Instant mode (sync) pros:**
- ✅ No cron job setup needed
- ✅ Guaranteed delivery
- ✅ Real-time notifications
- ✅ Works on shared hosting
- ✅ Simple deployment

**Instant mode (sync) cons:**
- ⏱️ User waits 5-10 seconds for email to send
- ⏱️ Page loading slightly slower

**When to use queue mode instead:**
- If you send notes to 10+ managers at once
- If SMTP is very slow (>15 seconds)
- If you have VPS with terminal access

## Security Note

**IMPORTANT:** After verifying everything works, remove debug routes from production:

1. Edit `routes/web.php`
2. Remove these lines:
```php
Route::get('/debug/notifications', ...);
Route::post('/debug/test-notification', ...);
```

3. Clear route cache:
```bash
php artisan route:clear
```

Or restrict to specific IPs for security team access.

## Support

If issues persist after checking debug page:
1. Take screenshot of debug page
2. Check "Recent Logs" section for error messages
3. Verify SMTP credentials with hosting provider
4. Test with a different email address

## Quick Comparison

| Feature | Queue Mode (Old) | Sync Mode (Current) |
|---------|-----------------|---------------------|
| Cron Job Required | ✅ YES | ❌ NO |
| Instant Delivery | After cron runs | ✅ Immediate |
| Setup Complexity | High | ✅ Low |
| Works on Shared Hosting | Sometimes | ✅ Always |
| User Experience | Fast page load | 5-10 sec wait |
| Deployment | Complex | ✅ Simple |

**Current mode: SYNC (Instant) - Best for most use cases!**
