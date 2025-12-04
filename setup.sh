#!/bin/bash

echo "Setting up SmartDesk..."

# Create .env file
if [ ! -f .env ]; then
    cp .env.example .env
    echo ".env file created"
fi

# Install composer dependencies
docker run --rm -v $(pwd):/app composer:latest install

# Generate application key
docker-compose up -d
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan storage:link

# Run migrations
docker-compose exec app php artisan migrate --seed

echo "SmartDesk setup complete!"
echo "Access the application at http://localhost:8080"
echo "Access MailHog at http://localhost:8025"
