<?php

declare(strict_types=1);

namespace MaliBoot\Utils;

use PhpZip\ZipFile;

class Zip
{
    /**
     * 打包.
     */
    public static function pack(string $sourcePath, ?string $filename = null, ?string $targetPath = null): ?string
    {
        if (! File::exists($sourcePath)) {
            throw new \RuntimeException("Directory to be decompressed does not exist {$sourcePath}");
        }

        $filename = $filename ?? File::name($sourcePath);
        $targetPath = $targetPath ?? File::dirname($sourcePath);
        $targetPath = $targetPath ?: File::dirname($sourcePath);

        File::ensureDirectoryExists($targetPath);

        $zipFilename = str_contains($filename, '.zip') ? $filename : $filename . '.zip';
        $zipFilepath = "{$targetPath}/{$zipFilename}";

        while (File::exists($zipFilepath)) {
            $basename = File::name($zipFilepath);
            $zipCount = count(File::glob("{$targetPath}/{$basename}*.zip"));

            $zipFilename = $basename . $zipCount . '.zip';
            $zipFilepath = "{$targetPath}/{$zipFilename}";
        }

        // Compression
        $zipFile = new ZipFile();
        $zipFile->addDirRecursive($sourcePath, $filename);
        $zipFile->saveAsFile($zipFilepath);

        return $targetPath;
    }

    /**
     * 解压.
     */
    public function unpack(string $sourcePath, ?string $targetPath = null): ?string
    {
        try {
            // Detects the file type and unpacks only zip files
            $mimeType = File::mimeType($sourcePath);
        } catch (\Throwable $e) {
            throw new \RuntimeException("Unzip failed {$e->getMessage()}");
        }

        // Get file types (only directories and zip files are processed)
        $type = match (true) {
            default => null,
            str_contains($mimeType, 'directory') => 1,
            str_contains($mimeType, 'zip') => 2,
        };

        if (is_null($type)) {
            throw new \RuntimeException("unsupport mime type {$mimeType}");
        }

        // Make sure the unzip destination directory exists
        $targetPath = $targetPath ?? sys_get_temp_dir();
        if (empty($targetPath)) {
            throw new \RuntimeException('targetPath cannot be empty.');
        }

        if (! is_dir($targetPath)) {
            File::ensureDirectoryExists($targetPath);
        }

        // Empty the directory to avoid leaving files of other plugins
        File::cleanDirectory($targetPath);

        // Directory without unzip operation, copy the original directory to the temporary directory
        if ($type == 1) {
            File::copyDirectory($sourcePath, $targetPath);

            // Make sure the directory decompression level is the top level of the plugin directory
            $this->ensureDoesntHaveSubdir($targetPath);

            return $targetPath;
        }

        if ($type == 2) {
            // unzip
            $zipFile = new ZipFile();
            $zipFile = $zipFile->openFile($sourcePath);
            $zipFile->extractTo($targetPath);

            // Make sure the directory decompression level is the top level of the plugin directory
            $this->ensureDoesntHaveSubdir($targetPath);

            // Decompress to the specified directory
            return $targetPath;
        }

        return null;
    }

    public function ensureDoesntHaveSubdir(string $targetPath): string
    {
        $targetPath = $targetPath ?? sys_get_temp_dir();

        $pattern = sprintf('%s/*', rtrim($targetPath, DIRECTORY_SEPARATOR));
        $files = File::glob($pattern);

        if (count($files) > 1) {
            return $targetPath;
        }

        $tmpDir = $targetPath . '-subdir';
        File::ensureDirectoryExists($tmpDir);

        $firstEntryname = File::name(current($files));

        File::copyDirectory($targetPath . "/{$firstEntryname}", $tmpDir);
        File::cleanDirectory($targetPath);
        File::copyDirectory($tmpDir, $targetPath);
        File::deleteDirectory($tmpDir);

        return $targetPath;
    }
}
