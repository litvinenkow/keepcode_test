<?php

namespace App\DTOs;

class BaseDTO {
    public function __construct(?array $values = [])
    {
        if (is_null($values)) {
            return;
        }
        foreach ($values as $key => $value) {
            if (property_exists(static::class, $key) && $value !== null) {
                $this->$key = $value;
            }
        }
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

