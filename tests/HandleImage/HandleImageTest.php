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
    private ?array $mockImages = [];

    protected function setUp(): void
    {
        $this->imagine = new Imagine();
        $this->size = new Box(10, 10);
        $this->handler = new HandleImage($this->imagine, $this->size);
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
        $path = $this->simulateOpenImage();

        $info = $this->handler->getImage()->metadata()->toArray();
        $pathActual = $info['filepath'];
        $this->assertSame($path, $pathActual);
    }

    /**
     * -----------------thumbnail--------------
     */
    public function testThumbnailThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->handler->thumbnail();
    }

    public function testThumbnailSuccess(): void
    {
        $this->simulateOpenImage();

        $this->handler->setMode(ImageInterface::THUMBNAIL_INSET)
            ->thumbnail();

        $this->assertSameSize(
            [$this->size->getWidth(), $this->size->getHeight()],
            [$this->handler->getImage()->getSize()->getWidth(), $this->handler->getImage()->getSize()->getHeight()]
        );
    }

    public function testSaveHI(): void
    {
        $path = $this->simulateOpenImage();

        $this->handler->saveHI();

        $this->assertSame($path, $this->handler->getImage()->metadata()['filepath']);
    }

    private function simulateCreateImage(): string
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test.png';
        $gb = imagecreatetruecolor(10, 10);
        imagepng($gb, $path);

        return $path;
    }

    private function simulateOpenImage(): string
    {
        $path = $this->simulateCreateImage();
        $this->handler->open($path);

        return $path;
    }

    // public function testResizeToThumbnail(): void
    // {
    //     $this->handler->resizeToThumbnail();

    //     $this->assertSame(HandleImage::WIDTH, )
    // }
}
