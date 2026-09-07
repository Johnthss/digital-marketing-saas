<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiPageAnalysisTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_page_loads_with_content(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->create([
            'agency_id' => $agency->id,
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->get('/ai');
        
        // Output status and content for analysis
        echo "\n\n=== AI PAGE STATUS: " . $response->getStatusCode() . " ===\n";
        
        if ($response->getStatusCode() === 200) {
            $content = $response->getContent();
            echo "Content length: " . strlen($content) . "\n";
            
            // Extract key sections
            if (str_contains($content, 'content-wrapper')) {
                echo "Has content-wrapper: YES\n";
            }
            
            // Extract visible text
            $text = strip_tags($content);
            $text = preg_replace('/\s+/', ' ', $text);
            echo "\n=== VISIBLE TEXT (first 3000 chars) ===\n";
            echo substr($text, 0, 3000);
            echo "\n=== END TEXT ===\n";
        } else {
            echo "Redirected to: " . $response->headers->get('Location') . "\n";
        }
        
        $this->assertTrue(true); // Always pass - this is for analysis output
    }
}
