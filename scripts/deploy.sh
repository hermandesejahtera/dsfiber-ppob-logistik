#!/bin/bash

# DSFiber Deploy Script
# Deploys to production or staging

set -e

ENV=${1:-staging}
DEPLOY_USER=${DEPLOY_USER:-deploy}
DEPLOY_HOST=${DEPLOY_HOST:-localhost}
DEPLOY_PATH=${DEPLOY_PATH:-/var/www/dsfiber}

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo "${YELLOW}Deploying to $ENV environment...${NC}"

# Build locally first
echo "${YELLOW}Building application...${NC}"
bash scripts/build.sh

if [ $? -ne 0 ]; then
    echo "${RED}Build failed!${NC}"
    exit 1
fi

# Create deployment archive
echo "${YELLOW}Creating deployment archive...${NC}"
TARBALL="dsfiber-$(date +%Y%m%d-%H%M%S).tar.gz"
tar --exclude='.git' \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='.env' \
    --exclude='storage/*' \
    --exclude='tests' \
    -czf "$TARBALL" .

# Upload to server
echo "${YELLOW}Uploading to server...${NC}"
scp "$TARBALL" "$DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH/"

# Extract and setup on server
echo "${YELLOW}Extracting and setting up on server...${NC}"
ssh "$DEPLOY_USER@$DEPLOY_HOST" << EOF
    cd $DEPLOY_PATH
    tar -xzf "$TARBALL"
    composer install --no-dev --optimize-autoloader
    php bin/console migrate
    chown -R www-data:www-data .
    chmod -R 755 storage/
EOF

if [ $? -ne 0 ]; then
    echo "${RED}Deployment failed!${NC}"
    exit 1
fi

# Cleanup
echo "${YELLOW}Cleaning up...${NC}"
rm -f "$TARBALL"
ssh "$DEPLOY_USER@$DEPLOY_HOST" "rm -f $DEPLOY_PATH/$TARBALL"

echo "${GREEN}✓ Deployment to $ENV completed successfully!${NC}"
