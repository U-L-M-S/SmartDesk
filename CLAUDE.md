# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**SmartDesk** is a modular business dashboard built with Laravel 10, providing support tickets, document management, notifications, and role-based access control. The application runs in Docker containers and uses a microservice-style modular architecture.

**🐳 Fully Dockerized**: Everything runs inside containers - no local PHP, Composer, or npm installation required. Works on Windows, macOS, and Linux.

### Tech Stack
- **Backend**: Laravel 10, PHP 8.2
- **Frontend**: Blade templates + TailwindCSS (Vite for asset bundling)
- **Database**: PostgreSQL 13
- **Cache/Queue**: Redis
- **Email Testing**: MailHog (development)
- **Containerization**: Docker Compose

## Development Commands

### Docker Environment

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View logs
docker-compose logs -f app

# Access application container
docker-compose exec app bash

# Access database
docker-compose exec db psql -U user -d smartdesk
```

### Initial Setup

**On Windows:**
```bash
# Quick setup - just run the batch file
setup.bat

# Or manually (CMD/PowerShell):
copy .env.example .env
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app npm install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan storage:link
```

**On Linux/macOS:**
```bash
# Quick setup - run the shell script
bash setup.sh

# Or run commands manually (same steps as Windows above, use 'cp' instead of 'copy')
```

### Laravel Artisan Commands

```bash
# Run migrations
docker-compose exec app php artisan migrate

# Run seeders
docker-compose exec app php artisan db:seed

# Clear caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Queue worker
docker-compose exec app php artisan queue:work redis --tries=3
```

### Testing

```bash
# Run all tests
docker-compose exec app php artisan test

# Run specific test suite
docker-compose exec app php artisan test --testsuite=Unit
docker-compose exec app php artisan test --testsuite=Feature

# Run single test file
docker-compose exec app php artisan test tests/Feature/TicketTest.php

# Run with coverage
docker-compose exec app php artisan test --coverage
```

### Code Quality

```bash
# Format code with Laravel Pint
docker-compose exec app ./vendor/bin/pint

# Check code style
docker-compose exec app ./vendor/bin/pint --test
```

### Frontend Assets

**All npm commands run inside the Docker container:**

```bash
# Install npm dependencies (first time only)
docker-compose exec app npm install

# Development mode with hot reload (keep this running in a separate terminal)
docker-compose exec app npm run dev

# Production build
docker-compose exec app npm run build
```

**Note**: When running `npm run dev`, the Vite dev server runs on port 5173 inside the container and is accessible at `http://localhost:5173` from your browser. The application at `http://localhost:8080` will automatically use the Vite dev server for hot module replacement.

## Architecture

### Modular Structure

The application uses a modular architecture where each business domain is isolated in the `modules/` directory:

```
modules/
├── Tickets/          # Support ticket system
│   ├── Controllers/
│   ├── Models/
│   └── (views would go here)
├── Documents/        # Document management with versioning
│   ├── Controllers/
│   ├── Models/
│   └── (views would go here)
└── Notifications/    # Notification system
    ├── Controllers/
    ├── Models/
    └── Notifications/
```

### Database Schema Overview

**Users & Permissions:**
- `users` - User accounts
- `roles` - Admin, Manager, Mitarbeiter (Employee)
- `permissions` - Granular module permissions (tickets.view, documents.edit, etc.)
- `role_user` - User-Role associations
- `permission_role` - Role-Permission associations

**Tickets Module:**
- `tickets` - Support tickets with status tracking
- `ticket_comments` - Comments on tickets
- `ticket_status_history` - Audit trail of status changes

**Documents Module:**
- `documents` - Main document records
- `document_versions` - Version history for each document
- `document_tags` - Tags for categorization
- `document_tag` - Document-Tag pivot
- `document_shares` - Share documents with specific users

**Notifications:**
- `notifications` - Laravel's standard notification table
- `system_notifications` - System-wide announcements

### Key Models and Relationships

**User Model** (`App\Models\User`):
- Has many roles (many-to-many)
- Methods: `hasRole(string)`, `hasPermission(string)`, `assignRole()`, `removeRole()`

**Ticket Model** (`Modules\Tickets\Models\Ticket`):
- Belongs to creator (User)
- Belongs to assignee (User, nullable)
- Has many comments and status history
- Methods: `changeStatus()`, `assignTo()`, scopes: `open()`, `inProgress()`, `closed()`

**Document Model** (`Modules\Documents\Models\Document`):
- Belongs to uploader (User)
- Has many versions
- Has many tags (many-to-many)
- Has many shares
- Methods: `createVersion()`, `shareWith()`, `canAccess(User)`, scope: `accessibleBy(User)`

### Middleware

Custom middleware aliases registered in `bootstrap/app.php`:
- `role:admin,manager` - Check if user has any of the specified roles
- `permission:tickets.view` - Check if user has specific permission

Usage in routes:
```php
Route::get('/admin', Controller@method)->middleware('role:admin');
Route::post('/tickets', Controller@store)->middleware('permission:tickets.create');
```

### Queue System

Configured to use Redis as the default queue driver (`config/queue.php`).

Notifications implement `ShouldQueue` interface and are processed asynchronously:
- `TicketCreatedNotification`
- `TicketAssignedNotification`
- `DocumentSharedNotification`

Run queue worker:
```bash
docker-compose exec app php artisan queue:work redis
```

### API Authentication

Uses Laravel Sanctum for API token authentication. API routes are in `routes/api.php` and protected by `auth:sanctum` middleware.

## What Has Been Completed

