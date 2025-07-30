<?php

namespace App\Tests\HandleImage;

use App\HandleImage\HandleImage;
use PHPUnit\Framework\TestCase;

class HandleImageTest extends TestCase
{
    private ?HandleImage $handler;
    private ?array $mockImages = [];

    protected function setUp(): void
    {
        $this->handler = new HandleImage();
        $this->mockImages = [];
    }

    protected function tearDown(): void
    {
        if ($this->mockImages) {
            foreach ($this->mockImages as $path) {
                unlink($path);
            }
        }
        $this->mockImages = null;
        $this->handler = null;
    }

    public function testOpenHI(): void
    {
        $path = $this->simulateCreateImage();

        $this->handler->open($path);

        $metadata = $this->handler->getImage()->metadata()->toArray();
        $pathActual = $metadata['filepath'];
        $this->assertSame($path, $pathActual);
    }

    public function testSetResizeImageHI(): void
    {
        $path = $this->simulateCreateImage();
        $widthExpected = 50;
        $heightExpected = 50;
        $this->handler->open($path)
            ->resize($widthExpected, $heightExpected);

        $this->assertEquals($widthExpected, $this->handler->getImage()->getSize()->getWidth());
        $this->assertEquals($heightExpected, $this->handler->getImage()->getSize()->getHeight());
    }

    public function testSaveHI(): void
    {
        $path = $this->simulateCreateImage();

        $this->handler->open($path)
            ->save($path);

        $metadata = $this->handler->getImage()->metadata()->toArray();
        $pathActual = $metadata['filepath'];
        $this->assertSame($path, $pathActual);
    }

    /**
     * renvoie le chemin du fichier
     */
    private function simulateCreateImage(): string
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test.png';
        $gb = imagecreatetruecolor(300, 300);
        imagepng($gb, $path);

        $this->mockImages[] = $path;
        return $path;
    }
}
