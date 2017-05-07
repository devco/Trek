<?php

namespace Test;
use Testes\Test\UnitAbstract;
use Trek\Migrator;
use Trek\MigrationIterator;
use MigrationsTwo\DoFakeWork;

/**
 * Only execute migration scripts that have a specific parent class.
 */
class MigrationsFiltered extends UnitAbstract
{
    private $migrator;

    public function setUp()
    {
        $this->migrator = new Migrator(__DIR__ . '/../MigrationsTwo', 'MigrationsTwo');
    }

    public function tearDown()
    {
        // ensure the version file is removed to reset tests and to not screw with version control
        $versionFile = __DIR__ . '/../MigrationsTwo/' . Migrator::VERSION_FILE;

        if (is_file($versionFile)) {
            unlink($versionFile);
        }
    }

    public function migrateUpWithClassFilter()
    {
        MigrationIterator::setClassFilters(['MigrationsTwo\DatabaseAbstract']);

        $this->migrator->up();

        $this->assert(DoFakeWork::$counterUp === 1, 'Should have completed 1 migrations');
        $this->assert($this->migrator->version()->compare('1.1.0') === 0,
            'Version should migrate despite no migrations in that namespace');
    }

    public function migrateDownWithClassFilter()
    {
        MigrationIterator::setClassFilters(['MigrationsTwo\DatabaseAbstract']);

        $this->migrator->down();

        $this->assert(DoFakeWork::$counterDown === 1, 'Should have completed 1 migrations');
    }
}
