<?php

declare(strict_types=1);

namespace MaliBoot\Validation;

use Hyperf\Utils\Arr;
use Hyperf\Utils\Str;
use Hyperf\Validation\Contract\PresenceVerifierInterface;
use Hyperf\Validation\Contract\Rule;
use Hyperf\Validation\ValidationException;
use Hyperf\Validation\ValidatorFactory;
use MaliBoot\Dto\AbstractCommand;
use MaliBoot\Dto\Annotation\Field;
use MaliBoot\Swagger\ApiAnnotation;
use MaliBoot\Validation\Annotation\Validation;
use Psr\Container\ContainerInterface;

class Validator
{
    public ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->factory = $this->container->get(ValidatorFactory::class);
    }

    public function validated(AbstractCommand $cmdInstance): bool
    {
        $annotations = ApiAnnotation::propertyMetadata(get_class($cmdInstance));

        if (empty($annotations)) {
            return true;
        }

        $rules = [];
        $attributes = [];
        $messages = [];
        foreach ($annotations as $key => $item) {
            foreach ($item as $annotation) {
                if ($annotation instanceof Field && ! empty($annotation->name)) {
                    $attributes[$key] = $annotation->name;
                    continue;
                }

                if (! $annotation instanceof Validation) {
                    continue;
                }

                if (! empty($annotation->rule)) {
                    $rules[$key] = $annotation->rule;
                }

                if (! empty($annotation->message)) {
                    $messages[$key] = $annotation->message;
                }
            }
        }

        $result = $this->check($cmdInstance->toArray(), $rules, $messages, $attributes, $cmdInstance);
        return true;
    }

    public function check(array $data, array $rules, array $messages, array $attributes, $cmdInstance = null)
    {
        if (empty($rules)) {
            return true;
        }

        $realRules = [];
        $whiteData = [];

        foreach ($rules as $key => $rule) {
            $rulesMap = explode('|', $rule);
            foreach ($rules as $index => &$item) {
                if (is_string($item) && Str::startsWith($item, 'cb_')) {
                    $item = $this->makeObjectCallback(Str::replaceFirst('cb_', '', $item), $cmdInstance);
                }
                unset($item);
            }
            $realRules[$key] = $rulesMap;
        }

        $validator = $this->factory->make($data, $realRules, $messages, $attributes);
        $verifier = $this->container->get(PresenceVerifierInterface::class);
        $validator->setPresenceVerifier($verifier);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $filterData = array_merge($this->parseData($validator->validated()), $whiteData);

        $realData = [];
        foreach ($filterData as $key => $val) {
            Arr::set($realData, $key, $val);
        }

        $realData = $this->arrayMapRecursive(function ($item) {
            return is_string($item) ? trim($item) : $item;
        }, $realData);

        return $realData;
    }

    public function makeObjectCallback($method, $object)
    {
        return new class($method, $this, $object) implements Rule {
            public $customRule;

            public $validation;

            public $object;

            public $error = '%s ';

            public $attribute;

            public function __construct($customRule, $validation, $object)
            {
                $this->customRule = $customRule;
                $this->validation = $validation;
                $this->object = $object;
            }

            public function passes($attribute, $value): bool
            {
                $this->attribute = $attribute;
                $rule = $this->customRule;
                if (strpos($rule, ':') !== false) {
                    $rule = explode(':', $rule)[0];
                    $extra = explode(',', explode(':', $rule)[1]);
                    $ret = $this->object->{$rule}($attribute, $value, $extra);
                    if (is_string($ret)) {
                        $this->error .= $ret;

                        return false;
                    }

                    return true;
                }
                $ret = $this->object->{$rule}($attribute, $value);
                if (is_string($ret)) {
                    $this->error .= $ret;

                    return false;
                }

                return true;
            }

            public function message(): string
            {
                return sprintf($this->error, $this->attribute);
            }
        };
    }

    /**
     * Parse the data array, converting -> to dots.
     */
    public function parseData(array $data): array
    {
        $newData = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->parseData($value);
            }

            if (Str::contains((string) $key, '->')) {
                $newData[str_replace('->', '.', $key)] = $value;
            } else {
                $newData[$key] = $value;
            }
        }

        return $newData;
    }

    private function arrayMapRecursive(callable $func, array $data)
    {
        $result = [];
        foreach ($data as $key => $val) {
            $result[$key] = is_array($val) ? $this->arrayMapRecursive($func, $val) : call($func, [$val]);
        }

        return $result;
    }
}
