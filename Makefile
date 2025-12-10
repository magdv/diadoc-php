help:
	@echo "\n\
Usage: \n\n\
    make \n\
		 | init                         Поднять приложение с нуля\n\
		 | up                           Запустить установленные контейнеры\n\
		 | down                         Остановить и уничтожить все контейнеры приложения\n\
		 | restart                      Перезапуск контейнеров\n\
		 | test                         Запуск тестов\n\
         | generate-proto               Гененрирование proto файлов\n\
         | app-lint                     Исправить котостайл\n\
         | rector                       Запуск ректора\n\
         | shell-php 					Запустить консоль контейнера\n\
         \n\
    "

init: docker-down-clear \
	docker-pull docker-build up \
	app-init
up: docker-up
down: docker-down
restart: down up
test: app-test

generate-proto:
	docker compose run --rm cli composer generate-proto

rector:
	docker compose run --rm cli vendor/bin/rector process src

docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-pull:
	docker compose pull --include-deps

docker-build:
	docker compose build

app-composer-install:
	docker compose run --rm cli composer install

app-init: permissions app-cache-clear app-composer-install

app-cache-clear:
	docker run --rm -v ${PWD}/:/app -w /app alpine sh -c 'rm -rf var/cache/* var/test/*'

permissions:
	docker run --rm -v ${PWD}/:/app -w /app alpine chmod 777 var/cache var/test

app-test:
	docker compose run --rm cli composer test

app-lint:
	docker compose run --rm cli composer lint
	docker compose run --rm cli composer cs-fix

app-analyze:
	docker compose run --rm cli composer psalm

shell-php:
	docker compose exec cli bash
