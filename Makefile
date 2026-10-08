.PHONY: build test serve

## Build the website into ./docs
build:
	php build.php

## Run the test suite
test:
	php tests/run.php

## Build and serve on http://127.0.0.1:8000
serve: build
	php -S 127.0.0.1:8000 -t docs
