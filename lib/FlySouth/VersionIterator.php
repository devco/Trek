<?php

namespace Trek;

class VersionIterator implements \Iterator
{
    private $migrator;
    
    private $index = 0;
    
    private $versions = array();
    
    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;
        $this->up();
    }
    
    public function first()
    {
        return $this->at(0);
    }
    
    public function last()
    {
        return $this->at(count($this->versions) - 1);
    }
    
    public function at($index)
    {
        if (!isset($this->versions[$index])) {
            throw new \InvalidArgumentException('The version at "' . $index . '" does not exist.');
        }
        return $this->versions[$index];
    }
    
    public function seek($version)
    {
        // Ensure the index was found and if not, throw an excpetion.
        if (!$this->exists($version)) {
            throw new \RuntimeException('Cannot seek to version "' . $version . '" because it does not exist.');
        }
        
        // Set the current index.
        $this->index = $this->find($version);
        
        return $this;
    }
    
    public function find($version)
    {
        return array_search((string) $version, $this->versions);
    }
    
    public function exists($version)
    {
        return in_array((string) $version, $this->versions);
    }
    
    public function migrations()
    {
        return new MigrationIterator($this->migrator, $this->current());
    }
    
    public function up()
    {
        $this->detectVersions();
        return $this;
    }
    
    public function down()
    {
        $this->detectVersions(true);
        return $this;
    }
    
    public function prev()
    {
        --$this->index;
        return $this;
    }
    
    public function current()
    {
        return new Version($this->versions[$this->index]);
    }
    
    public function key()
    {
        return $this->index;
    }
    
    public function next()
    {
        ++$this->index;
        return $this;
    }
    
    public function rewind()
    {
        $this->index = 0;
        return $this;
    }
    
    public function valid()
    {
        return $this->index > -1 && $this->index < count($this->versions);
    }
    
    private function detectVersions($reverse = false)
    {
        $versions = array();
        foreach (new \DirectoryIterator($this->migrator->path()) as $index => $version) {
            $version = $version->getBasename();
            
            // ensure that it is a version directory
            if ($this->isValidVersion($version)) {
                continue;
            }
            
            // add the version to the array
            $this->versions[] = $version;
        }
        
        // if downgrading
        if ($reverse) {
            $this->versions = array_reverse($this->versions);
        }
    }
    
    private function isValidVersion($version)
    {
        return preg_match('/^[^0-9]/', $version);
    }
}