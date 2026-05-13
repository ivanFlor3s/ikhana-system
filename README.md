# Ikhana

Monorepo containing the backend (Laravel) and frontend (Angular) for the Ikhana application.

## Structure

```
ikhana/
├── backend/          # Laravel API
│   ├── src/          # Laravel application code
│   ├── docker/       # Docker configuration
│   ├── docker-compose.yml
│   └── Dockerfile
└── frontend/         # Angular web application
    ├── src/          # Angular application code
    ├── angular.json
    └── package.json
```

## Getting Started

### Backend

```bash
cd backend/src
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### Frontend

```bash
cd frontend
npm install
npm run start
```

## Deployment

For now, deploy each service independently:

- **Backend**: `git pull` on the server, then run Laravel deploy commands
- **Frontend**: Build locally and deploy, or use CI/CD (planned)
