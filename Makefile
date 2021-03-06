init:
	docker-compose run composer install
	docker-compose up -d
	docker-compose exec apache-php bash -c "docker-php-ext-install pdo pdo_mysql"
	docker-compose exec apache-php bash -c "a2enmod rewrite"
	docker-compose exec apache-php bash -c "pecl install xdebug && docker-php-ext-enable xdebug"
	docker-compose exec db bash -c "mysql -u root -proot < /schema.sql"

test:
	docker run -v $$(pwd):/app -w="/app" php:7.4 /app/vendor/bin/phpunit

test-coverage:
	docker-compose exec apache-php bash -c "/var/www/html/vendor/bin/phpunit --coverage-html /var/www/html/tests/coverage"

reset-database:
	docker-compose exec db bash -c "mysql -u root -proot -e 'DROP DATABASE minimalCMS'"
	docker-compose exec db bash -c "mysql -u root -proot < /schema.sql"

create-passsword:
	docker-compose exec apache-php php -r "echo password_hash('mango', PASSWORD_DEFAULT).\"\n\";"