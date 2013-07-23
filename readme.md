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

The version also supports the usage of a pre-release version and pre-release version number.

- 1.0.0-alpha
- 1.0.0-alpha.1
- 1.0.0-beta
- 1.0.0-beta.1
- 1.0.0-rc.1

...and so forth.

### `<number>`

The number part of the file name allows you to simply define which order you want your migrations to be run in. It is removed from the actual class name during resolution.

### `<classname>`

This part of the file name maps directly to the class name within the resolved namespace.

Writing Migrations
------------------

The following is a migration for `Migration/1.0.10/1_AddUserTable.php`:

    <?php

    namespace Migration\One\Zero\OneZero;
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

If you want to rollback because of an error:

    try {
        $migrator->up();
    } catch (\Exception $e) {
        $migrator->rollback();
    }

If errors occur during the rollback process, you will need to handle those manually.

Running Tests
-------------

To run the tests from the working copy root:

    php bin/tests.php

License
-------

Copyright (c) 2013 Ultra Serve Internet Pty Ltd

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
