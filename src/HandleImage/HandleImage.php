<?php

namespace App\HandleImage;

use Doctrine\Common\Cache\Psr6\InvalidArgument;
use Imagine\Gd\Image;
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
    private ?Image $image = null;


    public function __construct(Imagine $imagine, Box $size, ?string $mode = '')
    {
        $this->imagine = $imagine;
        if ($mode) {
            $this->setMode($mode);
        }

        $this->setSize($size);
    }

    public function setMode(string|int $mode): self
    {
        if (!in_array($mode, self::MODE)) {
            throw new InvalidArgumentException(sprintf('%s est invalid, les liste valides sont %s', $mode, implode(self::MODE)));
        }

        $this->mode = $mode;

        return $this;
    }

    public function getMode(): string|int
    {
        return $this->mode;
    }

    public function setSize(Box $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): Box
    {
        return $this->size;
    }

    public function open(string $pathname): self
    {
        $this->image = $this->imagine->open($pathname);

        return $this;
    }
    /**
     * @throws InvalidArgumentException
     */
    public function thumbnail(): self
    {
        if (!$this->image) {
            throw new InvalidArgument('La taille de l\'image est indefinie! veuillez le redefinir dans le constructeur ou la methode set size!');
        }

        $this->image->thumbnail($this->size->widen($this->size->getWidth() - 10), $this->mode);

        return $this;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function save(string $path)
    {
        if (!$this->image) {
            throw new InvalidArgument('La taille de l\'image est indefinie! veuillez le redefinir dans le constructeur ou la methode set size!');
        }
        $this->image->save($path, ['quality' => 85, 'format' => 'png']);
    }

    // public function resize(int $width, int $height): self
    // {
    //     $this->image->resize($this->size->widen($this->size->getWidth() - 10), $this->mode);

    //     return $this;
    // }
}
