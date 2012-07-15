<?php

namespace Trek;
use RuntimeException;

/**
 * Used to locate and iterate over migration instances.
 * 
 * @category Migrations
 * @package  Trek
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  MIT http://www.opensource.org/licenses/mit-license.php
 */
class MigrationIterator implements \Iterator
{
    /**
     * The array if migration instances.
     * 
     * @var array
     */
    private $migrations = array();
    
    /**
     * The main migrator instance.
     * 
     * @var \Trek\Migrator
     */
    private $migrator;
    
    /**
     * The version to migrate to.
     * 
     * @var \Trek\Version
     */
    private $version;

    /**
     * Sets up the migration iterator.
     * 
     * @param \Trek\Migrator The main migrator instance.
     * @param mixed          The version to migrate to.
     * 
     * @return \Trek\MigrationIterator
     */
    public function __construct(Migrator $migrator, $version)
    {
        $this->migrator = $migrator;
        $this->version  = new Version($version);
        $this->detectMigrations();
    }
    
    /**
     * Upgrades.
     * 
     * @return \Trek\MigrationIterator
     */
    public function up()
    {
        foreach ($this as $migration) {
            $migration->setUp();
            $migration->up();
            $migration->tearDown();
        }
        return $this;
    }
    
    /**
     * Downgrades.
     * 
     * @return \Trek\MigrationIterator
     */
    public function down()
    {
        $migrations = array_reverse($this->migrations);
        foreach ($migrations as $migration) {
            $migration->setUp();
            $migration->down();
            $migration->tearDown();
        }
        return $this;
    }

    /**
     * Returns the current migration.
     * 
     * @return \Trek\MigrationInterface
     */
    public function current()
    {
        return current($this->migrations);
    }
    
    /**
     * Returns the current key.
     * 
     * @return int
     */
    public function key()
    {
        return key($this->migrations);
    }
    
    /**
     * Moves to the next migration.
     * 
     * @return \Trek\MigrationIterator
     */
    public function next()
    {
        next($this->migrations);
        return $this;
    }
    
    /**
     * Resets iteration.
     * 
     * @return \Trek\MigrationIterator
     */
    public function rewind()
    {
        reset($this->migrations);
        return $this;
    }
    
    /**
     * Returns whether or not iteration is still valid.
     * 
     * @return bool
     */
    public function valid()
    {
        return $this->current() !== false;
    }

    /**
     * Formats the file name into a class name.
     * 
     * @param string $item The file name to format.
     * 
     * @return string
     */
    private function format($item)
    {
        $item = preg_replace('/^[0-9]+_/', '', $item);
        $item = str_replace('.php', '', $item);
        return $item;
    }
    
    /**
     * Returns the full path to the file.
     * 
     * @param string $item The file to get the full path to.
     * 
     * @return string
     */
    private function getFileName($item)
    {
        return $this->migrator->path() . DIRECTORY_SEPARATOR . $this->version->__toString() . DIRECTORY_SEPARATOR . $item;
    }
    
    /**
     * Returns the class name for the specified file name.
     * 
     * @param string $item The file name to format into a class name.
     * 
     * @return string
     */
    private function getClassName($item)
    {
        return '\\' . $this->migrator->ns() . '\\' . $this->version->ns() . '\\' . $this->format($item);
    }
    
    /**
     * Takes the specified file name, formats it into a fully qualified class name, instantiates it and then returns it.
     * 
     * @param string $item The file name to transform and instantiate.
     * 
     * @return \Trek\MigrationInterface
     */
    private function instantiate($item)
    {
        require_once $this->getFileName($item);
        
        $class = $this->getClassName($item);
        $class = new $class;
        
        if (!$class instanceof MigrationInterface) {
            throw new RuntimeException(
                "Class {$class} must derive from \Trek\MigrationInterface."
            );
        }
        
        return $class;
    }

    /**
     * Returns whether or not the specified file name is a valid migration file.
     * 
     * @param string $item The file name to check.
     * 
     * @return bool
     */
    private function isValidMigration($item)
    {
        return preg_match('/^[0-9]+_/', $item);
    }
    
    /**
     * Detects all migration files for the specified version and sets them.
     * 
     * @return void
     */
    private function detectMigrations()
    {
        $temp = [];
        $path = $this->migrator->path() . DIRECTORY_SEPARATOR . $this->version->__toString();
        
        // first we get an array of each file name
        foreach (new \DirectoryIterator($path) as $item) {
            $item = $item->getBasename();
            
            if (!$this->isValidMigration($item)) {
                continue;
            }
            
            $temp[] = $item;
        }
        
        // make sure they are listed in alphabetical order
        sort($temp);
        
        // then instantiate them
        foreach ($temp as $item) {
            $this->migrations[] = $this->instantiate($item);
        }
    }
}