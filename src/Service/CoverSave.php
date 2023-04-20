<?php

namespace App\Service;

use App\Exceptions\InvalidCoverException;
use Symfony\Component\HttpFoundation\File\File;

class CoverSave
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

    /**
     * @param File $file
     * @param string $storePath
     * @return string
     * @throws InvalidCoverException
     */
    public function execute(File $file, string $storePath): string
    {
        if (!exif_imagetype($file->openFile()->getPathname())) {
            throw new InvalidCoverException('Cover is not an image');
        }

        if (!in_array($file->guessExtension(), self::ALLOWED_EXTENSIONS)) {
            throw new InvalidCoverException('Cover is jpeg or png');
        }

        $fileName = 'image_' . date('Y-m-d-H-i-s') . '_' . uniqid() . '.' . $file->guessExtension();
        $file->move($storePath, $fileName);

        return $fileName;
    }

}