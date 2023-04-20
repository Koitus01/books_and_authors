<?php

namespace App\Tests\Integration\Service;

use App\Exceptions\InvalidCoverException;
use App\Service\SaveCover;
use App\Tests\Integration\BaseIntegration;
use Symfony\Component\HttpFoundation\File\File;

class SaveCoverTest extends BaseIntegration
{
    public function testExecute()
    {
        /** @var SaveCover $sc */
        $sc = $this->container->get(SaveCover::class);
        $uploadedFile = new File(
            __DIR__ . '/../Fixtures/1.jpg'
        );
        $uploadPath = __DIR__ . '/../Fixtures/upload/cover';
        $mock = \Mockery::mock($uploadedFile);
        $mock->shouldReceive('move')->andReturn($uploadedFile);

        $result = $sc->execute($mock, $uploadPath);

        $this->assertNotEmpty($result);
    }

    public function testExecuteWithPhpFilesThrow()
    {
        $this->expectException(InvalidCoverException::class);
        $this->expectExceptionMessage('Cover is not an image');
        /** @var SaveCover $sc */
        $sc = $this->container->get(SaveCover::class);
        $uploadedFile = new File(
            __DIR__ . '/../Fixtures/somePhpFile.php'
        );
        $uploadPath = __DIR__ . '/../Fixtures/upload/cover';

        $sc->execute($uploadedFile, $uploadPath);
    }

    public function testExecuteWithNotPngOrJpgThrow()
    {
        $this->expectException(InvalidCoverException::class);
        $this->expectExceptionMessage('Cover is not jpeg or png');
        /** @var SaveCover $sc */
        $sc = $this->container->get(SaveCover::class);
        $uploadedFile = new File(
            __DIR__ . '/../Fixtures/i.webp'
        );
        $uploadPath = __DIR__ . '/../Fixtures/upload/cover';

        $sc->execute($uploadedFile, $uploadPath);
    }
}