### ✅ Core Infrastructure
- Docker setup with PHP-FPM, Nginx, PostgreSQL, Redis, MailHog
- Laravel 10 base installation with Vite + TailwindCSS
- Environment configuration

### ✅ User & Role Management
- Database migrations for users, roles, permissions
- Many-to-many relationships between users-roles-permissions
- User, Role, Permission models with helper methods
- Role middleware and Permission middleware
- Seeders creating 3 roles (Admin, Manager, Mitarbeiter) with granular permissions
- Demo users for each role (password: `password`)

### ✅ Tickets Module
- Full CRUD for tickets
- Ticket assignment to agents
- Status workflow (open → in_progress → closed)
- Comments on tickets (with internal/external flag)
- Status history tracking
- Controllers: `TicketController`, `TicketCommentController`

### ✅ Documents Module
- File upload with metadata
- Version control for documents
- Tagging system
- Share documents with specific users (view/edit/manage permissions)
- Access control based on ownership, public flag, or shares
- Controller: `DocumentController`

### ✅ Notifications Module
- Laravel notification system integrated
- System-wide announcement notifications
- Queued email and database notifications
- Notification classes for ticket and document events
- Controllers: `NotificationController`, `SystemNotificationController`

### ✅ Queue Configuration
- Redis queue driver configured
- Notifications queued for background processing

### ✅ Routes
- Web routes with middleware protection
- API routes with Sanctum authentication

## What Still Needs to Be Done

### 🔴 Authentication System
- Install Laravel Breeze or Fortify for login/register/logout
- Email verification
- Password reset functionality
- User invitation system

### 🔴 Frontend Views
All Blade views need to be created:
- Dashboard homepage with widgets
- Tickets module views (index, create, edit, show)
- Documents module views (index, create, edit, show)
- Notifications views (index, notification list)
- System notifications views (admin interface)
- User management views (admin)
- Layout templates with navigation

### 🔴 Dashboard & Widgets
- Widget system for dashboard
- Widgets showing:
  - Recent tickets
  - Active users count
  - New documents
  - Notification center
- Customizable dashboard per user (save widget preferences)

### 🔴 Policies
Create Laravel Policies for authorization:
- `TicketPolicy` for ticket actions
- `DocumentPolicy` for document actions
- `UserPolicy` for user management
- Register policies in `AuthServiceProvider`

### 🔴 File Storage
- Configure storage disk for documents (currently using local)
- Optional: Integrate S3 or MinIO for production file storage
- File size limits and validation
- Allowed MIME types configuration

### 🔴 Tests
- Unit tests for models (User, Ticket, Document)
- Feature tests for API endpoints
- Test database seeders
- Test role and permission checks

### 🔴 Additional Features (from PROMPT.md)
- Audit trail for GDPR compliance (who changed what, when)
- Export functionality (CSV, PDF) for tickets and documents
- Multi-language support (EN/DE)
- OAuth integration (Google, GitHub login)
- Dashboard widget customization UI
- Search functionality for tickets and documents

### 🔴 CI/CD Pipeline
- GitHub Actions or Drone CI configuration
- Automated testing on PRs
- Docker image building
- Deployment automation

### 🔴 Production Readiness
- Environment variable validation
- Logging configuration
- Error handling and user-friendly error pages
- Rate limiting for API endpoints
- CORS configuration for API
- Database backups strategy
- Queue supervisor/monitoring

## Access Information

### Service URLs
- **Application**: http://localhost:8080
- **MailHog Web UI**: http://localhost:8025
- **PostgreSQL**: localhost:5432
- **Redis**: localhost:6379

### Demo Credentials
After running seeders (`php artisan db:seed`):
- **Admin**: admin@smartdesk.com / password
- **Manager**: manager@smartdesk.com / password
- **Employee**: employee@smartdesk.com / password

## Important Notes

- **Everything runs in Docker**: No need to install PHP, Composer, or npm locally
- **Windows Compatible**: Use `setup.bat` for initial setup on Windows
- **Cross-platform**: Same Docker commands work on Windows, macOS, and Linux
- The `modules/` directory is autoloaded via PSR-4 in `composer.json`
- Middleware aliases are registered in `bootstrap/app.php`
- Queue jobs must be processed with `php artisan queue:work redis`
- MailHog catches all outgoing emails in development (check localhost:8025)
- File uploads are stored in `storage/app/documents/` by default
- Soft deletes are enabled on users, tickets, and documents

### Windows-Specific Notes
- Docker Desktop for Windows must be running
- If using PowerShell, you may need to allow script execution: `Set-ExecutionPolicy RemoteSigned -Scope CurrentUser`
- File paths in Windows use backslashes, but Docker uses forward slashes (Docker handles this automatically)
- The Vite dev server (for frontend hot reload) runs inside the container on port 5173

## Common Development Tasks

### Creating a New Module
1. Create directory structure in `modules/YourModule/`
2. Add namespace to `composer.json` autoload if needed
3. Create migrations, models, controllers
4. Add routes to `routes/web.php` and `routes/api.php`
5. Create permissions in `PermissionSeeder`

### Adding New Permissions
1. Add to `PermissionSeeder.php`
2. Re-run seeder: `php artisan db:seed --class=PermissionSeeder`
3. Assign to roles as needed

### Working with Notifications
1. Create notification class in `modules/Notifications/Notifications/`
2. Implement `via()`, `toMail()`, `toArray()` methods
3. Use `ShouldQueue` interface for async processing
4. Dispatch: `$user->notify(new YourNotification($data))`
