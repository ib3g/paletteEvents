install: installation init_db
reinstall: installation clean_db_dev

installation:
	@if [ ! -f .env.local ]; then \
		cp .env .env.local; \
	else \
		echo ".env.local file already exist, skipping"; \
	fi

	docker-compose build
	docker-compose up -d
	docker-compose exec -T php composer install -n

	docker-compose exec -T php php bin/console assets:install

	docker-compose exec -T php npm install
	#docker-compose exec -T php npm run build

init_db:
	docker-compose exec php php bin/console d:d:c -vvv
	docker-compose exec php php bin/console d:m:m -n -vvv
	docker-compose exec php php bin/console d:f:l -n -vvv

clean_db:
	docker-compose exec php php bin/console d:s:d -f -vvv
	docker-compose exec php php bin/console d:m:m -n -vvv
	docker-compose exec -T php php bin/console d:f:l --group=prod -n

clean_db_dev:
	docker-compose exec php php bin/console d:d:d -f -vvv
	docker-compose exec php php bin/console d:d:c -vvv
	docker-compose exec php php bin/console d:m:m -n -vvv
	docker-compose exec php php bin/console d:f:l -n


security_check:
	docker-compose exec -T php symfony security:check

run_unit_tests:
	docker-compose exec -T php php vendor/bin/phpunit

sf_console:
	docker-compose exec -T php php bin/console $(command)

run_unit_test:
	docker-compose exec -T php php vendor/bin/phpunit $(test)

backend_start:
	docker-compose up -d

start: backend_start

stop:
	docker-compose down

d:
	docker-compose $(command)

dc:
	docker-compose exec -T php $(command)

npm:
	docker-compose exec -T php npm $(command)

composer:
	docker-compose exec -T php composer $(command)

run_assets:
	docker-compose exec -T php php bin/console assets:install
	docker-compose exec -T php npm run build

assets_watch:
	docker-compose exec -T php php bin/console assets:install
	docker-compose exec -T php npm run build
	docker-compose exec -T php npm run watch