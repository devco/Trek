<?php

namespace Trek;

/**
 * Represents a verison.
 * 
 * @category Versioning
 * @package  Trek
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  MIT http://www.opensource.org/licenses/mit-license.php
 */
class Version implements VersionInterface
{
    /**
     * The default version.
     * 
     * @var string
     */
    const INITIAL = '0.0.0';
    
    /**
     * The major version.
     * 
     * @var int
     */
    private $major;
    
    /**
     * The minor version.
     * 
     * @var int
     */
    private $minor;
    
    /**
     * The patch version.
     * 
     * @var int
     */
    private $patch;
    
    /**
     * Word mapping.
     * 
     * @var array
     */
    private $map = array(
        0  => 'Zero',
        1  => 'One',
        2  => 'Two',
        3  => 'Three',
        4  => 'Four',
        5  => 'Five',
        6  => 'Six',
        7  => 'Seven',
        8  => 'Eight',
        9  => 'Nine'
    );
    
    public function __construct($version = self::INITIAL)
    {
        $this->parse($version);
    }
    
    public function __toString()
    {
        return $this->major . '.' . $this->minor . '.' . $this->patch;
    }
    
    public function compare($version)
    {
        return version_compare($this->__toString(), (string) $version);
    }
    
    public function ns()
    {
        return implode('\\', array(
            $this->toWord($this->major),
            $this->toWord($this->minor),
            $this->toWord($this->patch)
        ));
    }
    
    private function toWord($part)
    {
        $word = '';
        foreach (str_split($part) as $char) {
            $word .= $this->map[$char];
        }
        return $word;
    }
    
    private function parse($version)
    {
        // parse out <major>.<minor>.<patch>
        preg_match('/(\d+)\.?(\d*)\.?(\d*)\.?(\d*)/', $version, $versions);

        // set version parts
        $this->major = isset($versions[1]) ? (int) $versions[1] : null;
        $this->minor = isset($versions[2]) ? (int) $versions[2] : null;
        $this->patch = isset($versions[3]) ? (int) $versions[3] : null;
    }
}