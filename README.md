Installation:

1. Install dependencies
```composer install```
2. Create .env file
```cp .env.example .env```
3. Generate app key
```php artisan key:generate```
4. Create .env.testing file
```cp .env .env.testing```
5. Run migrations
```php artisan migrate```
6. Run test migrations
```php artisan migrate --env=testing```
7. Run seeders
```php artisan db:seed```
8. Run the list of available commands
```php artisan jukebox```
9. Run tests
```php artisan test```

