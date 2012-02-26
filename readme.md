Trek
====

Trek is a simple migration and versioning library. It abstracts the version tracking and migration between versions allowing you to focus on writing clean migration scrpts based on a simple interface.

Directory Structure
-------------------

Trek assumes a conventional directory structure:

    <namespace>/<major>.<minor>.<patch>/<number>_<classname>.php

### `<namespace>`

By default it is set to `Migration` but you can change it to whatever you want as long as it is a valid namespace.

### `<major>.<minor>.<patch>`

In order to map the version directory to part of the migration class namespace, it is transformed so that each number is mapped to its English word and each period is replaced by a namespace separator.

### `<number>`

The number part of the file name allows you to simply define which order you want your migrations to be run in. This part, in addition to the underscore and file extension, is removed in order to resolve the class name. No other transformations occur.

Writing Migrations
------------------

The following is a migration for `Migration/1.0.10/1_AddUserTable.php`:

    <?php
    
    namespace Migration\One\Zero\Ten;
    use Trek\MigrationInterface;
    
    class AddUserTable implements MigrationInterface
    {
        public function up()
        {
            // upgrade code here...
        }
        
        public function down()
        {
            // downgrade code here...
        }
    }

It is up to you how you write your code in your methods; there are no dependencies and you can use whatever libraries you want.

Migrating
---------

Migration is done by using the `Trek\Migrator` class.

    <?php
    
    use Trek\Migrator;
    
    $migrator = new Migrator('path/to/migrations', 'Migration\Namespace');

The migrator requires you pass it a directory to load the migrations from and an optional, preferred namespace to use instead of the default.

Moving between versions is quite simple. If you want to update to the latest version no matter what version you are currently on:

    $migrator->up();

If you want to move to a specific version:

    $migrator->to('1.0.0');

Running Tests
-------------

To run the tests from the work copy root:

    php bin/tests.php