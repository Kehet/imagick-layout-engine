FROM serversideup/php:8.3-cli@sha256:18c82d8812b92ca38d91e87d71655f296208b92f3085b5b6e1ddad4656859d4c

ARG USER_ID
ARG GROUP_ID

USER root

RUN install-php-extensions imagick pcov

RUN apt-get update \
    && apt-get install -y --no-install-recommends fonts-dejavu-core \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID  && \
    docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID

USER www-data

WORKDIR /var/www/html

CMD ["composer", "test"]
