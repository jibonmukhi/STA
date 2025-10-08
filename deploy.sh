#!/bin/bash

# STA Management System - Deployment Script
# Usage: ./deploy.sh [environment]
# Example: ./deploy.sh production

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
ENVIRONMENT=${1:-production}
APP_PATH="/var/www/sta"

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}STA Management System Deployment${NC}"
echo -e "${GREEN}Environment: ${YELLOW}$ENVIRONMENT${NC}"
echo -e "${GREEN}========================================${NC}"

# Function to print steps
step() {
    echo -e "\n${YELLOW}→ $1${NC}"
}

# Function to print success
success() {
    echo -e "${GREEN}✓ $1${NC}"
}

# Function to print error and exit
error() {
    echo -e "${RED}✗ $1${NC}"
    exit 1
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
   error "Please run as root or with sudo"
fi

# Navigate to application directory
step "Navigating to application directory"
cd $APP_PATH || error "Failed to navigate to $APP_PATH"
success "In directory: $(pwd)"

# Enable maintenance mode
step "Enabling maintenance mode"
php artisan down --message="System upgrade in progress" --retry=60 || error "Failed to enable maintenance mode"
success "Maintenance mode enabled"

# Pull latest code (if using git)
if [ -d ".git" ]; then
    step "Pulling latest code from repository"
    git pull origin main || error "Failed to pull latest code"
    success "Code updated"
fi

# Install/update composer dependencies
step "Installing PHP dependencies"
composer install --optimize-autoloader --no-dev --no-interaction || error "Failed to install composer dependencies"
success "PHP dependencies installed"

# Install/update NPM dependencies and build assets
step "Installing Node dependencies"
npm ci || error "Failed to install npm dependencies"
success "Node dependencies installed"

step "Building assets"
npm run build || error "Failed to build assets"
success "Assets built successfully"

# Run database migrations
step "Running database migrations"
php artisan migrate --force || error "Failed to run migrations"
success "Database migrations completed"

# Clear all caches
step "Clearing caches"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
success "Caches cleared"

# Rebuild caches
step "Optimizing application"
php artisan config:cache || error "Failed to cache config"
php artisan route:cache || error "Failed to cache routes"
php artisan view:cache || error "Failed to cache views"
php artisan event:cache || error "Failed to cache events"
success "Application optimized"

# Create storage link if it doesn't exist
step "Checking storage link"
if [ ! -L "public/storage" ]; then
    php artisan storage:link || error "Failed to create storage link"
    success "Storage link created"
else
    success "Storage link already exists"
fi

# Set correct permissions
step "Setting file permissions"
chown -R www-data:www-data $APP_PATH
find $APP_PATH -type d -exec chmod 755 {} \;
find $APP_PATH -type f -exec chmod 644 {} \;
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod 600 .env
success "Permissions set correctly"

# Restart queue workers if using supervisor
if command -v supervisorctl &> /dev/null; then
    step "Restarting queue workers"
    supervisorctl restart sta-worker:* || echo "Queue workers not configured"
fi

# Clear OPcache if PHP-FPM is running
if command -v service &> /dev/null; then
    step "Restarting PHP-FPM to clear OPcache"
    service php8.2-fpm reload || service php-fpm reload || echo "PHP-FPM reload skipped"
fi

# Run health check
step "Running health check"
php artisan about || error "Health check failed"
success "Application healthy"

# Disable maintenance mode
step "Disabling maintenance mode"
php artisan up || error "Failed to disable maintenance mode"
success "Maintenance mode disabled"

# Final success message
echo -e "\n${GREEN}========================================${NC}"
echo -e "${GREEN}✓ Deployment completed successfully!${NC}"
echo -e "${GREEN}========================================${NC}"
echo -e "\n${YELLOW}Post-deployment tasks:${NC}"
echo -e "  1. Test the application at your domain"
echo -e "  2. Check error logs for any issues"
echo -e "  3. Verify email functionality"
echo -e "  4. Test file uploads"
echo -e "  5. Confirm audit logging is working"

# Optional: Send notification
# curl -X POST https://slack.webhook.url -d '{"text":"STA deployment completed successfully"}'

exit 0