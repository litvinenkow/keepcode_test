### Процесс запуска приложения

скопировать .env.example в .env

Создать БД в локальной mysql и указать реквизиты в .env

Выполнить следующие команды:
```
composer install
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan db:seed
```

Если хотим увидеть фронт, то 
```
npm i
npm run dev
```

Ежеминутная крон команда для сброса арендованных продуктов по истечении срока аренды 
```
php artisan check-rent-orders
```

Тесты написал только на роуты ибо особо тестировать нечего, почти нет логики
```
php vendor/bin/phpunit
```
