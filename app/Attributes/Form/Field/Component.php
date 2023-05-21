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

            public static function children(\ReflectionMethod $method): self
            {
                /** @var \Illuminate\Database\Eloquent\Relations\Relation $invoke */
                $invoke = $method->invoke(!$method->isStatic()
                    ? $method->getDeclaringClass()->newInstanceWithoutConstructor()
                    : null);

                $props = new \stdClass();
                $props->urlFetch = $invoke->getModel()::class;
                return new self('select-children', $props);
            }

            public function toProp(CrudAttributesService $attributesService): array
            {
                switch ($this->type) {
                    case 'select-children':
                        $modelPath = $attributesService->getInfoByNamespace($this->props->urlFetch)->path;
                        $gridProps = $attributesService->getGridProps($modelPath);

                        $this->props->size = $gridProps['size'];
                        $this->props->columns = $gridProps['columns'];
                        $this->props->urlFetch = route('crud.grid.fetch', ['grid' => $modelPath]);
                        break;
                    default:
                        break;
                }

                return [
                    'type' => $this->type,
                    'props' => $this->props,
                ];
            }
        };

        return match ($this) {
            self::Input => $config::input(),
            self::Children => (function () use ($config, $field) {
                return call_user_func([$config, 'children'], $field->getReflection());
            })(),
        };
    }
}
