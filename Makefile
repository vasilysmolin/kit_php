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

heroku-build:
	php artisan migrate --force
	php artisan db:seed --force
	php artisan optimize
