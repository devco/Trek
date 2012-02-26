<?php

namespace Trek;

class Version
{
    const INITIAL = '0.0.0';
    
    private $major;
    
    private $minor;
    
    private $patch;
    
    private $words = array(
        0 => 'Zero',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine'
    );
    
    public function __construct($version = self::INITIAL)
    {
        $parts       = explode('.', $version, 3);
        $this->major = isset($parts[0]) ? (int) $parts[0] : 0;
        $this->minor = isset($parts[1]) ? (int) $parts[1] : 0;
        $this->patch = isset($parts[2]) ? (int) $parts[2] : 0;
    }
    
    public function __toString()
    {
        return $this->major . '.' . $this->minor . '.' . $this->patch;
    }
    
    public function getMajor()
    {
        return $this->major;
    }
    
    public function getPatch()
    {
        return $this->patch;
    }
    
    public function lt($version)
    {
        return $this->compare($version) === -1;
    }
    
    public function gt($version)
    {
        return $this->compare($version) === 1;
    }
    
    public function eq($version)
    {
        return $this->compare($version) === 0;
    }
    
    public function gteq($version)
    {
        return $this->gt($version) || $this->eq($version);
    }
    
    public function lteq($version)
    {
        return $this->lt($version) || $this->eq($version);
    }
    
    public function compare($version)
    {
        return version_compare($this->__toString(), (string) $version);
    }
    
    public function ns()
    {
        return $this->toWord($this->major) . '\\' . $this->toWord($this->minor) . '\\' . $this->toWord($this->patch);
    }
    
    public function isInitial()
    {
        return $this->__toString() === self::INITIAL;
    }
    
    private function toWord($part)
    {
        $word = '';
        foreach (str_split($part) as $num) {
            $word .= $this->words[(int) $num];
        }
        return $word;
    }
}