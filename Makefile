test:
	php artisan test

lint:
	composer exec phpcs -v

lint-fix:
	composer exec phpcbf -v

analyse:
	composer exec phpstan analyse -v

config-clear:
	php artisan config:clear

env-prepare:
	cp -n .env.example .env || true

key:
	php artisan key:generate

ide-helper:
	php artisan ide-helper:eloquent
	php artisan ide-helper:gen
	php artisan ide-helper:meta
	php artisan ide-helper:mod -n

build:
	composer install --no-interaction --ansi --no-suggest
	php artisan migrate --force
	php artisan db:seed --force
	php artisan optimize

build-docker:
	docker-compose exec php composer install --no-interaction --ansi --no-suggest
	docker-compose exec php php artisan migrate --force
	docker-compose exec php php artisan db:seed --force
	docker-compose exec php php artisan optimize

heroku-build:
	php artisan migrate --force
	php artisan db:seed --force
	php artisan optimize

ci:
	docker-compose -f docker-compose.ci.yml -p ci up -d --build
#	docker-compose up -d --build
	docker-compose exec php composer install --no-interaction --ansi --no-suggest
	docker-compose exec php php artisan migrate --force
	docker-compose exec php php artisan db:seed --force
	docker-compose exec php php artisan optimize
	docker-compose exec php composer exec phpcs -v
	docker-compose exec php php artisan test
