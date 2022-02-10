### api from tapigo:

[comment]: <> ([![Actions Status]&#40;https://github.com/vasilysmolin/php-project-lvl3/workflows/hexlet-check/badge.svg&#41;]&#40;https://github.com/vasilysmolin/php-project-lvl3/actions&#41;)

[comment]: <> ([![CI]&#40;https://github.com/vasilysmolin/php-project-lvl3/workflows/linter/badge.svg&#41;]&#40;https://github.com/vasilysmolin/php-project-lvl3/actions&#41;)

[comment]: <> ([![Maintainability]&#40;https://api.codeclimate.com/v1/badges/b8b2e46d966ad5a5ac36/maintainability&#41;]&#40;https://codeclimate.com/github/vasilysmolin/php-project-lvl3/maintainability&#41;)

[comment]: <> ([![Test Coverage]&#40;https://api.codeclimate.com/v1/badges/b8b2e46d966ad5a5ac36/test_coverage&#41;]&#40;https://codeclimate.com/github/vasilysmolin/php-project-lvl3/test_coverage&#41;)

# About Project

api from tapigo

## Требования

* PHP >= 7.4
* Composer >= 2
* make >= 4

## Install and start project
* `host write 127.0.0.1 tapigol.ru`

* `добавить сертификаты в nginx`

* `проверить env файл`
- uid пользователя должен соответствовать uid текущего пользователя в системе
- проверить ssl сертификаты и среду. Место положение сертификатов зависит от среды. Это касает dev и prod режима. кально делаются самоподписанные сертификаты и кладуться сами.

* `docker-compose up -d --build`

* Заседировать данные `make seeder`

* Парсер пользователей `docker-compose exec php sh and php artisan user-parse`

* Сбросить ключи в postgress `docker-compose exec database sh`

  SELECT setval(pg_get_serial_sequence('jobs_vacancies', 'id'), coalesce(max(id)+1, 1), false) FROM jobs_vacancies;

  SELECT setval(pg_get_serial_sequence('users', 'id'), coalesce(max(id)+1, 1), false) FROM users;

  SELECT setval(pg_get_serial_sequence('jobs_resumes', 'id'), coalesce(max(id)+1, 1), false) FROM jobs_resumes;

  SELECT setval(pg_get_serial_sequence('services', 'id'), coalesce(max(id)+1, 1), false) FROM services;

  SELECT setval(pg_get_serial_sequence('service_categories', 'id'), coalesce(max(id)+1, 1), false) FROM service_categories;

  SELECT setval(pg_get_serial_sequence('profiles', 'id'), coalesce(max(id)+1, 1), false) FROM profiles;


#basic auth from dev
ktotam
eto_tapigo


## Tests and lint

* `make lint`
* `make lint-fix`
* `make test`
* `test-coverage`

## Site
[project](https://tapigo.ru)

