#!/bin/bash
# Setup script for SkinRec Application

echo "=========================================="
echo "SkinRec Database Setup Script"
echo "=========================================="
echo ""

# Change to project directory
cd "$(dirname "$0")" || exit

echo "Step 1: Installing dependencies..."
composer install

echo ""
echo "Step 2: Generating application key..."
php artisan key:generate

echo ""
echo "Step 3: Creating database and running migrations..."
php artisan migrate:fresh --seed

echo ""
echo "=========================================="
echo "Setup Complete!"
echo "=========================================="
echo ""
echo "To start the development server, run:"
echo "  php artisan serve"
echo ""
echo "Then visit http://localhost:8000 in your browser"
