<?php

namespace Trek;

class Migrator
{
    const NS = 'Migration';
    
    const VERSION_FILE = 'version';
    
    private $dir;
    
    private $ns;
    
    private $version;
    
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
    
    public function up()
    {
        return $this->to($this->versions()->last());
    }
        
    public function to($version)
    {
        $it  = $this->versions();
        $ver = new Version($version);
        $cur = $this->version();
        
        if ($it->exists($cur)) {
            $it->seek($cur);
        }
        
        if ($ver->gt($it->current())) {
            while ($it->valid() && $ver->gteq($it->current())) {
                $it->migrations()->up();
                $it->next();
            }
        } else {
            while ($it->valid() && $ver->lteq($it->current())) {
                $it->migrations()->down();
                $it->prev();
            }
        }
        
        return $this;
    }
    
    public function ns()
    {
        return $this->ns;
    }
    
    public function path()
    {
        return $this->dir;
    }
    
    public function versionNs()
    {
        return '\\' . $this->ns . '\\' . $this->version->ns();
    }
    
    public function versionPath()
    {
        return $this->dir . DIRECTORY_SEPARATOR . $this->version;
    }
    
    public function version()
    {
        if (!$this->version) {
            $this->version = $this->detectVersion();
            $this->bump($this->version);
        }
        return $this->version;
    }
    
    public function versions()
    {
        return new VersionIterator($this);
    }
    
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
    
    private function detectVersion()
    {
        $file = $this->getVersionFile();
        if (file_exists($file)) {
            return new Version(file_get_contents($file));
        }
        return new Version;
    }
    
    private function getVersionFile()
    {
        return $this->dir . DIRECTORY_SEPARATOR . self::VERSION_FILE;
    }
}