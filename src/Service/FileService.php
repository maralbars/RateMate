<?php

namespace App\Service;

class FileService
{
    private string $filePath;

    private string $fileName;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function setFileName(string $fileName = ''): void
    {
        if (!empty($fileName)) {
            $this->fileName = sprintf($this->filePath, $fileName);
        }
    }

    public function saveJson(array $data, string $fileName = ''): void
    {
        $this->setFileName($fileName);

        file_put_contents($this->fileName, json_encode($data));
    }

    public function getJson(string $fileName = ''): array
    {
        $this->setFileName($fileName);

        if (!$this->jsonExists()) {
            return [];
        }

        return json_decode(file_get_contents($this->fileName), true) ?? [];
    }

    public function jsonExists(string $fileName = ''): bool
    {
        $this->setFileName($fileName);

        return file_exists($this->fileName);
    }
}
