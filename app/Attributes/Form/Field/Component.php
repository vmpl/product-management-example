<?php declare(strict_types=1);

namespace App\Attributes\Form\Field;

enum Component
{
    case Input;

    public function getConfig(): \App\Attributes\PropAttribute
    {
        $config = new class implements \App\Attributes\PropAttribute {
            public function __construct(
                public readonly ?string $type = null,
                public readonly \stdClass $props = new \stdClass(),
            ) {
            }

            public static function input(): self
            {
                $props = new \stdClass();
                $props->outlined = true;
                return new self('q-input', $props);
            }

            public function toProp(): array
            {
                return [
                    'type' => $this->type,
                    'props' => $this->props,
                ];
            }
        };

        return match ($this) {
            static::Input => $config::input()
        };
    }
}
