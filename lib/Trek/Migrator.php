<?php

namespace Trek;

/**
 * The main migrator class used for managing migrations.
 * 
 * @category Migrations
 * @package  Trek
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  MIT http://www.opensource.org/licenses/mit-license.php
 */
class Migrator
{
    /**
     * The default migration namespace.
     * 
     * @var string
     */
    const NS = 'Migration';
    
    /**
     * The default version file name.
     * 
     * @var string
     */
    const VERSION_FILE = 'version';
    
    /**
     * The base directory where migrations are kept.
     * 
     * @var string
     */
    private $dir;
    
    /**
     * The namespace to use for migrations.
     * 
     * @var string
     */
    private $ns;
    
    /**
     * The current version.
     * 
     * @var \Trek\Version
     */
    private $version;
    
    /**
     * Set when beginning a migration so rollback() can be called.
     * 
     * @var \Trek\Version
     */
    private $rollbackVersion;
    
    /**
     * Sets up the migrator.
     * 
     * @param string $dir The base directory that all migrations are loaded from.
     * @param string $ns  The namespace to use.
     * 
     * @return \Trek\Migrator
     */
    public function __construct($dir, $ns = self::NS)
    {
        // a namespace must be specified
        if (!$ns) {
            throw new \InvalidArgumentException('You must specify a namespace.');
        }
        
        // the directory must exist
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException('The directory "' . $dir . '" does not exist.');
        }
        
        // the directory must be writable
        if (!is_writable($dir)) {
            throw new \RuntimeException('The directory "' . $dir . '" must be writable in order to track versions.');
        }
        
        // resolve the path
        $this->dir = realpath($dir);
        
        // normalize namespace
        $this->ns = trim($ns, '\\');
    }
    
    /**
     * Upgrades to the latest version.
     * 
     * @return \Trek\Migrator
     */
    public function up()
    {
        return $this->to($this->versions()->last());
    }
    
    /**
     * Migrates to the specified version.
     * 
     * @param mixed $version The version to migrate to.
     * 
     * @return \Trek\Migrator
     */
    public function to($version)
    {
        $it  = $this->versions();
        $ver = new Version($version);
        $cur = $this->version();
        
        if ($it->exists($cur)) {
            $it->seek($cur);
        }
        
        $this->rollbackVersion = $cur;
        
        if ($ver->compare($it->current()) === 1) {
            while ($it->valid() && $ver->compare($it->current()) >= 0) {
                $it->migrations()->up();
                $it->next();
            }
        } else {
            while ($it->valid() && $ver->compare($it->current()) <= 0) {
                $it->migrations()->down();
                $it->prev();
            }
        }
        
        return $this;
    }
    
    /**
     * Rolls back the migration to the previous version.
     * 
     * @return \Trek\Migrator
     */
    public function rollback()
    {
        // ensure we can rollback
        if (!$this->rollbackVersion) {
            throw new \RuntimeException('Cannot rollback migration if a migration has not been initiated.');
        }
        
        // perform rollback (can be up or down)
        $this->to($this->rollbackVersion);
        
        // remove the rollback version
        $this->rollbackVersion = null;
        
        return $this;
    }
    
    /**
     * Returns the namespace associated to the current instance.
     * 
     * @return string
     */
    public function ns()
    {
        return $this->ns;
    }
    
    /**
     * Returns the base load path associated to the current instance.
     * 
     * @return string
     */
    public function path()
    {
        return $this->dir;
    }
    
    /**
     * Returns the version instance representing the current version of your code.
     * 
     * @return \Trek\Version
     */
    public function version()
    {
        if (!$this->version) {
            $this->version = $this->detectVersion();
            $this->bump($this->version);
        }
        return $this->version;
    }
    
    /**
     * Returns a version iterator representing the current migrator.
     * 
     * @return \Trek\VersionIterator
     */
    public function versions()
    {
        return new VersionIterator($this);
    }
    
    /**
     * Updates the version tracker to the specified version. If a version is not specified, it is bumped to the next available version.
     * 
     * @param mixed $version The version to track.
     * 
     * @return \Trek\Migrator
     */
    public function bump($version = null)
    {
        // Detect version we are bumping to.
        $version = $version ? $version : $this->versions()->next()->current();
        
        // Update stored version.
        file_put_contents($this->getVersionFile(), (string) $version);
        
        // Update cached version.
        $this->version = $version;
        
        return $this;
    }
    
    /**
     * Detects the current version and returns it.
     * 
     * @return \Trek\Version
     */
    private function detectVersion()
    {
        $file = $this->getVersionFile();
        if (file_exists($file)) {
            return new Version(file_get_contents($file));
        }
        return new Version;
    }
    
    /**
     * Returns the path to the version file.
     * 
     * @return string
     */
    private function getVersionFile()
    {
        return $this->dir . DIRECTORY_SEPARATOR . self::VERSION_FILE;
    }
}