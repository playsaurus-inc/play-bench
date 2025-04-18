<?php

namespace Tests\Feature\AiModels;

use App\Models\AiModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RpsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_rps_page_loads(): void
    {
        $model = AiModel::factory()->create();

        $response = $this->get(route('models.show.rps', $model));

        $response->assertOk();
        $response->assertViewIs('models.show-rps');
        $response->assertViewHas('model', $model);
        $response->assertViewHas('activeTab', 'rps');
    }
}
