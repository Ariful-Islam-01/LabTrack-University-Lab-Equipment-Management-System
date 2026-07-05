<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_booking_create_page(): void
    {
        config(['database.default' => 'oracle']);
        $response = $this->withSession([
            'user_id' => 2207045,
            'full_name' => 'Sk Nazmus Salehin Nirob',
            'role' => 'STUDENT'
        ])->get('/bookings/create/EQ001');

        $response->assertStatus(200);
        echo "Response status is: " . $response->status() . "\n";
    }
}
