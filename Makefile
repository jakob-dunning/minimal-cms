init:
	docker run composer dump-autoload

test:
	docker run -v $$(pwd):/app -w="/app" php:7.4 /app/vendor/bin/phpunit