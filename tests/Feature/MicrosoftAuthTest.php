<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MicrosoftAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function testMicrosoftPageRedirect()
    {
        $response = $this->get('/login/microsoft');

        $response->assertStatus(302); // Assert that it's a redirect
        
        $redirectUrl = $response->headers->get('Location');
        
        // Debug information
        dump('Redirect URL: ' . $redirectUrl);
        
        // Check that the URL starts with the expected base
        $this->assertStringStartsWith('https://login.microsoftonline.com/', $redirectUrl);
        
        // Check for necessary parameters
        $this->assertStringContainsString('client_id=', $redirectUrl);
        $this->assertStringContainsString('redirect_uri=', $redirectUrl);
        $this->assertStringContainsString('scope=', $redirectUrl);
        $this->assertStringContainsString('response_type=code', $redirectUrl);
        $this->assertStringContainsString('state=', $redirectUrl);
        $this->assertStringContainsString('prompt=select_account', $redirectUrl);

        // Check specific values (replace with your actual values)
        $this->assertStringContainsString('client_id=f0d950f5-84e7-4560-8d1e-4705cd8240f9', $redirectUrl);
        $this->assertStringContainsString('redirect_uri=http%3A%2F%2Flocalhost%3A8000%2Fauth%2Fcallback', $redirectUrl);
        $this->assertStringContainsString('scope=User.Read', $redirectUrl);
    }
}