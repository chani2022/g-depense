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


    public function __construct(Imagine $imagine, Box $size)
    {
        $this->imagine = $imagine;
        $this->size = $size;
    }

    public function setMode(string $mode)
    {
        if (!in_array($mode, self::MODE)) {
            throw new InvalidArgumentException(sprintf('%s est invalid, les liste valides sont %s', $mode, implode(self::MODE)));
        }
    }

    public function resizeToThumbnail() {}
}
