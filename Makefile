all: clean test coverage

clean:
	rm -rf build/artifacts/*

test:
	phpunit --testsuite=ultimate $(TEST)

coverage:
	phpunit --testsuite=ultimate --coverage-html=build/artifacts/coverage $(TEST)

coverage-show:
	open build/artifacts/coverage/index.html
