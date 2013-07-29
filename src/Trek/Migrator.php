<?php

namespace Trek;
use Exception;
use InvalidArgumentException;
use RuntimeException;

class Migrator
{
    const NS = 'Migration';

    const VERSION_FILE = 'version';

    private $path;

    private $ns;

    private $version;

    private $versions;

    private $rollbackVersion;

    private $error;

    public function __construct($path, $ns = self::NS)
    {
        if (!$ns) {
            throw new InvalidArgumentException('You must specify a namespace.');
        }

        if (!is_dir($path)) {
            throw new InvalidArgumentException('The path "' . $path . '" does not exist.');
        }

        if (!is_writable($path)) {
            throw new RuntimeException(
                'The path "' . $path . '" must be writable in order to track versions.'
            );
        }

        $this->path = realpath($path);
        $this->ns = trim($ns, '\\');

        $this->detectVersions();
    }

    public function up()
    {
        return $this->to($this->versions()->last());
    }

    public function down()
    {
        return $this->to($this->versions()->first());
    }

    public function to($version)
    {
        $this->error = null;

        try {
            $this->tryTo($version);
            $this->bump($version);
        } catch (Exception $e) {
            $this->rollback();
            $this->error = $e;
        }

        return $this;
    }

    public function rollback()
    {
        if (!$this->rollbackVersion) {
            return $this;
        }

        try {
            $this->tryTo($this->rollbackVersion);
        } catch (Exception $e) {
            throw new RuntimeException(
                "Cannot rollback to {$this->rollbackVersion}.",
                $e->getCode(),
                $e
            );
        }

        $this->rollbackVersion = null;

        return $this;
    }

    public function ns()
    {
        return $this->ns;
    }

    public function path()
    {
        return $this->path;
    }

    public function version()
    {
        if (!$this->version) {
            $this->version = $this->detectVersion();
        }

        return $this->version;
    }

    public function versions()
    {
        return $this->versions;
    }

    public function isVersioned()
    {
        return file_exists($this->getVersionFile());
    }

    public function bump($version = null)
    {
        $version = $version ? new Version($version) : $this->versions()->next()->current();

        file_put_contents($this->getVersionFile(), (string) $version);

        $this->version = $version;

        return $this;
    }

    public function error()
    {
        return $this->error;
    }

    private function tryTo($version)
    {
        $versions = $this->versions();
        $version = new Version($version);
        $current = $this->version();
        $diff = $version->compare($current);

        if ($versions->exists($current)) {
            $versions->seek($current);
        }

        $this->rollbackVersion = $current;

        if ($diff === 1) {
            while ($versions->valid() && $version->compare($versions->current()) >= 0) {
                $migrations = new MigrationIterator($this, $versions->current());
                $migrations->up();
                $versions->next();
            }
        } elseif ($diff === -1) {
            while ($versions->valid() && $version->compare($versions->current()) === -1) {
                $migrations = new MigrationIterator($this, $versions->current());
                $migrations->down();
                $versions->prev();
            }
        }
    }

    private function detectVersions()
    {
        $this->versions = new VersionIterator;

        foreach (new \DirectoryIterator($this->path) as $index => $version) {
            $version = $version->getBasename();

            if ($this->isValidVersion($version)) {
                $this->versions->add(new Version($version));
            }
        }
    }

    private function isValidVersion($version)
    {
        return preg_match(VersionInterface::REGEX, $version);
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
        return $this->path . DIRECTORY_SEPARATOR . self::VERSION_FILE;
    }
}