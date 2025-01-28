# Тестовое задание для Ит Фиш

## Допущения
По-хорошему, стоит ещё добавить авторизацию, чтобы пользователи могли смотреть только свой баланс. Но для простоты показа, была опущена.  

## Развёртывание

```bash
git clone https://github.com/Akulon-dev/it-fish-test-task.git
cd it-fish-test-task
cp .env.production .env
composer install --no-dev --optimize-autoloader
php artisan key:generate

sudo sysctl vm.overcommit_memory=1

docker compose build
docker compose up -d

docker exec -it it_fish_test_task php artisan migrate --force
docker exec -it it_fish_test_task php artisan db:seed -n --force
docker exec -it it_fish_test_task php artisan l5-swagger:generate
docker exec -it it_fish_test_task php artisan optimize:clear
docker exec -it it_fish_test_task php artisan config:cache
docker exec -it it_fish_test_task php artisan route:cache
```

## Документация
доступна по адресу **/api/documentation**.


