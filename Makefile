
# Usage:
# make up - starts the docker-compose in the same directory in demon (background)
# make down - stops the docker-compose
# make shell - opens a sh terminal in the running ajaxcrud container as a standard user
# make shell-root -  opens a sh in ajaxcrud container as root user
# make shell-web -  opens a sh in ajaxcrud container as www-data user
# make up-f - start the docker-compose in foreground (useful for error messages)
# make tests - run phpunit tests
# make test-coverage phpunit coverage html report will be created in build/coverage
# make phpstan - static analysis using phpstan
# make checkcode - check code using php_code sniffer (phpcbf)
# make fixcode - fix code using php_code sniffer (phpcbf)
# make check-cs - check code using easy coding standards (ecs)
# make fix-cs - fix code using easy coding standards (ecs)

# www-data:www-data is 33:33 (Duster)
# www-data:www-data is 100:101 (Alpine)
APACHE_UID = 33
APACHE_GID = 33
# To confirm your working directory, at the command line run: echo $(pwd)

up:
	docker-compose up --build --remove-orphans -d
up-f:
	docker-compose up --build --remove-orphans
down:
	docker-compose down --remove-orphans
shell:
	docker-compose exec -u ${shell id -u}:${shell id -g} web bash
shell-run:
	docker-compose run -u ${shell id -u}:${shell id -g} web bash
shell-root:
	docker-compose exec -u 0:0 web bash
shell-web:
	docker-compose exec -u ${APACHE_UID}:${APACHE_GID} web bash

chown:
	docker-compose exec -u 0:0 web chown -R ${shell id -u}:${shell id -g} ./
	docker-compose exec -u 0:0 web chown -R ${APACHE_UID}:${APACHE_GID} ./storage

# The project needs to have phpunit.xml or phpunit.xml.dist in the root folder
.PHONY : tests
pu:
	docker run --init -it --rm -v $(shell pwd):/project -v $(shell pwd)/tmp-phpqa:/tmp -w /project \
    		jakzal/phpqa:1.50-php7.4-alpine phpunit

# The project needs to have phpstan.neon in the root folder
ps:
	docker run --init -it --rm -v $(shell pwd):/project -v $(shell pwd)/tmp-phpqa:/tmp -w /project \
		jakzal/phpqa:1.50-php7.4-alpine phpstan analyse

# The project needs to have phpcs.xml in the root folder
checkcode:
	docker run --init -it --rm -v $(shell pwd):/project -v $(shell pwd)/tmp-phpqa:/tmp -w /project \
    		jakzal/phpqa:1.50-php7.4-alpine phpcs public --standard=phpcs.xml

# The project needs to have phpcs.xml in the root folder
fixcode:
	docker run --init -it --rm -v $(shell pwd):/project -v $(shell pwd)/tmp-phpqa:/tmp -w /project \
    		jakzal/phpqa:1.50-php7.4-alpine phpcbf public --standard=phpcs.xml

# The project needs to have ecs.php in the root folder
cc:
	docker run --init -it --rm -v $(shell pwd):/project -v $(shell pwd)/tmp-phpqa:/tmp -w /project \
    		jakzal/phpqa:1.50-php7.4-alpine ecs check

# The project needs to have ecs.php in the root folder
fc:
	docker run --init -it --rm -v $(shell pwd):/project -v $(shell pwd)/tmp-phpqa:/tmp -w /project \
    		jakzal/phpqa:1.50-php7.4-alpine ecs check --fix

toolbox:
	docker run --init -it --rm -v $(shell pwd):/project -v $(shell pwd)/tmp-phpqa:/tmp -w /project \
			jakzal/phpqa:1.50-php7.4-alpine sh
