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
abstract class MigrationAbstract implements MigrationInterface
{
    /**
     * Set up the migration.
     * 
     * @return void
     */
    public function setUp()
    {
        
    }

    /**
     * Tear down the migration.
     * 
     * @return void
     */
    public function tearDown()
    {
        
    }
    
    /**
     * Empty down method since not everyone will write one.
     * 
     * @return void
     */
    public function down()
    {
        
    }
}