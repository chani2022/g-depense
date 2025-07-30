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
    /**
     * @param string $pathname  chemin du fichier
     * @return static
     */
    public function open(string $pathname): self
    {
        $this->image = $this->imagine->open($pathname);

        return $this;
    }
    /**
     * @param int $width    largeur
     * @param int $height   hauteur
     * @return static
     */
    public function resize(int $width, int $heigth): self
    {
        $this->image->resize(new Box($width, $heigth));

        return $this;
    }

    public function getImage(): Image
    {
        return $this->image;
    }
    /**
     * @param string $path  chemin du fichier
     * @param array<string, string> $options
     * @return static
     */
    public function save(string $path, array $options = []): self
    {
        $this->image->save($path, $options);

        return $this;
    }
}
