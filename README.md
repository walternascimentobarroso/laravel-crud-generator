[![Build Status](https://travis-ci.org/walternascimentobarroso/laravel-crud-generator.svg?branch=develop)](https://travis-ci.org/walternascimentobarroso/laravel-crud-generator)
[![Latest Stable Version](https://poser.pugx.org/walternascimentobarroso/laravel-crud-generator/v/stable.png)](https://packagist.org/packages/walternascimentobarroso/laravel-crud-generator) 
[![Total Downloads](https://poser.pugx.org/walternascimentobarroso/laravel-crud-generator/downloads.png)](https://packagist.org/packages/walternascimentobarroso/laravel-crud-generator)

Via Composer

```bash
composer require walternascimentobarroso/laravel-crud-generator 
```

Add the service provider in `config/app.php`:'

```php
WalterNascimentoBarroso\CrudGenerator\CrudGeneratorServiceProvider::class,
```

if use the first time, run the code below:

```bash
php artisan vendor:publish --provider="WalterNascimentoBarroso\CrudGenerator\CrudGeneratorServiceProvider"
```

# Generate doc
```
cd doc
apidoc -i ../routes/ -o ../public/apidoc/
http://localhost:8000/apidoc/index.html
```
