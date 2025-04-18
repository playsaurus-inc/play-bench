<?php

namespace Tests\Feature\Models;

use App\Models\AiModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiModelControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_index_page_loads(): void
    {
        $response = $this->get(route('models.index'));

        $response->assertOk();
        $response->assertViewIs('models.index');
    }

    public function test_model_detail_page_loads(): void
    {
        $model = AiModel::factory()->create();

        $response = $this->get(route('models.show', $model));

        $response->assertOk();
        $response->assertViewIs('models.show');
        $response->assertViewHas('model');
        $response->assertSee($model->name);
    }
}
