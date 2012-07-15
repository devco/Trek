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
    
    /**
     * Sets up the version instance.
     * 
     * @param mixed $version The verions to use. Defaults to 0.0.0.
     * 
     * @return Trek\Version
     */
    public function __construct($version = self::INITIAL)
    {
        $this->parse($version);
    }
    
    /**
     * Returns the version as a string.
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->major . '.' . $this->minor . '.' . $this->patch;
    }
    
    /**
     * Compares this version to the specified version.
     * 
     * @param mixed $version The version to compare to.
     * 
     * @return int
     */
    public function compare($version)
    {
        return version_compare($this->__toString(), (string) $version);
    }
    
    /**
     * Returns a namespace representing the version.
     * 
     * @return string
     */
    public function ns()
    {
        return implode('\\', array(
            $this->toWord($this->major),
            $this->toWord($this->minor),
            $this->toWord($this->patch)
        ));
    }
    
    /**
     * Converts the version part to a word.
     * 
     * @param string $part The part to convert to a word.
     * 
     * @return string
     */
    private function toWord($part)
    {
        $word = '';
        foreach (str_split($part) as $char) {
            $word .= $this->map[$char];
        }
        return $word;
    }
    
    /**
     * Parses the version into its meaningful parts.
     * 
     * @param string $version The version to parse.
     * 
     * @return void
     */
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