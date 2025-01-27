# Тестовое задание для Ит Фиш

## Допущения
По-хорошему, стоит ещё добавить авторизацию, чтобы пользователи могли смотреть только свой баланс. Но для простоты показа, была опущена.  

## Развёртывание

```bash
git clone https://github.com/Akulon-dev/it-fish-test-task.git
cd it-fish-test-task
cp .env.example .env
composer install --no-dev --optimize-autoloader
php artisan key:generate

sudo docker compose up -d db redis

php artisan migrate --force
php artisan db:seed
php artisan l5-swagger:generate

sudo docker compose up -d app
```

## Документация
доступна по адресу **/api/documentation**.

