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

    // public function testResizeToThumbnail(): void
    // {
    //     $this->handler->resizeToThumbnail();

    //     $this->assertSame(HandleImage::WIDTH, )
    // }
}
