### api from tapigo:

[comment]: <> ([![Actions Status]&#40;https://github.com/vasilysmolin/php-project-lvl3/workflows/hexlet-check/badge.svg&#41;]&#40;https://github.com/vasilysmolin/php-project-lvl3/actions&#41;)

[comment]: <> ([![CI]&#40;https://github.com/vasilysmolin/php-project-lvl3/workflows/linter/badge.svg&#41;]&#40;https://github.com/vasilysmolin/php-project-lvl3/actions&#41;)

[comment]: <> ([![Maintainability]&#40;https://api.codeclimate.com/v1/badges/b8b2e46d966ad5a5ac36/maintainability&#41;]&#40;https://codeclimate.com/github/vasilysmolin/php-project-lvl3/maintainability&#41;)

[comment]: <> ([![Test Coverage]&#40;https://api.codeclimate.com/v1/badges/b8b2e46d966ad5a5ac36/test_coverage&#41;]&#40;https://codeclimate.com/github/vasilysmolin/php-project-lvl3/test_coverage&#41;)

# About Project

api from project

## Требования

* PHP >= 8.0
* Composer >= 2
* make >= 4

## Install and start project
* `host write 127.0.0.1 domainl.ru api.domainl.ru hub.tdomainl.ru jobs.tdomainl.ru catalog.domainl.ru`
* `make setup - запуск проекта` 

##№ для продакшена
* `сертификаты в image/nginx/ssl закинуть`
* `выставить среду production`

##№ для локальной разработки
* `выставить среду local`
* `сертификаты сделаются самоподписанные при сборке докера`

* `проверить env файл`
- uid пользователя должен соответствовать uid текущего пользователя в системе
- проверить ssl сертификаты и среду. Место положение сертификатов зависит от среды. Это касает dev и prod режима. кально делаются самоподписанные сертификаты и кладуться сами.

* Засидировать данные `make seeder`


## Tests and lint

* `make lint`
* `make lint-fix`
* `make test`
* `test-coverage`

