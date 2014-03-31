<?php

namespace Task\Plugin\Archive;

class ArchiveBuilder {
    protected $path;
    protected $type;
    protected $extension;
    protected $compression;

    protected $supportedTypes = ['tar', 'zip'];
    protected $supportedCompressionStreams = ['zlib', 'bzip2'];
    protected $compressionConstants = [
        'zlib' => \Phar::GZ,
        'bzip2' => \Phar::BZ2
    ];
    protected $compressionExtensions = [
        'zlib' => 'gz',
        'bzip2' => 'bz2'
    ];

    public function __construct($path = null) {
        $this->setPath($path);
    }

    public static function create($path = null) {
        return new static($path);
    }

    public function setPath($path) {
        $this->path = $path;
        return $this;
    }

    public function getPath() {
        return $this->path;
    }

    public function setType($type) {
        if (!in_array($type, $this->supportedTypes)) {
            throw new Exception("Unsupported type [$type]");
        }

        if ($compression = $this->getCompression() && $type == 'zip') {
            throw new Exception('Cannot compress zip archives');
        }

        $this->type = $type;
        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function setCompression($compression) {
        if (!in_array($compression, $this->supportedCompressionStreams)) {
            throw new Exception("Unsupported compression stream [$compression]");
        }

        if ($this->getType() == 'zip') {
            throw new Exception('Cannot compress zip archives');
        }

        $this->compression = $compression;
        return $this;
    }

    public function getCompression() {
        return $this->compression;
    }

    public function setExtension($extension) {
        $this->extension = $extension;
        return $this;
    }

    public function getExtension() {
        return $this->extension;
    }

    public function getPathname() {
        if ($extension = $this->getExtension()) {
            $pathname = [$this->getPath(), $extension];
        } else {
            $pathname = [$this->getPath(), $this->getType()];

            if ($compression = $this->getCompression()) {
                $pathname[] = $this->compressionExtensions[$compression];
            }
        }

        return implode('.', $pathname);
    }

    public function validate() {
        return $this->getPath() && $this->getType();
    }

    public function buildFromDirectory($baseDir, $include = null) {
        if (!$this->validate()) {
            throw new Exception("Missing information!");
        }

        $tmp = sprintf('%s/archive%s.%s',
            sys_get_temp_dir(),
            time(),
            $this->getType()
        );

        $phar = new \PharData($tmp);
        $phar->buildFromDirectory($baseDir, $include);

        if ($compression = $this->getCompression()) {
            $phar->compress($this->compressionConstants[$compression]);
            unlink($tmp);
            $tmp .= ".{$this->compressionExtensions[$compression]}";
        }

        $archive = $this->getPathname();
        rename($tmp, $archive);
        chmod($archive, 0644);

        return $archive;
    }
}
