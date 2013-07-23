<?php

namespace Test;
use Testes\Test\UnitAbstract;
use Trek\Version;
use Trek\VersionIterator;

class Versions extends UnitAbstract
{
    public function versionNamespacing()
    {
        $ver = new Version('1.12.123');
        $this->assert($ver->ns() === 'One\OneTwo\OneTwoThree');

        $ver = new Version('1.0.0-alpha');
        $this->assert($ver->ns() === 'One\Zero\Zero\Alpha');

        $ver = new Version('1.0.0-beta.1');
        $this->assert($ver->ns() === 'One\Zero\Zero\Beta\One');

        $ver = new Version('1.0.0-rc.12');
        $this->assert($ver->ns() === 'One\Zero\Zero\Rc\OneTwo');
    }

    public function versionOrdering()
    {
        $ordered = [
            '0.1.0',
            '0.1.1',
            '0.2.0',
            '1.0.0-alpha',
            '1.0.0-alpha.1',
            '1.0.0-alpha.2',
            '1.0.0-beta',
            '1.0.0-beta.1',
            '1.0.0-beta.2',
            '1.0.0-rc',
            '1.0.0-rc.1',
            '1.0.0-rc.2',
            '1.0.0',
            '10.0.0'
        ];

        $shuffled = $ordered;
        shuffle($shuffled);
        $reversed = array_reverse($ordered);
        $versions = new VersionIterator;

        foreach ($shuffled as $version) {
            $versions->add(new Version($version));
        }

        $versions->asc();

        foreach ($ordered as $index => $version) {
            $this->assert($versions->at($index)->compare($version) === 0, "{$version} !== {$versions->at($index)}");
        }

        $versions->desc();

        foreach ($reversed as $index => $version) {
            $this->assert($versions->at($index)->compare($version) === 0, "{$version} !== {$versions->at($index)}");
        }
    }
}