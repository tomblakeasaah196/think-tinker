#!/bin/bash
# deploy.sh
# 
# Manual Deployment Script for cPanel (Alternative to GitHub Actions FTP)
# Usage:
# 1. Place this script in a secured location (e.g., above public_html)
# 2. Configure a webhook in cPanel or execute manually via SSH.

# Configuration
REPO_URL="https://github.com/tomblakeasaah196/think-tinker.git"
BRANCH="main"
TARGET_DIR="/home/YOUR_CPANEL_USERNAME/public_html" # Update this
TMP_DIR="/home/YOUR_CPANEL_USERNAME/tmp_repo" # Update this

echo "🚀 Starting Deployment..."

# Create a temporary directory to clone the repo if it doesn't exist
if [ ! -d "$TMP_DIR" ]; then
    echo "Cloning repository..."
    git clone --branch $BRANCH $REPO_URL $TMP_DIR
else
    echo "Pulling latest changes..."
    cd $TMP_DIR
    git fetch origin
    git reset --hard origin/$BRANCH
fi

# Sync files from the temporary repo to the live public_html folder using rsync
# We exclude files that shouldn't be on the live server.
echo "Syncing files to public_html..."
rsync -avz --exclude='.git*' --exclude='scripts' --exclude='.github' --exclude='README.md' $TMP_DIR/public_html/ $TARGET_DIR/

# Optional: Fix permissions if needed
# chown -R YOUR_CPANEL_USERNAME:YOUR_CPANEL_USERNAME $TARGET_DIR
# find $TARGET_DIR -type d -exec chmod 755 {} \;
# find $TARGET_DIR -type f -exec chmod 644 {} \;

echo "✅ Deployment Complete!"
