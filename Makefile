all: install

install: env pull migrate

deploy: pull migrate

doc: pull
	@npm run build

pull:
	@echo "pull laravel-shop"
	@git pull origin main

env:
	@echo "Installing Laravel Shop..."
	@[ ! -f .env ] && cp .env.example .env && php artisan key:generate || echo ".env already exists, skipping copy."

migrate:
	@composer install --no-dev
	@php artisan migrate --force
	@php artisan db:seed --force
