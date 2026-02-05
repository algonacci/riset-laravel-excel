# Omniflow Laravel Starter

A production-ready Laravel starter kit with Filament admin panel, designed for stateless/load-balancer-ready deployments.

## Tech Stack

- **PHP 8.5+**
- **Laravel 12** - Framework
- **Livewire 4** - Full-stack components
- **Filament 5** - Admin panel
- **SQLite** - Default database (production-ready for shared database)

## Features

- Modern Filament 5 admin panel with custom auth guard
- Table prefix isolation (`laravel_cms_*`) for shared database environments
- Stateless architecture - ready for horizontal scaling behind load balancers
- Pre-configured for Redis session/cache, S3 storage, and RabbitMQ queues
- Health check endpoint for load balancer health probes

## Requirements

- PHP 8.5 or higher
- Composer
- Node.js & NPM
- Docker & Docker Compose
- SQLite (default) or MySQL/PostgreSQL

## Installation

1. Clone the repository:
```bash
git clone <repository-url> omniflow-starter
cd omniflow-starter
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy environment file and configure:
```bash
cp .env.example .env
php artisan key:generate
```

4. Setup database:
```bash
php artisan migrate --force
```

5. Install frontend dependencies:
```bash
npm install
npm run build
```

6. Start the development server:
```bash
composer dev
```

## Docker Development

Start the application with Docker:

```bash
docker compose up -d
```

Access the app at http://localhost

### Useful Commands

```bash
# View logs
docker compose logs -f php

# Run artisan commands
docker compose exec php php artisan migrate

# Restart services
docker compose restart php
```

## Available Scripts

| Command | Description |
|---------|-------------|
| `composer setup` | Full project setup (install deps, migrate, build assets) |
| `composer dev` | Start dev server with concurrent processes |
| `composer lint` | Run Pint linter |
| `composer test` | Run tests with linting |
| `php artisan pail` | View logs in real-time |

## Production Deployment

For stateless/load balancer deployments, ensure these configurations in `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:SAME_KEY_ON_ALL_SERVERS

# Session & Cache (Redis for stateless)
SESSION_DRIVER=redis
CACHE_DRIVER=redis

# Queue (RabbitMQ or Redis)
QUEUE_CONNECTION=rabbitmq

# Storage (S3 for distributed access)
FILESYSTEM_DISK=s3

# Logging
LOG_CHANNEL=stderr
```

See [INSTRUKSI.md](./INSTRUKSI.md) for detailed production deployment guide.

## Project Structure

```
app/
├── Filament/Resources/     # Filament resources and pages
├── Models/                 # Eloquent models
└── Providers/              # Service providers
database/
├── migrations/             # Database migrations
└── seeders/                # Database seeders
routes/
├── web.php                 # Web routes
└── console.php             # Console routes
```

## Security

For security vulnerabilities, please review the [security policy](https://github.com/laravel/laravel/security/policy) from the Laravel framework.

## License

MIT License
