# OpenCal – Open Source Appointment Scheduling Platform

OpenCal is a modern, open-source web application designed for effortless appointment scheduling. Inspired by platforms
like Calendly and cal.com, it empowers individuals, small businesses, and teams to manage bookings efficiently.

## Configuration

Both backend and frontend Docker containers can be customized using environment variables.

### Frontend Environment Variables

| Variable            | Description                                           | Default Value           |
|---------------------|-------------------------------------------------------|-------------------------|
| `VITE_APP_LANGUAGE` | Defines the default language/locale for the frontend. | `en_GB`                 |
| `VITE_API_URL`      | Specifies the backend API's base URL.                 | `http://localhost:8080` |

## Local Development

### Prerequisites

- Docker
- Docker Compose

### Pull the repository

```bash
git clone git@github.com:var-lab-it/opencal.git
```

### Starting the Application

```bash
make up
```

**Frontend** → http://localhost
**Backend (API docs)** → http://localhost:8080  
**Mailpit** → http://localhost:8025

## Common Commands

### All

| Action                       | Command                    |
|------------------------------|----------------------------|
| Stop containers              | `make down`                |
| Show running containers      | `make ps`                  |

### Backend (Symfony)

| Action                       | Command                    |
|------------------------------|----------------------------|
| Start project                | `make up`                  |
| Build PHP container          | `make backend.build`       |
| Open shell in backend        | `make backend.sh`          |
| Run PHPUnit tests            | `make backend.phpunit`     |
| Load fixtures                | `make backend.fixtures`    |
| Recreate DB schema           | `make backend.db.recreate` |
| Run migrations               | `make backend.migrate`     |
| Install backend dependencies | `make backend.install`     |

**Direct Symfony Console Example**:

```bash
docker compose exec php_backend bin/console doctrine:migrations:migrate
```

### Frontend (Vue.js)

| Action                 | Command                 |
|------------------------|-------------------------|
| Build production image | `make frontend.build`   |
| Open development shell | `make frontend.sh`      |
| Install dependencies   | `make frontend.install` |
| Run ESLint             | `make frontend.lint`    |
| Run security audit     | `make frontend.audit`   |

**Dev server** starts automatically with `make up` and is available at [http://localhost](http://localhost).

## Linting & Security

ESLint and npm audit checks are automatically run via GitHub Actions.

Manual checks:

```bash
make frontend.lint
make frontend.audit
```

## Production Build

The `frontend` service uses a **multi-stage Dockerfile** to produce a lightweight NGINX container for production.

```bash
make frontend.build
```

The Symfony backend uses a similar multi-stage Dockerfile for optimized production builds.

## Useful Commands

```bash
# Symfony console (example)
docker compose exec php_backend bin/console

# MySQL CLI
docker compose exec database mysql -u symfony -psymfony symfony
```

## Mailpit

All emails sent by the application will appear at [http://localhost:8025](http://localhost:8025).

## 🔧 Build All Services

```bash
make build
```

This builds the PHP backend, the NGINX server, and the frontend production image.

## 🤝 Contributing

Contributions are welcome! Please follow the existing code style and ensure all code passes linting and security checks.
