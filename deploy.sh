#!/bin/bash

# Auto-deploy script for webhook-auto-deploy project
echo "[$(date)] Starting deployment process..."

# Get the current directory
DEPLOY_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
echo "[$(date)] Deploy directory: $DEPLOY_DIR"

# Change to the project directory
cd "$DEPLOY_DIR"

# Set up logging (only if on server with proper directory structure)
LOG_DIR="$DEPLOY_DIR/storage/logs"
if [ -d "$LOG_DIR" ]; then
    LOG_FILE="$LOG_DIR/deploy.log"
    echo "[$(date)] Logging to: $LOG_FILE"
else
    LOG_FILE="/dev/null"
    echo "[$(date)] No log directory found, logging disabled"
fi

# Function to log messages
log_message() {
    echo "[$(date)] $1"
    echo "[$(date)] $1" >> "$LOG_FILE" 2>/dev/null || true
}

# Pull latest changes (if this is a git repository)
if [ -d ".git" ]; then
    log_message "Pulling latest changes from git..."
    git pull origin main 2>/dev/null || git pull origin master 2>/dev/null || log_message "Git pull failed or not needed"
fi

# Install/update dependencies
if [ -f "composer.json" ]; then
    log_message "Installing/updating composer dependencies..."
    
    # Try to find composer
    if command -v composer >/dev/null 2>&1; then
        composer install --no-dev --optimize-autoloader 2>/dev/null || log_message "Composer install failed"
    else
        log_message "Composer not found, skipping dependency installation"
    fi
fi

# Find PHP binary - improved detection
PHP_BINARY=""

# First, try the which command
if command -v php >/dev/null 2>&1; then
    PHP_BINARY=$(which php)
    log_message "Found PHP using 'which': $PHP_BINARY"
elif [ -x "/usr/bin/php" ]; then
    PHP_BINARY="/usr/bin/php"
    log_message "Found PHP at: $PHP_BINARY"
elif [ -x "/usr/local/bin/php" ]; then
    PHP_BINARY="/usr/local/bin/php"
    log_message "Found PHP at: $PHP_BINARY"
elif [ -x "/opt/cpanel/ea-php82/root/usr/bin/php" ]; then
    PHP_BINARY="/opt/cpanel/ea-php82/root/usr/bin/php"
    log_message "Found PHP at: $PHP_BINARY"
elif [ -x "/opt/cpanel/ea-php81/root/usr/bin/php" ]; then
    PHP_BINARY="/opt/cpanel/ea-php81/root/usr/bin/php"
    log_message "Found PHP at: $PHP_BINARY"
elif [ -x "/opt/cpanel/ea-php80/root/usr/bin/php" ]; then
    PHP_BINARY="/opt/cpanel/ea-php80/root/usr/bin/php"
    log_message "Found PHP at: $PHP_BINARY"
else
    # Try PATH-based search more thoroughly
    for path in $(echo $PATH | tr ':' '\n'); do
        if [ -x "$path/php" ]; then
            PHP_BINARY="$path/php"
            log_message "Found PHP in PATH: $PHP_BINARY"
            break
        fi
    done
fi

if [ -z "$PHP_BINARY" ]; then
    log_message "Warning: PHP binary not found, using 'php' command"
    PHP_BINARY="php"
fi

log_message "Using PHP binary: $PHP_BINARY"

# Test PHP binary
if ! "$PHP_BINARY" --version >/dev/null 2>&1; then
    log_message "Error: PHP binary is not working: $PHP_BINARY"
    # Fallback to just 'php'
    PHP_BINARY="php"
    log_message "Falling back to: $PHP_BINARY"
fi

# Clear Laravel caches
if [ -f "artisan" ]; then
    log_message "Clearing Laravel caches..."
    "$PHP_BINARY" artisan config:clear 2>/dev/null || log_message "Config clear failed"
    "$PHP_BINARY" artisan cache:clear 2>/dev/null || log_message "Cache clear failed"
    "$PHP_BINARY" artisan route:clear 2>/dev/null || log_message "Route clear failed"
    "$PHP_BINARY" artisan view:clear 2>/dev/null || log_message "View clear failed"
fi

# Run migrations (if needed)
if [ -f "artisan" ]; then
    log_message "Running database migrations..."
    "$PHP_BINARY" artisan migrate --force 2>/dev/null || log_message "Migrations failed or not needed"
fi

log_message "Deployment completed successfully!"
echo "[$(date)] Deployment completed successfully!"
