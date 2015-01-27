all: clean test coverage

clean:
	rm -rf build/artifacts/*

test:
	vendor/bin/phpunit --testsuite=shutdownhandler $(TEST)

coverage:
	vendor/bin/phpunit --testsuite=shutdownhandler --coverage-html=build/artifacts/coverage $(TEST)

coverage-show:
	open build/artifacts/coverage/index.html

travis:
	vendor/bin/phpunit
