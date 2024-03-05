##
## DOCKER
## -----------
up:
	docker-compose up -d

down:
	docker-compose down

clear:
	docker system prune --all --volumes --force

build:
	docker-compose down -v --remove-orphans
	docker-compose rm -vsf
	docker-compose up -d --build
	docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction
	cat inserts.sql | docker exec -i postgres psql -U postgres -d db2

##
## UTILS
## -----------
psql-connect:
	docker-compose exec postgres psql -U postgres db2


##
## Накатить все миграции
## -----------
migrate:
	docker-compose exec php bin/console doctrine:migrations:migrate

##
## Откатить последнюю миграцию
## -----------
rollback:
	docker-compose exec php bin/console doctrine:migrations:migrate prev


##
## Создать миграцию
## -----------
migration:
	docker-compose exec php bin/console make:migration

##
## Распаковать дамп с inserts
## -----------
restore-dump:
	cat inserts.sql | docker exec -i postgres psql -U postgres -d db2


##
## Создает полный дамп
## -----------
dump:
	docker exec postgres pg_dump -U postgres db2 > backup

##
## Создает дамп только inserts
## -----------
dump-only-inserts:
	docker exec postgres pg_dump -U postgres --column-inserts --data-only db2 > inserts.sql

##
## TESTING
## -----------
test:
	docker-compose exec php sh ./bin/run-tests.sh


##
## REFACTORING
## -----------
fixer:
	docker-compose exec php vendor/bin/php-cs-fixer fix src --verbose

phpstan:
	docker-compose exec php vendor/bin/phpstan analyse src/

