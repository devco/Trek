<?php

namespace Trek;

class Version implements VersionInterface
{
    private $major;

    private $minor;

    private $patch;

    private $preRelease;

    private $preReleaseNumber;

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
        $str = $this->major . '.' . $this->minor . '.' . $this->patch;

        if ($this->preRelease) {
            $str .= '-' . $this->preRelease;
        }

        if ($this->preReleaseNumber) {
            $str .= '.' . $this->preReleaseNumber;
        }

        return $str;
    }

    public function compare($version)
    {
        return version_compare($this->__toString(), (string) $version);
    }

    public function ns()
    {
        $ns = implode('\\', array(
            $this->toWord($this->major),
            $this->toWord($this->minor),
            $this->toWord($this->patch)
        ));

        if ($this->preRelease) {
            $ns .= '\\' . ucfirst(strtolower($this->preRelease));
        }

        if ($this->preReleaseNumber) {
            $ns .= '\\' . $this->toWord($this->preReleaseNumber);
        }

        return $ns;
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
        // parse out <major>.<minor>.<patch>-<pre-release>.<pre-release-number>
        preg_match(self::REGEX, $version, $versions);

        $this->major = isset($versions[1]) ? (int) $versions[1] : null;
        $this->minor = isset($versions[2]) ? (int) $versions[2] : null;
        $this->patch = isset($versions[3]) ? (int) $versions[3] : null;
        $this->preRelease = isset($versions[4]) ? $versions[4] : null;
        $this->preReleaseNumber = isset($versions[5]) ? $versions[5] : null;
    }
}