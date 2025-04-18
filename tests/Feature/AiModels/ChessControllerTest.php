<?php

namespace Tests\Feature\AiModels;

use App\Models\AiModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChessControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_chess_page_loads(): void
    {
        $model = AiModel::factory()->create();

        $response = $this->get(route('models.show.chess', $model));

        $response->assertOk();
        $response->assertViewIs('models.show-chess');
        $response->assertViewHas('model', $model);
        $response->assertViewHas('activeTab', 'chess');
    }
}
