@echo off
REM Setup script for SkinRec Application (Windows)

echo ==========================================
echo SkinRec Database Setup Script
echo ==========================================
echo.

echo Step 1: Installing dependencies...
call composer install

echo.
echo Step 2: Generating application key...
php artisan key:generate

echo.
echo Step 3: Creating database and running migrations...
php artisan migrate:fresh --seed

echo.
echo ==========================================
echo Setup Complete!
echo ==========================================
echo.
echo To start the development server, run:
echo   php artisan serve
echo.
echo Then visit http://localhost:8000 in your browser
pause
