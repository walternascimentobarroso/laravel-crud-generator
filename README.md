
Via Composer

```bash
composer require walternascimentobarroso/laravel-crud-generator 
```

Add the service provider in `config/app.php`:

```php
WalterNascimentoBarroso\CrudGenerator\CrudGeneratorServiceProvider::class,
```

if use the first time, run the code below:

```bash
php artisan vendor:publish --provider="WalterNascimentoBarroso\CrudGenerator\CrudGeneratorServiceProvider"
```
