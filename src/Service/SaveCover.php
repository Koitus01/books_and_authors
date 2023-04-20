<?php

namespace App\Service;

use App\Exceptions\InvalidCoverException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class SaveCover
{
    public const ALLOWED_TYPES = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg'
    ];

    public const ALLOWED_EXTENSIONS = [
        'png',
        'jpg',
        'jpeg'
    ];
    protected string $filePath = '';

    public function __construct(private readonly Filesystem $filesystem, private readonly string $coverPath)
    {
    }

    /**
     * @param File $file
     * @return string
     * @throws InvalidCoverException
     */
    public function execute(File $file): string
    {
        if (!exif_imagetype($file->openFile()->getPathname())) {
            throw new InvalidCoverException('Cover is not an image');
        }

        if (!in_array($file->guessExtension(), self::ALLOWED_EXTENSIONS)) {
            throw new InvalidCoverException('Cover is not jpeg or png');
        }

        $fileName = 'image_' . date('Y-m-d-H-i-s') . '_' . uniqid() . '.' . $file->guessExtension();
        $file->move($this->coverPath, $fileName);

        $this->filePath = $this->coverPath . $fileName;
        return $fileName;
    }

    public function rollback(): void
    {
        if ($this->filesystem->exists($this->filePath)) {
            $this->filesystem->remove($this->filePath);
        }
    }

}