FROM php:5.6-apache
LABEL "purpose"="For spinning up a developer instance quickly." "do not use in"="production,public instances"

# install the PHP extensions and tool required by OSTEPU
RUN set -ex; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
		git \
		mariadb-client \
	; \
	savedAptMark="$(apt-mark showmanual)"; \
	apt-get install -y --no-install-recommends \
		zlib1g-dev \
		libcurl4-openssl-dev \
		libldb-dev libldap2-dev \
		libmcrypt-dev \
	; \
	\
	ln -s /usr/lib/x86_64-linux-gnu/libldap.so /usr/lib/libldap.so; \
	ln -s /usr/lib/x86_64-linux-gnu/liblber.so /usr/lib/liblber.so; \
	docker-php-ext-install mysqli opcache zip curl mysql sockets ldap mcrypt; \
	\
# reset apt-mark's "manual" list so that "purge --auto-remove" will remove all build dependencies
	apt-mark auto '.*' > /dev/null; \
	apt-mark manual $savedAptMark; \
	ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
		| awk '/=>/ { print $3 }' \
		| sort -u \
		| xargs -r dpkg-query -S \
		| cut -d: -f1 \
		| sort -u \
		| xargs -rt apt-mark manual; \
	\
	apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
	rm -rf /var/lib/apt/lists/*

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=2'; \
		echo 'opcache.fast_shutdown=1'; \
		echo 'opcache.enable_cli=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini \
	&& { \
		echo '[Date]'; \
		echo 'date.timezone = Europe/Berlin'; \
	} > /usr/local/etc/php/conf.d/timezone.ini

# Warning! For non-public instances only. Web servers should be only
# allowed to write to known locations (where special hardening can be
# applied to)!
COPY --chown=www-data:www-data . /var/www/html

RUN mkdir -p /var/www/files /var/www/queryTree /var/www/backup /var/www/html/install/logs \
	&& chown www-data:www-data /var/www/files /var/www/queryTree /var/www/backup /var/www/html/install/logs

RUN a2enmod rewrite expires deflate headers filter

