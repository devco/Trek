<?php

use Trek\Autoloader;
use Trek\Migrator;
use Trek\Version;

class Test extends Testes\Test\Test
{
    private $migrator;
    
    public function setUp()
    {
        // register autolaoding
        // this may not be necessary for other libraries that follow proper autoloading conventions
        require_once(__DIR__ . '/../lib/Trek/Autoloader.php');
        Autoloader::register();
        
        // so we don't have to re-instantiate it for every test
        $this->migrator = new Migrator(__DIR__ . '/migrations');
    }
    
    public function tearDown()
    {
        // ensure the version file is removed to reset tests and to not screw with version control
        unlink(__DIR__ . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . Migrator::VERSION_FILE);
    }
    
    public function fullUpgradeFromUnversioned()
	{
        $this->migrator->up();
        $this->assert($this->migrator->version()->eq('1.0.0'));
    }
    
    public function specificDowngrade()
    {
        $this->migrator->to('0.0.1');
        $this->assert($this->migrator->version()->eq('0.0.1'));
    }
    
    public function specificUpgrade()
    {
        $this->migrator->to('0.1.0');
        $this->assert($this->migrator->version()->eq('0.1.0'));
    }
    
    public function fullUpgradeFromExistingVersion()
    {
        $this->migrator->up();
        $this->assert($this->migrator->version()->eq('1.0.0'));
    }
}