<?php

namespace Tests\Feature;

use App\Models\SvgMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SvgMatchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_svg_index_page_loads(): void
    {
        $response = $this->get(route('svg.index'));

        $response->assertOk();
        $response->assertViewIs('svg.index');
    }

    public function test_svg_matches_index_page_loads(): void
    {
        SvgMatch::factory()->count(5)->create();

        $response = $this->get(route('svg.matches.index'));

        $response->assertOk();
        $response->assertViewIs('svg.matches.index');
    }

    public function test_svg_match_detail_page_loads(): void
    {
        $match = SvgMatch::factory()->create();

        $response = $this->get(route('svg.matches.show', $match));

        $response->assertOk();
        $response->assertViewIs('svg.matches.show');
        $response->assertViewHas('svgMatch', $match);
    }
}
