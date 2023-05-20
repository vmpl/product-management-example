<?php declare(strict_types=1);

namespace App\Attributes\Form\Field;

use App\Providers\CrudAttributesService;

enum Component
{
    case Input;
    case Children;

    public function getConfig(\App\Attributes\Form\Field $field): \App\Attributes\PropAttribute
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

            public static function children(string $name): self
            {
                $props = new \stdClass();
                $props->urlFetch = $name;
                return new self('select-children', $props);
            }

            public function toProp(CrudAttributesService $attributesService): array
            {
                $className = $attributesService->getClassName();
                $urlFetch = $this->props->urlFetch;
                $results = $className::$urlFetch();

                $gridProps = $attributesService->getGridProps();
                $this->props->size = $gridProps['size'];
                $this->props->columns = $gridProps['columns'];

                return [
                    'type' => $this->type,
                    'props' => $this->props,
                ];
            }
        };

        return match ($this) {
            self::Input => $config::input(),
            self::Children => (function () use ($config, $field) {
                return call_user_func([$config, 'children'], $field->getName());
            })(),
        };
    }
}
