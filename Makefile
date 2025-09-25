# Makefile for Symfony + Nginx (+ PHP-FPM, Xdebug, PostgreSQL to be added)
# default target
.DEFAULT_GOAL := help

# adjust if you change ports or container names later
NGINX_CONTAINER := symfony-nginx
APP_URL := http://localhost:8080

.PHONY: help up down down-v stop restart logs ps build \
        nginx-shell open curl-test prune

help: ## Show available make commands
	@echo ""
	@echo "Usage: make [target]"
	@echo ""
	@echo "Targets:"
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| sort \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'
	@echo ""

up: ## Start containers in detached mode
	docker compose up -d
	docker compose up -d --build

down: ## Stop and remove containers, networks (keeps volumes)
	docker compose down

down-v: ## Stop and remove containers, networks and volumes (DB data lost!)
	docker compose down -v

stop: ## Stop running containers (keep them)
	docker compose stop

restart: ## Restart containers
	docker compose restart

logs: ## Tail logs from all containers
	docker compose logs -f

rebuild:
	docker compose up -d --build

build: ## Rebuild containers
	docker compose build

ping: ## Health check (HTTP HEAD request)
	curl -I http://localhost:8080/	

cache-clear: ## Clear cache
	docker compose exec -T php bin/console cache:clear	

phpstan:
	docker compose exec -T php composer phpstan

cs-check:
	docker compose exec -T php composer cs:check

cs-fix:
	docker compose exec -T php composer cs:fix

test:
	docker compose exec -T php composer test:unit