#!/bin/bash

# STA Management System - Health Check Script
# This script checks the health of the application

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}STA Management System Health Check${NC}"
echo -e "${GREEN}========================================${NC}"

# Check function
check() {
    if eval "$2"; then
        echo -e "${GREEN}✓${NC} $1"
        return 0
    else
        echo -e "${RED}✗${NC} $1"
        return 1
    fi
}

# Variables to track overall health
HEALTHY=true

echo -e "\n${YELLOW}System Requirements:${NC}"
check "PHP Version >= 8.2" "php -v | grep -q 'PHP 8.[2-9]'"
check "Composer installed" "command -v composer &> /dev/null"
check "Node.js installed" "command -v node &> /dev/null"
check "NPM installed" "command -v npm &> /dev/null"

echo -e "\n${YELLOW}PHP Extensions:${NC}"
check "BCMath extension" "php -m | grep -q bcmath"
check "PDO extension" "php -m | grep -q pdo"
check "PDO MySQL extension" "php -m | grep -q pdo_mysql"
check "Mbstring extension" "php -m | grep -q mbstring"
check "OpenSSL extension" "php -m | grep -q openssl"
check "Tokenizer extension" "php -m | grep -q tokenizer"
check "XML extension" "php -m | grep -q xml"
check "cURL extension" "php -m | grep -q curl"

echo -e "\n${YELLOW}Application Status:${NC}"
check "Environment file exists" "[ -f .env ]"
check "APP_KEY is set" "grep -q '^APP_KEY=.\+' .env"
check "Storage directory writable" "[ -w storage ]"
check "Bootstrap cache writable" "[ -w bootstrap/cache ]"
check "Public storage link exists" "[ -L public/storage ]"

echo -e "\n${YELLOW}Database Connection:${NC}"
if php artisan tinker --execute="DB::connection()->getPdo();" &> /dev/null; then
    echo -e "${GREEN}✓${NC} Database connection successful"
else
    echo -e "${RED}✗${NC} Database connection failed"
    HEALTHY=false
fi

echo -e "\n${YELLOW}Migrations Status:${NC}"
PENDING_MIGRATIONS=$(php artisan migrate:status | grep "Pending" | wc -l)
if [ "$PENDING_MIGRATIONS" -eq 0 ]; then
    echo -e "${GREEN}✓${NC} All migrations are up to date"
else
    echo -e "${YELLOW}!${NC} There are $PENDING_MIGRATIONS pending migrations"
fi

echo -e "\n${YELLOW}Cache Status:${NC}"
check "Configuration cached" "[ -f bootstrap/cache/config.php ]"
check "Routes cached" "[ -f bootstrap/cache/routes-v7.php ]"
check "Views cached" "[ -d storage/framework/views ] && [ $(ls -1 storage/framework/views/*.php 2>/dev/null | wc -l) -gt 0 ]"

echo -e "\n${YELLOW}Asset Status:${NC}"
check "Assets built" "[ -f public/build/manifest.json ]"

echo -e "\n${YELLOW}Required Directories:${NC}"
check "User photos directory" "[ -d storage/app/public/user-photos ]"
check "Company logos directory" "[ -d storage/app/public/company-logos ]"
check "Certificates directory" "[ -d storage/app/public/certificates ]"

echo -e "\n${YELLOW}Application Info:${NC}"
php artisan about --only=environment

echo -e "\n${GREEN}========================================${NC}"
if [ "$HEALTHY" = true ]; then
    echo -e "${GREEN}✓ All health checks passed!${NC}"
    exit 0
else
    echo -e "${RED}✗ Some health checks failed${NC}"
    exit 1
fi