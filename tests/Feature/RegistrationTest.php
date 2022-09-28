<?php

namespace Tests\Feature;


use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function testRendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }
}
