@echo off
echo Setting up SmartDesk on Windows...
echo.

REM Check if .env exists
if not exist .env (
    echo Creating .env file...
    copy .env.example .env
    echo .env file created
) else (
    echo .env file already exists
)

echo.
echo Starting Docker containers...
docker-compose up -d

echo.
echo Waiting for containers to be ready...
timeout /t 5 /nobreak >nul

echo.
echo Installing Composer dependencies...
docker-compose exec app composer install

echo.
echo Installing npm dependencies...
docker-compose exec app npm install

echo.
echo Generating application key...
docker-compose exec app php artisan key:generate

echo.
echo Creating storage link...
docker-compose exec app php artisan storage:link

echo.
echo Running migrations and seeders...
docker-compose exec app php artisan migrate --seed

echo.
echo.
echo ========================================
echo SmartDesk setup complete!
echo ========================================
echo.
echo Access the application at: http://localhost:8080
echo Access MailHog at: http://localhost:8025
echo.
echo Demo Credentials:
echo   Admin: admin@smartdesk.com / password
echo   Manager: manager@smartdesk.com / password
echo   Employee: employee@smartdesk.com / password
echo.
echo To start queue worker:
echo   docker-compose exec app php artisan queue:work redis
echo.
pause
