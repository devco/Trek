<?php

namespace Trek;
use RuntimeException;

class MigrationIterator implements \Iterator
{
    private $migrations = [];

    private $migrator;

    private $version;

    private static $classFilters = [];

    public function __construct(Migrator $migrator, VersionInterface $version)
    {
        $this->migrator = $migrator;
        $this->version  = $version;
        $this->detectMigrations();
    }

    public static function setClassFilters(array $filters)
    {
        self::$classFilters = [];

        array_walk($filters, function($filter) {
            self::$classFilters[] = $filter;
        });
    }

    public function up()
    {
        foreach ($this as $migration) {
            $migration->setUp();
            $migration->up();
            $migration->tearDown();
        }

        return $this;
    }

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
        $class = new $class;

        if (!$class instanceof MigrationInterface) {
            throw new RuntimeException(
                "Class {$class} must derive from \Trek\MigrationInterface."
            );
        }

        if (count(self::$classFilters)) {
            $countFiltersLeft = count(array_filter(self::$classFilters, function($classFilter) use($class) {
                return get_parent_class($class) === $classFilter;
            }));

            if ($countFiltersLeft === 0) {
                return false;
            }
        }

        return $class;
    }

    private function isValidMigration($item)
    {
        return preg_match('/^[0-9]+_/', $item);
    }

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
        natsort($temp);

        // then instantiate them
        foreach ($temp as $item) {
            if ($itemClass = $this->instantiate($item)) {
                $this->migrations[] = $itemClass;
            }
        }
    }
}
