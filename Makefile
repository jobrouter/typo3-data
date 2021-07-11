.PHONY: qa
qa: coding-standards unit-tests acceptance-tests

.PHONY: acceptance-tests
acceptance-tests: vendor
	./Build/Scripts/runTests.sh -s acceptance -p 7.2

.PHONY: code-coverage
code-coverage: vendor
	XDEBUG_MODE=coverage .Build/bin/phpunit -c Tests/phpunit.xml.dist --log-junit .Build/logs/phpunit.xml --coverage-text --coverage-clover .Build/logs/clover.xml

.PHONY: coding-standards
coding-standards: vendor
	.Build/bin/php-cs-fixer fix --config=.php_cs --diff

.PHONY: unit-tests
unit-tests: vendor
	.Build/bin/phpunit --configuration=Tests/phpunit.xml.dist

vendor: composer.json composer.lock
	composer validate
	composer install
	composer normalize

.PHONY: zip
zip:
	grep -Po "(?<='version' => ')([0-9]+\.[0-9]+\.[0-9]+)" ext_emconf.php | xargs -I {version} sh -c 'mkdir -p ../zip; git archive -v -o "../zip/$(shell basename $(CURDIR))_{version}.zip" v{version}'
