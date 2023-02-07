.DEFAULT_GOAL := help
install: ## Copy .env.example to .env and build docker image
	cp .env.example .env
	docker-compose build

start: ## Run server http
	docker-compose up -d

stop: ## Stop server http
	docker-compose down --remove-orphans

composer-install: ## Run composer install
	docker-compose exec php composer install

key-generate: ## Create key for artisan
	docker-compose exec php php artisan key:generate

init: ## First setup
	$(MAKE) install
	$(MAKE) start
	$(MAKE) composer-install

kill-all: ## Kill all running containers
	docker container kill $$(docker container ls -q)

.PHONY: help
help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'