#!/bin/sh
set -e

if grep -q DATABASE_URL= .env; then
  echo "Waiting for dev/prod database to be ready..."
  ATTEMPTS_LEFT_TO_REACH_DATABASE=60
  until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || DATABASE_ERROR=$(php bin/console doctrine:database:create --if-not-exists 2>&1); do
    if [ $? -eq 255 ]; then
      # If the Doctrine command exits with 255, an unrecoverable error occurred
      ATTEMPTS_LEFT_TO_REACH_DATABASE=0
      break
    fi
    sleep 1
    ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))
    echo "Still waiting for dev/prod  database to be ready... Or maybe the database is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left."
  done

  if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
    echo "The dev/prod database is not up or not reachable:"
    echo "$DATABASE_ERROR"
    exit 1
  else
    echo "The dev/prod database is now ready and reachable"
  fi

  if ls -A migrations/*.php >/dev/null 2>&1; then
    php bin/console doctrine:migrations:migrate --no-interaction
  fi

  if grep -q '^APP_ENV=dev' .env; then
    echo "Loading fixtures in dev environment..."
    php bin/console doctrine:fixtures:load --no-interaction || echo "Fixtures could not be loaded."
  fi
fi

if [ -f ".env.test" ]; then
  echo "Waiting for test database to be ready..."
  if grep -q DATABASE_URL= .env.test; then
    php bin/console doctrine:database:create --env=test --if-not-exists
    if ls -A migrations/*.php >/dev/null 2>&1; then
      php bin/console doctrine:migrations:migrate --no-interaction --env=test
    fi
  else
    echo "The test database is not configured"
    exit 1
  fi
fi

php bin/console lexik:jwt:generate-keypair --overwrite -n

mkdir -p var
chown -R www-data var
mkdir -p tmp
chown -R www-data tmp

chmod -R 777 var

exec docker-php-entrypoint "$@"
