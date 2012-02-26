<?php

namespace Trek;

class MigrationIterator implements \Iterator
{
    private $migrations = array();
    
    private $migrator;
    
    private $version;

    public function __construct(Migrator $migrator, $version)
    {
        $this->migrator = $migrator;
        $this->version  = new Version($version);
        $this->detectMigrations();
    }
    
    public function up()
    {
        foreach ($this as $migration) {
            $migration->up();
        }
        $this->migrator->bump($this->version);
        return $this;
    }
    
    public function down()
    {
        foreach ($this as $migration) {
            $migration->down();
        }
        $this->migrator->bump($this->version);
        return $this;
    }

    public function current()
    {
        return current($this->migrations);
    }
    
    public function key()
    {
        return key($this->migrations);
    }
    
    public function next()
    {
        next($this->migrations);
        return $this;
    }
    
    public function rewind()
    {
        reset($this->migrations);
        return $this;
    }
    
    public function valid()
    {
        return $this->current() !== false;
    }

    private function format($item)
    {
        $item = preg_replace('/^[0-9]+_/', '', $item);
        $item = str_replace('.php', '', $item);
        return $item;
    }
    
    private function getFileName($item)
    {
        return $this->migrator->path() . DIRECTORY_SEPARATOR . $this->version->__toString() . DIRECTORY_SEPARATOR . $item;
    }
    
    private function getClassName($item)
    {
        return '\\' . $this->migrator->ns() . '\\' . $this->version->ns() . '\\' . $this->format($item);
    }
    
    private function instantiate($item)
    {
        require_once $this->getFileName($item);
        $class = $this->getClassName($item);
        return new $class;
    }

    private function isValidMigration($item)
    {
        return preg_match('/^[^[0-9]/', $item);
    }
    
    private function detectMigrations()
    {
        $path = $this->migrator->path() . DIRECTORY_SEPARATOR . $this->version->__toString();
        foreach (new \DirectoryIterator($path) as $item) {
            $item = $item->getBasename();
            if ($this->isValidMigration($item)) {
                continue;
            }
            $this->migrations[] = $this->instantiate($item);
        }
    }
}