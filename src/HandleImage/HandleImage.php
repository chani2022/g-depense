<?php

namespace App\HandleImage;

use Doctrine\Common\Cache\Psr6\InvalidArgument;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use InvalidArgumentException;

class HandleImage
{

    const MODE = [
        ImageInterface::THUMBNAIL_INSET,
        ImageInterface::THUMBNAIL_OUTBOUND
    ];
    private Imagine $imagine;
    private Box $size;
    private string|int $mode;


    public function __construct(Box $size)
    {
        $this->imagine = new Imagine();
        $this->size = $size;
        $this->mode = '';
    }

    public function setMode(string|int $mode)
    {
        if (!in_array($mode, self::MODE)) {
            throw new InvalidArgumentException(sprintf('%s est invalid, les liste valides sont %s', $mode, implode(self::MODE)));
        }

        $this->mode = $mode;
    }

    public function getMode(): string|int
    {
        return $this->mode;
    }

    public function resizeToThumbnail() {}
}
