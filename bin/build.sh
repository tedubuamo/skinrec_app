#!/bin/bash
set -e

echo "Deploying application..."

# Run migrations
php artisan migrate --force

echo "Application deployed successfully!"
