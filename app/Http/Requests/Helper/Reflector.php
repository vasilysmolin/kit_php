<?php

namespace App\Http\Requests\Helper;

use Illuminate\Support\Collection;
use ReflectionClass;

class Reflector
{
    private string $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function reflector(): Collection
    {
        $reflector = new ReflectionClass($this->class);
        $relations = collect();
        foreach ($reflector->getMethods() as $reflectionMethod) {
            $returnType = $reflectionMethod->getReturnType();
            if ($returnType) {
                if (
                    in_array(class_basename($returnType->getName()), [
                    'HasOne',
                    'HasMany',
                    'BelongsTo',
                    'BelongsToMany',
                    'MorphToMany',
                    'MorphTo',
                    ])
                ) {
                    $relations->add($reflectionMethod->name);
                }
            }
        }

        return $relations;
    }
}
