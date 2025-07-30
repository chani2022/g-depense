<?php

namespace App\HandleImage;

use Imagine\Gd\Image;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class HandleImage
{

    private Imagine $imagine;
    private Image $image;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    public function open(string $pathname): self
    {
        $this->image = $this->imagine->open($pathname);

        return $this;
    }

    public function resize(int $width, int $heigth): self
    {
        $this->image->resize(new Box($width, $heigth));

        return $this;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function save(string $path, array $options = [])
    {
        $this->image->save($path, $options);
    }
}
