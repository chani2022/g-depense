<?php

namespace App\Tests\HandleImage;

use App\HandleImage\HandleImage;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class HandleImageTest extends TestCase
{
    private ?HandleImage $handler;
    private ?Imagine $imagine;
    private ?Box $size;

    protected function setUp(): void
    {
        $this->imagine = new Imagine();
        $this->size = new Box(40, 40);
        $this->handler = new HandleImage($this->imagine, $this->size);
    }

    protected function tearDown(): void
    {
        $this->handler = null;
        $this->imagine = null;
        $this->size = null;
    }

    /**
     * ----------------mode----------------
     */
    public function testSetModeThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->handler->setMode('invalid');
    }
    /**
     * @dataProvider modeValid
     */
    public function testSetModeValid(string|int $mode): void
    {
        $this->handler->setMode($mode);
        $this->assertSame($mode, $this->handler->getMode());
    }

    /**
     * ------------------size----------------
     */
    public function testSize(): void
    {
        $this->handler->setSize(new Box(10, 10));

        $this->assertSame(10, $this->handler->getSize()->getWidth());
        $this->assertSame(10, $this->handler->getSize()->getHeight());
    }

    public static function modeValid(): array
    {
        return [
            [ImageInterface::THUMBNAIL_INSET],
            [ImageInterface::THUMBNAIL_OUTBOUND]

        ];
    }

    /**
     * -------------------open--------------
     */
    public function testOpenHI(): void
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test.png';
        $gb = imagecreatetruecolor(10, 10);
        imagepng($gb, $path);

        $this->handler->open($path);

        $info = $this->imagine->getMetadataReader()->readFile($path)->toArray();
        $pathActual = $info['filepath'];
        $this->assertSame($path, $pathActual);
    }

    // public function testResizeToThumbnail(): void
    // {
    //     $this->handler->resizeToThumbnail();

    //     $this->assertSame(HandleImage::WIDTH, )
    // }
}
