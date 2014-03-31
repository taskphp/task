<?php

namespace Task\Plugin;

use Task\Plugin\PluginInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class FilesystemPlugin extends Filesystem implements PluginInterface
{
    public function copy($source, $target)
    {
        $target = rtrim($target, '/');
        $source = rtrim($source, '/');

        if (is_file($source)) {
            if (is_dir($target)) {
                return parent::copy($source, $target.DIRECTORY_SEPARATOR.basename($source));
            } elseif (is_link($source)) {
                return $this->symlink(readlink($source), $target);
            } else {
                return parent::copy($source, $target);
            }
        } elseif (is_dir($source)) {
            if (is_file($target)) {
                throw new \RuntimeException("Cannot copy directory to file");
            } else {
                return $this->mirror($source, $target);
            }
        }

        throw new FileNotFoundException("Could not copy $source to $target");
    }

    public function copyTree($source, $target, array $include, array $exclude = [])
    {
        $target = rtrim($target, '/');
        $source = rtrim($source, '/');

        foreach ($iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        ) as $file) {
            if (!$file->isDir()) {
                $path = substr($file->getPathname(), strlen("$source/"));
                if ($this->match($include, $path)) {
                    if ($this->match($exclude, $path)) {
                    } else {
                        $this->copy("$source/$path", "$target/$path");
                    }
                }
            }
        }
    }

    public function match($patterns, $match)
    {
        if (!is_array($patterns)) {
            $patterns = [$patterns];
        }

        foreach ($patterns as $pattern) {
            if (fnmatch($pattern, $match)) {
                return true;
            }
        }

        return false;
    }
}
