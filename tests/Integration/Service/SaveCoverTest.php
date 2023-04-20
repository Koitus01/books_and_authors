<?php

namespace App\Tests\Integration\Service;

use App\Exceptions\InvalidCoverException;
use App\Service\SaveCover;
use App\Tests\Integration\BaseIntegration;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class SaveCoverTest extends BaseIntegration
{
    public function testExecute()
    {
        $sc = new SaveCover(new Filesystem(), './fixtures');
        $uploadedFile = new File(
            __DIR__ . '/../Fixtures/1.jpg'
        );
        $mock = \Mockery::mock($uploadedFile);
        $mock->shouldReceive('move')->andReturn($uploadedFile);

        $result = $sc->execute($mock);

        $this->assertNotEmpty($result);
    }

    public function testExecuteWithPhpFilesThrow()
    {
        $this->expectException(InvalidCoverException::class);
        $this->expectExceptionMessage('Cover is not an image');
        $sc = new SaveCover(new Filesystem(), './fixtures');
        $uploadedFile = new File(
            __DIR__ . '/../Fixtures/somePhpFile.php'
        );

        $sc->execute($uploadedFile);
    }

    public function testExecuteWithNotPngOrJpgThrow()
    {
        $this->expectException(InvalidCoverException::class);
        $this->expectExceptionMessage('Cover is not jpeg or png');
        $sc = new SaveCover(new Filesystem(), './fixtures');
        $uploadedFile = new File(
            __DIR__ . '/../Fixtures/i.webp'
        );

        $sc->execute($uploadedFile);
    }
}
