# Комментарии

Тестовое задание: список комментариев на Docker + CodeIgniter 4.

Стек: PHP 8.2, MySQL 8, CI4, jQuery 3, Bootstrap 4.

## Как запустить

Нужен Docker и Docker Compose. На Windows ещё WSL.

Из папки проекта:

```
docker-compose up -d
```

Потом зайти в контейнер и поставить зависимости:

```
docker-compose exec web bash
composer install
```

`.env` лежит в `src/`. Если его нет — скопировать из `env` и прописать базу:

```
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

database.default.hostname = db
database.default.database = comments_db
database.default.username = comments_user
database.default.password = comments_password_local
database.default.DBDriver = MySQLi
database.default.port = 3306
```

`hostname = db` потому что так называется сервис в docker-compose.

Таблицу создать миграцией (из контейнера web):

```
php spark migrate
```

Сайт: http://localhost:8080/

Если `exec web bash` пишет что сервис не запущен — ещё раз `docker-compose up -d`.

## Что есть на сайте

Форма добавления под списком. Добавление и удаление через jquery (ajax).

По 3 комментария на страницу, можно выбрать страницу. Сортировка по id и по дате, asc/desc.

Email проверяется и в форме, и на сервере, ошибка выводится.

Вёрстка адаптивная.

По заданию `name` — это почта автора, отдельного поля с именем нет. Ещё есть `text` и `date` (дату выбирает сам пользователь).

## Если docker не качает образы (403)

Иногда Docker Hub режет по региону. Тогда зеркало в daemon.json:

Linux: `/etc/docker/daemon.json`  
Windows: `C:\ProgramData\docker\config\daemon.json`  
Docker Desktop: `C:\Users\<юзер>\.docker\daemon.json`

```
{ "registry-mirrors" : [ "https://dockerhub.timeweb.cloud" ] }
```

Потом `systemctl reload docker`.
