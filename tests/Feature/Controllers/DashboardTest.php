<?php

namespace Tests\Feature\Controllers;

use Mss\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{

    public function test_guest_is_getting_redirected()
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_gets_dashboard() {
        $response = $this->actingAs(User::first())->get('/');

        $response->assertStatus(200);
        $response->assertSeeText('Dashboard');
    }
}
