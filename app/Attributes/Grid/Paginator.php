<?php declare(strict_types=1);

namespace App\Attributes\Grid;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Paginator
{
    public function __construct(
        protected readonly ?string $path = null,
        protected readonly int $size = 20,
    ) {
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }
}
