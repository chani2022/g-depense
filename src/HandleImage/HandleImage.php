<?php

namespace App\HandleImage;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

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

    public function setMode(string $mode) {}

    public function resizeToThumbnail() {}
}
