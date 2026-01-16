.PHONY: *

## —— Help ————————————————————————————————————
help: ## Show help
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' Makefile | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Tests ———————————————————————————————————
clean-test-cache: ## Deletes the PHPUnit/test cache
	rm -rf $(CURDIR)/var/test/cache/*
	rm -rf $(CURDIR)/var/test/coverage/*
tests: clean-test-cache ## Run tests
	php vendor/bin/simple-phpunit -v
tests-coverage: clean-test-cache ## Generate test coverage
	XDEBUG_MODE=coverage php vendor/bin/simple-phpunit --coverage-html $(CURDIR)/var/test/coverage/
tests-coverage-view-in-browser: ## Open the generated HTML coverage in your default browser
	xdg-open "$(CURDIR)/var/test/coverage/index.html" || open "file://$(CURDIR)/var/test/coverage/index.html"

## —— Linters —————————————————————————————————
linter-code-syntax: ## Lint PHP code (in dry-run mode, does not edit files)
	vendor/bin/simple-phpunit install
	vendor/bin/phpstan analyse
	vendor/bin/phpcs ./src ./tests
	vendor/bin/php-cs-fixer fix --diff --dry-run -vvv --using-cache=no
fix-code-syntax: ## Lint PHP code (in dry-run mode, does not edit files)
	vendor/bin/phpcbf ./src ./tests
	vendor/bin/php-cs-fixer fix --diff --dry-run -vvv --using-cache=no

## —— Development —————————————————————————————
build: ## Initially build the package before development
	composer update

checks-before-pr: linter-code-syntax tests ## Runs tests and linters which are also run on PRs
