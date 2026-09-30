# php-migration

a MySQL migration tool for framework-free PHP applications.

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
 * example migration scripts, executed in natural order of file names :
 * 0-create-tbl-foo.sql
 * 1-modify-tbl-foo.sql
 * 2-another-migration.sql
 * 2-foo-new-fields.sql
 * 3-foo-new-index.sql
 * ...
 * 9-foo-new-index-y.sql
 * 10-foo-new-index-z.sql
 */
$scripts_dir = '/var/www/myproject/any/path';

/**
 * no output
 */
$migration = new YD\Migration($instance, $scripts_dir);
$migration->run();
```