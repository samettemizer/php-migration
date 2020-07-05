# php-migration

a mysql migration tool for php applications without framework

## installation

you can add this package dependency to your project using [composer](https://getcomposer.org/):

    composer require stemizer/php-migration

if you only need this package during development:

    composer require --dev stemizer/php-migration
    
## usage example

```php
/**
 * pdo instance
 */
$instance = new PDO(args);

/**
 * example migration scripts :
 * 0-create-tbl-foo.sql
 * 1-modify-tbl-foo.sql
 * 2-another-migration.sql
 * 2-foo-new-fields.sql
 * 3-foo-new-index.sql
 */
$scripts_dir = '/var/www/myproject/any/path';

/**
 * no output
 */
$migration = new YD\Migration($instance, $scripts_dir);
$migration->run();
```