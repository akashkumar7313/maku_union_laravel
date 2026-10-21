<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('मिर्जापुर असंगठित कामगार यूनियन', false);
    }

    public function test_membership_registration_page_loads(): void
    {
        $this->seed();

        $this->get('/membership/register')->assertOk();
    }

    public function test_admin_login_page_loads(): void
    {
        $this->seed();

        $this->get('/admin/login')->assertOk();
    }
}
