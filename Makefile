# ---------------------------
# Backend targets (Symfony)
# ---------------------------

apibuild:
	docker compose build php_api

apish:
	make up && docker compose exec -it php_api sh

apiphpunit:
	docker compose exec php_api bin/phpunit

apifixtures:
	docker compose exec php_api bin/console doctrine:fixtures:load -n

apidb.recreate:
	docker compose exec php_api composer db:recreate:dev

apimigrate:
	docker compose exec php_api bin/console doctrine:migrations:migrate -n

apiinstall:
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
	make apibuild && docker compose build

install:
	make apiinstall

make ics:
	bash tools/generate-ics.sh
