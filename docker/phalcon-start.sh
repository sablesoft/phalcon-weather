#!/bin/bash

# install composer if needed:
composer install

# devtools symlink
[ ! -e "/usr/bin/phalcon" ] && ln -s /project/vendor/phalcon/devtools/phalcon /usr/bin/phalcon

# project init
[ ! -d "/project/weather" ] && cd /project && phalcon project weather && \
                               chown -R www-data:www-data weather;

# run app migrations:
cd /project/weather && phalcon migration run;

/start.sh
