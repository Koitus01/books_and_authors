# books_and_authors

Управление книгами и их авторами

## Requirements

1. [Docker](https://docs.docker.com/get-docker/)
2. [Docker compose](https://docs.docker.com/compose/install/)

## Инструкция по развертыванию

Не стоит выполнять эти действия, будучи пользователем root.

«Склонировать» проект:

    git clone https://github.com/Koitus01/books_and_authors.git

Перейти в директорию с ним:

    cd books_and_authors

Запустить контейнеры:

    # Если docker compose установлен как «standalone package»:
    docker-compose up -d

    # Если как плагин для docker'a:
    docker compose up -d

Выполнить composer install:

    # Это также прогонит миграции для dev и testing баз данных
    docker exec -it books_and_authors_app composer install

## Использование

После развертывания веб интерфейс должен быть доступен по адресу: http://localhost:9080

Запуск тестов:

    docker exec -it books_and_authors_app php bin/phpunit

## Чистка

Удаление ненужных контейнеров и volum'ов:

    docker rm --force books_and_authors_db books_and_authors_webserver books_and_authors_app && docker volume rm books_and_authors_dbdata 

