<?php

namespace PsrLinter\Cli;

class Io
{
    /**
     * Reads file and returns its contents
     * @return string
     */
    public static function read($path) : string
    {
        if (!file_exists($path)) {
            throw new IoException("File not found: $path");
        }

        if (!is_file($path)) {
            throw new IoException("$path is not a file");
        }

        if (!is_readable($path)) {
            throw new IoException('Permission denied');
        }

        return file_get_contents($path);
    }

    /**
     * @return bool
     */
    public static function isDir($path) : bool
    {
        if (!file_exists($path)) {
            throw new IoException("File or directory not found: $path");
        }

        if (!is_readable($path)) {
            throw new IoException('Permission denied');
        }

        return is_dir($path);
    }
}
