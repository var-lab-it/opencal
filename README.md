# OpenCal

OpenCal is a modern web application combining a Symfony backend with a Vue.js frontend. The project is fully containerized using Docker and Docker Compose for streamlined local development and deployment.

## 🛠 Tech Stack

- **Backend**: PHP 8.x (Symfony), MySQL 8.2
- **Frontend**: Vue.js + TypeScript + Bootstrap
- **Containerization**: Docker & Docker Compose
- **Database**: MySQL
- **Mail Testing**: Mailpit
- **Dev Tools**: ESLint, npm audit, GitHub Actions CI

## 🚀 Local Development

### Prerequisites

- Docker
- Docker Compose
- Node.js 20 (optional for direct local frontend dev)

### Starting the Application

```bash
make up
```

**Frontend** → http://localhost:5173  
**Backend (Symfony)** → http://localhost:8080  
**Mailpit** → http://localhost:8025

## 📝 Common Commands

### 🐘 Backend (Symfony)

| Action                       | Command                                     |
| --------------------------- | ------------------------------------------- |
| Build PHP container         | `make backend.build`                        |
| Start project               | `make up`                                   |
| Open shell in backend       | `make backend.sh`                           |
| Run PHPUnit tests           | `make backend.phpunit`                      |
| Load fixtures               | `make backend.fixtures`                     |
| Recreate DB schema          | `make backend.db.recreate`                  |
| Run migrations              | `make backend.migrate`                      |
| Setup for E2E tests         | `make backend.setupe2e`                     |
| Install backend dependencies| `make backend.install`                      |
| Stop containers             | `make down`                                 |
| Show running containers     | `make ps`                                   |

**Direct Symfony Console Example**:

```bash
docker compose exec php_backend bin/console doctrine:migrations:migrate
```

### 🌐 Frontend (Vue.js)

| Action                  | Command                                      |
| ----------------------- | -------------------------------------------- |
| Build production image  | `make frontend.build`                        |
| Open development shell  | `make frontend.sh`                           |
| Install dependencies    | `make frontend.install`                      |
| Run ESLint              | `make frontend.lint`                         |
| Run security audit      | `make frontend.audit`                        |

**Dev server** starts automatically with `make up` and is available at [http://localhost:5173](http://localhost:5173).

## 🧪 Linting & Security

ESLint and npm audit checks are automatically run via GitHub Actions.

Manual checks:

```bash
make frontend.lint
make frontend.audit
```

## 📦 Production Build

The `frontend` service uses a **multi-stage Dockerfile** to produce a lightweight NGINX container for production.

```bash
make frontend.build
```

The Symfony backend uses a similar multi-stage Dockerfile for optimized production builds.

## 📝 Useful Commands

```bash
# Symfony console (example)
docker compose exec php_backend bin/console

# MySQL CLI
docker compose exec database mysql -u symfony -psymfony symfony
```

## 📧 Mailpit

All emails sent by the application will appear at [http://localhost:8025](http://localhost:8025).

## 🔧 Build All Services

```bash
make build
```

This builds the PHP backend, the NGINX server, and the frontend production image.

## 🤝 Contributing

Contributions are welcome! Please follow the existing code style and ensure all code passes linting and security checks.
