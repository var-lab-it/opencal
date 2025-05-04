# ---------------------------
# Backend targets (Symfony)
# ---------------------------

backend.build:
	docker compose build php_backend

backend.sh:
	make up && docker compose exec -it php_backend sh

backend.phpunit:
	docker compose exec php_backend bin/phpunit

backend.fixtures:
	docker compose exec php_backend bin/console doctrine:fixtures:load -n

backend.db.recreate:
	docker compose exec php_backend composer db:recreate:dev

backend.migrate:
	docker compose exec php_backend bin/console doctrine:migrations:migrate -n

backend.setupe2e:
	make backend.migrate && make backend.fixtures

backend.install:
	docker compose run --rm php_backend composer install

# ---------------------------
# Frontend targets (Vue.js)
# ---------------------------

frontend.build:
	docker build -f frontend/Dockerfile --target production -t opencal-frontend:latest frontend

frontend.sh:
	docker compose exec -it frontend sh

frontend.install:
	docker compose run --rm frontend npm ci

frontend.lint:
	docker compose exec frontend npm run lint

frontend.audit:
	docker compose exec frontend npm audit --audit-level=low

# ---------------------------
# Common targets
# ---------------------------

up:
	docker compose up -d

down:
	docker compose down

ps:
	docker compose ps

build:
	make backend.build && docker compose build nginx_backend && make frontend.build

install:
	make backend.install && make frontend.install
