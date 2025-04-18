<?php

namespace Tests\Feature\AiModels;

use App\Models\AiModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SvgControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_svg_page_loads(): void
    {
        $model = AiModel::factory()->create();

        $response = $this->get(route('models.show.svg', $model));

        $response->assertOk();
        $response->assertViewIs('models.show-svg');
        $response->assertViewHas('model', $model);
        $response->assertViewHas('activeTab', 'svg');
    }
}
