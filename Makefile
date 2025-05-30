# ---------------------------
# Backend targets (Symfony)
# ---------------------------

api.build:
	docker compose build php_api

api.sh:
	make up && docker compose exec -it php_api sh

api.phpunit:
	docker compose exec php_api bin/phpunit

api.fixtures:
	docker compose exec php_api bin/console doctrine:fixtures:load -n

api.db.recreate:
	docker compose exec php_api composer db:recreate:dev

api.migrate:
	docker compose exec php_api bin/console doctrine:migrations:migrate -n

api.install:
	docker compose run --entrypoint="composer" php_api install

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
	make api.build && docker compose build nginx_api

install:
	make api.install

make ics:
	bash generate-ics.sh
