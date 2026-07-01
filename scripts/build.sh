#!/bin/bash

# DSFiber Build Script
# Builds the application for production

set -e

echo "🔨 Building DSFiber PPOB & Logistik System..."

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check PHP version
echo "${YELLOW}Checking PHP version...${NC}"
PHP_VERSION=$(php -v | head -n 1 | awk '{print $2}')
echo "PHP Version: $PHP_VERSION"

# Install dependencies
echo "${YELLOW}Installing composer dependencies...${NC}"
composer install --no-dev --optimize-autoloader

# Clear cache
echo "${YELLOW}Clearing application cache...${NC}"
rm -rf storage/cache/*
rm -rf storage/logs/*

# Run database migrations
echo "${YELLOW}Running database migrations...${NC}"
if [ -f "bin/console" ]; then
    php bin/console migrate
else
    echo "${RED}Migration script not found${NC}"
    exit 1
fi

# Run tests
echo "${YELLOW}Running tests...${NC}"
vendor/bin/phpunit --configuration=phpunit.xml

if [ $? -ne 0 ]; then
    echo "${RED}Tests failed!${NC}"
    exit 1
fi

# Build assets
echo "${YELLOW}Building assets...${NC}"
if command -v npm &> /dev/null; then
    npm install
    npm run build
fi

echo "${GREEN}✓ Build completed successfully!${NC}"
echo "${GREEN}Ready for deployment${NC}"
