<?php

namespace App\DTOs;

class OrderDTO extends BaseDTO
{
    public int $product_id;
    public string $type = 'buy';
    public ?int $user_id;
    public ?string $hours;

    public function __construct(
        array $data = []
    ) {
        parent::__construct($data);
    }
}
