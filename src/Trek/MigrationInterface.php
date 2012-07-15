<?php

namespace Trek;

/**
* The migration interface each migration class must implement.
* 
* @category Migrations
* @package  Trek
* @author   Trey Shugart <treshugart@gmail.com>
* @license  MIT http://www.opensource.org/licenses/mit-license.php
*/
interface MigrationInterface
{
    /**
     * Set up the migration.
     * 
     * @return void
     */
    public function setUp();
    
    /**
     * Tear down the migration.
     * 
     * @return void
     */
    public function tearDown();
    
    /**
     * Runs the upgrade.
     * 
     * @return void
     */
    public function up();
    
    /**
     * Runs the downgrade.
     * 
     * @return void
     */
    public function down();
}