<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $users = User::all();

        return $users;
    }

    /* public function testBasicTest()
    {
        $response = $this -> get('/');

        $response -> assertStatus(200);
    } */
}
