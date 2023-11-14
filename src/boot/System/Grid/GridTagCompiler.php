<?php

namespace Boot\System\Grid;

use Illuminate\View\Compilers\ComponentTagCompiler;

class GridTagCompiler extends ComponentTagCompiler
{
    public function compile($value)
    {
        return $this->compileGridSelfClosingTags($value);
    }

    protected function compileGridSelfClosingTags($value)
    {
        $pattern = "/
            <
                \s*
                grid\:([\w\-\:\.]*)
                \s*
                (?<attributes>
                    (?:
                        \s+
                        [\w\-:.@]+
                        (
                            =
                            (?:
                                \\\"[^\\\"]*\\\"
                                |
                                \'[^\']*\'
                                |
                                [^\'\\\"=<>]+
                            )
                        )?
                    )*
                    \s*
                )
            \/?>
        /x";

        return preg_replace_callback($pattern, function (array $matches) {
            $attributes = $this->getAttributesFromAttributeString($matches['attributes']);

            // Convert all kebab-cased to camelCase.
            $attributes = collect($attributes)->mapWithKeys(function ($value, $key) {
                // Skip snake_cased attributes.
                if (str($key)->contains('_')) return [$key => $value];

                return [(string) str($key)->camel() => $value];
            })->toArray();

            // Convert all snake_cased attributes to camelCase, and merge with
            // existing attributes so both snake and camel are available.
            $attributes = collect($attributes)->mapWithKeys(function ($value, $key) {
                // Skip snake_cased attributes
                if (! str($key)->contains('_')) return [$key => false];

                return [(string) str($key)->camel() => $value];
            })->filter()->merge($attributes)->toArray();

            $component = $matches[1];
            $component = "'{$component}'";
            return $this->componentString($component, $attributes);
        }, $value);
    }

    protected function componentString(string $component, array $attributes)
    {
        if (isset($attributes['key']) || isset($attributes['grid:key'])) {
            $key = $attributes['key'] ?? $attributes['grid:key'];
            unset($attributes['key']);
            unset($attributes['grid:key']);

            return "@grid({$component}, [".$this->attributesToString($attributes, $escapeBound = false)."], key({$key}))";
        }

        return "@grid({$component}, [".$this->attributesToString($attributes, $escapeBound = false).'])';
    }

    protected function attributesToString(array $attributes, $escapeBound = true)
    {
        return collect($attributes)
                ->map(function (string $value, string $attribute) use ($escapeBound) {
                    return $escapeBound && isset($this->boundAttributes[$attribute]) && $value !== 'true' && ! is_numeric($value)
                                ? "'{$attribute}' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute({$value})"
                                : "'{$attribute}' => {$value}";
                })
                ->implode(',');
    }
}
