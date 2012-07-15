<?php

use Testes\Test\UnitAbstract;
use Trek\Autoloader;
use Trek\Migrator;
use Trek\Version;

class Test extends UnitAbstract
{
    private $migrator;
    
    public function setUp()
    {
        // register autolaoding
        // this may not be necessary for other libraries that follow proper autoloading conventions
        require_once(__DIR__ . '/../src/Trek/Autoloader.php');
        Autoloader::register();
        
        // so we don't have to re-instantiate it for every test
        $this->migrator = new Migrator(__DIR__ . '/migrations');
    }
    
    public function tearDown()
    {
        // ensure the version file is removed to reset tests and to not screw with version control
        unlink(__DIR__ . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . Migrator::VERSION_FILE);
    }
    
    public function versioning()
    {
        $ver = new Version('1.12.123');
        $this->assert($ver->ns() === 'One\OneTwo\OneTwoThree');
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
}