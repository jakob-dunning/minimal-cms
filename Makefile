init:
	docker-compose run composer install
	docker-compose up -d
	docker-compose exec apache-php bash -c "docker-php-ext-install pdo pdo_mysql"
	docker-compose exec apache-php bash -c "a2enmod rewrite"
	docker-compose exec db bash -c "mysql -u root -proot < /schema.sql"
test:
	docker run -v $$(pwd):/app -w="/app" php:7.4 /app/vendor/bin/phpunit