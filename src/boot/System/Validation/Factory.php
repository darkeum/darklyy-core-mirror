<?php

namespace Boot\System\Validation;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Validation\Factory as LaravelFactory;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Translation\Translator;
use Boot\Contracts\Validation\Factory as FactoryContract;

class Factory extends LaravelFactory implements FactoryContract
{
    /**
     * Resolve a new Validator instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $attributes
     * @return \Illuminate\Validation\Validator
     */
    protected function resolve(array $data, array $rules, array $messages, array $attributes)
    {
        if (is_null($this->resolver)) {
            return new Validator($this->translator, $data, $rules, $messages, $attributes);
        }

        return call_user_func($this->resolver, $this->translator, $data, $rules, $messages, $attributes);
    }
}
