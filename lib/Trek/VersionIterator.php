<?php

namespace Trek;

/**
 * Represents multiple versions.
 * 
 * @category Versioning
 * @package  Trek
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  MIT http://www.opensource.org/licenses/mit-license.php
 */
class VersionIterator implements \Iterator
{
    /**
     * The associated migrator.
     * 
     * @var \Trek\Migrator
     */
    private $migrator;
    
    /**
     * The current index.
     * 
     * @var int
     */
    private $index = 0;
    
    /**
     * The versions in the iterator.
     * 
     * @var array
     */
    private $versions = array();
    
    /**
     * Sets up the version iterator.
     * 
     * @param \Trek\Migrator $migrator The main migrator instance.
     * 
     * @return \Trek\VersionIterator
     */
    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;
        $this->up();
    }
    
    /**
     * Returns the first version.
     * 
     * @return \Trek\Version
     */
    public function first()
    {
        return $this->at(0);
    }
    
    /**
     * Returns the last version.
     * 
     * @return \Trek\Version
     */
    public function last()
    {
        return $this->at(count($this->versions) - 1);
    }
    
    /**
     * Returns the version at the specified index.
     * 
     * @return \Trek\Version
     */
    public function at($index)
    {
        if (!isset($this->versions[$index])) {
            throw new \InvalidArgumentException('The version at "' . $index . '" does not exist.');
        }
        return $this->versions[$index];
    }
    
    /**
     * Seeks to the specified version.
     * 
     * @param mixed $version The version to seek to.
     * 
     * @return \Trek\VersionIterator
     */
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
    
    /**
     * Returns the index to the specified version or false if it is not found.
     * 
     * @param mixed $version The version to search for.
     * 
     * @return int|false
     */
    public function find($version)
    {
        return array_search((string) $version, $this->versions);
    }
    
    /**
     * Returns whether or not the specified version exists.
     * 
     * @param mixed $version The version to check for.
     * 
     * @return bool
     */
    public function exists($version)
    {
        return in_array((string) $version, $this->versions);
    }
    
    /**
     * Returns a migration iterator representing the current version.
     * 
     * @return \Trek\MigrationIterator
     */
    public function migrations()
    {
        return new MigrationIterator($this->migrator, $this->current());
    }
    
    /**
     * Sorts the versions from least to greatest.
     * 
     * @return \Trek\VersionIterator
     */
    public function up()
    {
        $this->detectVersions();
        return $this;
    }
    
    /**
     * Sorts the versions from greatest to least.
     * 
     * @return \Trek\VersionIterator
     */
    public function down()
    {
        $this->detectVersions(true);
        return $this;
    }
    
    /**
     * Moves back one version.
     * 
     * @return \Trek\VersionIterator
     */
    public function prev()
    {
        --$this->index;
        return $this;
    }
    
    /**
     * Returns the current version.
     * 
     * @return \Trek\Version
     */
    public function current()
    {
        return new Version($this->versions[$this->index]);
    }
    
    /**
     * Returns the current key.
     * 
     * @return int
     */
    public function key()
    {
        return $this->index;
    }
    
    /**
     * Moves forward one version.
     * 
     * @return \Trek\VersionIterator
     */
    public function next()
    {
        ++$this->index;
        return $this;
    }
    
    /**
     * Resets iteration.
     * 
     * @return \Trek\VersionIterator
     */
    public function rewind()
    {
        $this->index = 0;
        return $this;
    }
    
    /**
     * Returns whether or not iteration is still valid.
     * 
     * @return bool
     */
    public function valid()
    {
        return $this->index > -1 && $this->index < count($this->versions);
    }
    
    /**
     * Detects the available versions.
     * 
     * @return void
     */
    private function detectVersions($reverse = false)
    {
        $this->versions = array();
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
    
    /**
     * Returns whether or not the specified version is valid.
     * 
     * @param string $version The version to check.
     * 
     * @return bool
     */
    private function isValidVersion($version)
    {
        return preg_match('/^[^0-9]/', $version);
    }
}