<?php

namespace Tests\Feature;

use App\Models\RpsMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RpsMatchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_rps_index_page_loads(): void
    {
        $response = $this->get(route('rps.index'));

        $response->assertOk();
        $response->assertViewIs('rps.index');
    }

    public function test_rps_matches_index_page_loads(): void
    {
        RpsMatch::factory()->count(5)->create();

        $response = $this->get(route('rps.matches.index'));

        $response->assertOk();
        $response->assertViewIs('rps.matches.index');
    }

    public function test_rps_match_detail_page_loads(): void
    {
        $match = RpsMatch::factory()->create();

        $response = $this->get(route('rps.matches.show', $match));

        $response->assertOk();
        $response->assertViewIs('rps.matches.show');
        $response->assertViewHas('rpsMatch', $match);
    }
}
