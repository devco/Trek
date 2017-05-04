<?php

namespace Test;
use Testes\Test\UnitAbstract;
use Trek\Migrator;

class Migrations extends UnitAbstract
{
    private $migrator;

    public function setUp()
    {
        $this->migrator = new Migrator(__DIR__ . '/../migrations');
    }

    public function tearDown()
    {
        // ensure the version file is removed to reset tests and to not screw with version control
        $versionFile = __DIR__ . '/../migrations/' . Migrator::VERSION_FILE;

        if (is_file($versionFile)) {
            unlink($versionFile);
        }
    }

    public function multipleUpgradesToLatestStable()
    {
        $this->migrator->to('1.0.0');
        $this->assert($this->migrator->version()->compare('1.0.0') === 0);
    }

    public function multipleDowngradesFromLatestStable()
    {
        $this->migrator->to('0.0.1');
        $this->assert($this->migrator->version()->compare('0.0.1') === 0);
    }

    public function singleUpgradeFromFirstVersion()
    {
        $this->migrator->to('0.1.0');
        $this->assert($this->migrator->version()->compare('0.1.0') === 0);
    }

    public function fullUpgradeFromExistingVersion()
    {
        $this->migrator->to('1.0.0');
        $this->assert($this->migrator->version()->compare('1.0.0') === 0);
    }

    public function rollbackProcedureUsingFullUpgrade()
    {
        // ensure an exception is caught
        try {
            $this->migrator->up();
        } catch (\Exception $e) {
            $this->assert($e->getMessage() === 'Testing rollback.');
        }

        // ensure that the version is NOT bumped
        $this->assert($this->migrator->version()->compare('1.0.0') === 0);
    }

    public function downgradeToFirstVersion()
    {
        $this->migrator->down();

        $this->assert($this->migrator->version()->compare('0.0.1') === 0);
    }
}
