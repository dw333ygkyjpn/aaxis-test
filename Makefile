SHELL = sh
.DEFAULT_GOAL = help

## —— 🎶 aaxis-test Makefile :) 🎶 ——————————————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help start stop purge test coverage cov-report stan fix-php lint-php lint-container lint-twig lint-yaml cs lint
.PHONY: version-php version-composer version-symfony version-phpunit version-phpstan version-php-cs-fixer

build: ## Build the application :)
build: build-docker build-database start
build-docker: ## Build the docker container
	@docker compose build --no-cache
	@docker compose up --pull always -d --wait
build-database: ## Build the database
	@symfony console doctrine:database:drop --if-exists --force
	@symfony console doctrine:database:create
	@symfony console doctrine:schema:update --force
	@symfony console doctrine:fixtures:load -n
## —— Symfony binary 💻 ————————————————————————————————————————————————————————
start: ## Serve the application with the Symfony binary
	@sudo symfony serve -d
	@symfony open:local

stop: ## Stop the web server
	@symfony server:stop

## —— Symfony 🎶  ——————————————————————————————————————————————————————————————
warmup: ## Warmup the dev cache for the statis analysis
	@symfony console c:w --env=dev

purge: ## Purge all Symfony cache and logs
	@rm -rf ./var/cache/* ./var/logs/* ./var/coverage/*


## —— Tests ✅ —————————————————————————————————————————————————————————————————
test: ## Run all PHPUnit tests
	@symfony php vendor/bin/phpunit

coverage: ## Generate the HTML PHPUnit code coverage report (stored in var/coverage)
coverage: purge
	@XDEBUG_MODE=coverage symfony php -d xdebug.enable=1 -d memory_limit=-1 vendor/bin/phpunit --coverage-html=var/coverage
	@symfony php bin/coverage-checker.php var/coverage/clover.xml 100

cov-report: var/coverage/index.html ## Open the PHPUnit code coverage report (var/coverage/index.html)
	@open var/coverage/index.html

## —— Coding standards/lints ✨ ————————————————————————————————————————————————
stan: ## Run PHPStan
	@symfony console cache:clear
	@symfony php vendor/bin/phpstan analyse -c phpstan.neon --memory-limit 1G -vvv

fix-php: ## Fix PHP files with php-cs-fixer
	@PHP_CS_FIXER_IGNORE_ENV=1 symfony php vendor/bin/php-cs-fixer fix --allow-risky=yes $(PHP_CS_FIXER_ARGS)

lint-php: ## Lint PHP files with php-cs-fixer (report only)
lint-php: PHP_CS_FIXER_ARGS=--dry-run
lint-php: fix-php

lint-container: ## Lint the Symfony DI container
	@symfony php bin/console lint:container

lint-twig: ## Lint Twig files
	@symfony php bin/console lint:twig templates/

lint-yaml: ## Lint YAML files
	@symfony php bin/console lint:yaml --parse-tags config/

cs: ## Run all CS checks
cs: fix-php stan

lint: ## Run all lints
lint: lint-php lint-container lint-twig lint-yaml

## —— Other tools and helpers 🔨 ———————————————————————————————————————————————
versions: version-php version-docker version-docker-compose version-composer version-symfony version-phpunit version-phpstan version-php-cs-fixer ## Output current stack versions
version-php:
	@echo   '—— PHP ————————————————————————————————————————————————————————————'
	@php -v
version-docker:
	@echo '\n—— docker ———————————————————————————————————————————————————'
	@docker --version
version-docker-compose:
	@echo '\n—— docker compose ———————————————————————————————————————————————————'
	@docker compose version
version-composer:
	@echo '\n—— Composer ———————————————————————————————————————————————————————'
	@composer --version
version-symfony:
	@echo '\n—— Symfony ————————————————————————————————————————————————————————'
	@php bin/console --version
version-phpunit:
	@echo '\n—— PHPUnit (phpunit) ———————————————————————————————————————'
	@php vendor/bin/phpunit --version
version-phpstan:
	@echo '—— PHPStan ————————————————————————————————————————————————————————'
	@php vendor/bin/phpstan --version
version-php-cs-fixer:
	@echo '\n—— php-cs-fixer ———————————————————————————————————————————————————'
	@php vendor/bin/php-cs-fixer --version
	@echo