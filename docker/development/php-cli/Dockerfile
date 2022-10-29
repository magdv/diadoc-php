FROM php:7.2-cli-alpine

ENV XDEBUG_VERSION 3.1.2
RUN apk update && apk upgrade

RUN apk add --no-cache bash coreutils git \
    && git clone --branch $XDEBUG_VERSION --depth 1 https://github.com/xdebug/xdebug.git /usr/src/php/ext/xdebug \
    && docker-php-ext-configure xdebug --enable-xdebug-dev \
    && docker-php-ext-install xdebug

RUN apk add --no-cache unzip

RUN apk update && apk add curl bash unzip build-base autoconf automake libtool make g++
ENV PROTOBUF_VERSION 3.6.1
ENV PROTOBUF_URL https://github.com/google/protobuf/releases/download/v"$PROTOBUF_VERSION"/protobuf-cpp-"$PROTOBUF_VERSION".zip
RUN curl --silent -L -o protobuf.zip "$PROTOBUF_URL" && \
    unzip protobuf.zip && \
    cd protobuf-"$PROTOBUF_VERSION" && \
    ./configure && \
    make -j$(nproc) && \
    make install && \
    cd .. && rm protobuf.zip

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d
COPY ./development/php/conf.d /usr/local/etc/php/conf.d

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet \
    && rm -rf /root/.composer/cache

RUN addgroup -g 1000 app && adduser -u 1000 -G app -s /bin/sh -D app

WORKDIR /app

USER app