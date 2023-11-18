<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiTest extends TestCase
{
    public function test_api_routes(): void
    {
        $productsResponse = $this->get('/api/products');

        $productsResponse->assertStatus(200);

        $product_id = json_decode($productsResponse->content(), true)['data'][0]['id'];

        $productResponse = $this->get('/api/products/'.$product_id);

        $productResponse->assertStatus(200);

        $userCredentials = [
            'name' => 'Test User',
            'email' => 'test@test.ru',
            'password' => 'password'
        ];

        $registerResponse = $this->post('/api/register', $userCredentials);

        $registerResponse->assertStatus(200);

        $loginResponse = $this->post('/api/login', $userCredentials, ['accept' => 'application/json']);

        $loginResponse->assertStatus(200);

        $order = [
            'product_id' => $product_id,
            'type' => 'buy'
        ];

        $orderCreateResponse = $this->post('/api/orders/create', $order, ['accept' => 'application/json']);

        $orderCreateResponse->assertStatus(200);

        $order_id = json_decode($orderCreateResponse->content(), true)['order']['id'];

        $orderListResponse = $this->get('/api/orders');

        $orderListResponse->assertStatus(200);

        $orderShowResponse = $this->get('/api/orders/'.$order_id);

        $orderShowResponse->assertStatus(200);
    }
}
