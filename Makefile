all: clean test coverage

clean:
	rm -rf build/artifacts/*

test:
	phpunit --testsuite=shutdownhandler $(TEST)

coverage:
	phpunit --testsuite=shutdownhandler --coverage-html=build/artifacts/coverage $(TEST)

coverage-show:
	open build/artifacts/coverage/index.html
