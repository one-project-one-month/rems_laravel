# rems_laravel

# Setting up for development using docker
## generate .env
copy .env.example and rename it .env

## install dependencies
```
composer install
```

## generate key
```
php artisan key:generate
```

## build and run the services
```
docker compose up -d
```

## running migrations
```
docker compose exec laravel php artisan migrate
```
