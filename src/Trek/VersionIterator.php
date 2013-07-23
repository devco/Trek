<?php

namespace Trek;

class VersionIterator implements \Iterator
{
    const SORT_ASC = 'asc';

    const SORT_DESC = 'desc';

    private $index = 0;

    private $direction = self::SORT_ASC;

    private $versions = [];

    public function add(VersionInterface $version)
    {
        $this->versions[] = $version;
        return $this->{$this->direction}();
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

    public function asc()
    {
        usort($this->versions, 'version_compare');
        $this->versions = array_values($this->versions);
        $this->direction = self::SORT_ASC;
        return $this;
    }

    public function desc()
    {
        $this->up();
        $this->versions = array_reverse($this->versions);
        $this->direction = self::SORT_DESC;
        return $this;
    }

    public function direction()
    {
        return $this->direction();
    }

    public function prev()
    {
        --$this->index;
        return $this;
    }

    public function current()
    {
        return $this->versions[$this->index];
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
}