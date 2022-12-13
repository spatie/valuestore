<?php

namespace Spatie\Valuestore;

use ArrayAccess;
use Countable;

class Valuestore implements ArrayAccess, Countable
{
    protected string $fileName;

    public static function make(string $fileName, ?array $values = null): static
    {
        $valuestore = (new static())->setFileName($fileName);

        if (! is_null($values)) {
            $valuestore->put($values);
        }

        return $valuestore;
    }

    protected function __construct()
    {
    }

    protected function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function put(
        array|string $name,
        mixed $value = null
    ): static {
        if ($name === []) {
            return $this;
        }

        $newValues = $name;

        if (! is_array($name)) {
            $newValues = [$name => $value];
        }

        $newContent = array_merge($this->all(), $newValues);

        $this->setContent($newContent);

        return $this;
    }

    public function push(string $name, mixed $pushValue): static
    {
        if (! is_array($pushValue)) {
            $pushValue = [$pushValue];
        }

        if (! $this->has($name)) {
            $this->put($name, $pushValue);

            return $this;
        }

        $oldValue = $this->get($name);

        if (! is_array($oldValue)) {
            $oldValue = [$oldValue];
        }

        $newValue = array_merge($oldValue, $pushValue);

        $this->put($name, $newValue);

        return $this;
    }

    public function prepend(string $name, mixed $prependValue): static
    {
        if (! is_array($prependValue)) {
            $prependValue = [$prependValue];
        }

        if (! $this->has($name)) {
            $this->put($name, $prependValue);

            return $this;
        }

        $oldValue = $this->get($name);

        if (! is_array($oldValue)) {
            $oldValue = [$oldValue];
        }

        $newValue = array_merge($prependValue, $oldValue);

        $this->put($name, $newValue);

        return $this;
    }

    public function get(string $name, mixed $default = null): mixed
    {
        $all = $this->all();

        if (! array_key_exists($name, $all)) {
            return $default;
        }

        return $all[$name];
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->all());
    }

    public function all(): array
    {
        if (! file_exists($this->fileName)) {
            return [];
        }

        return json_decode(file_get_contents($this->fileName), true) ?? [];
    }

    public function allStartingWith(string $startingWith = ''): array
    {
        $values = $this->all();

        if ($startingWith === '') {
            return $values;
        }

        return $this->filterKeysStartingWith($values, $startingWith);
    }

    public function forget(string $key): static
    {
        $newContent = $this->all();

        unset($newContent[$key]);

        $this->setContent($newContent);

        return $this;
    }

    public function flush(): static
    {
        return $this->setContent([]);
    }

    public function flushStartingWith(string $startingWith = ''): static
    {
        $newContent = [];

        if ($startingWith !== '') {
            $newContent = $this->filterKeysNotStartingWith($this->all(), $startingWith);
        }

        return $this->setContent($newContent);
    }

    public function pull(string $name): mixed
    {
        $value = $this->get($name);

        $this->forget($name);

        return $value;
    }

    public function increment(string $name, int $by = 1): mixed
    {
        $currentValue = $this->get($name) ?? 0;

        if (! $this->isNumber($currentValue)) {
            return $currentValue;
        }

        $newValue = $currentValue + $by;

        $this->put($name, $newValue);

        return $newValue;
    }

    public function decrement(string $name, int $by = 1): array|string|int|float|null
    {
        return $this->increment($name, $by * -1);
    }

    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->put($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->forget($offset);
    }

    public function count(): int
    {
        return count($this->all());
    }

    protected function filterKeysStartingWith(array $values, string $startsWith): array
    {
        return array_filter($values, function ($key) use ($startsWith) {
            return str_starts_with($key, $startsWith);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function filterKeysNotStartingWith(array $values, string $startsWith): array
    {
        return array_filter($values, function ($key) use ($startsWith) {
            return ! str_starts_with($key, $startsWith);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function setContent(array $values): static
    {
        file_put_contents($this->fileName, json_encode($values));

        if (! count($values)) {
            unlink($this->fileName);
        }

        return $this;
    }

    protected function isNumber($value): bool
    {
        return is_int($value) || is_float($value);
    }
}
