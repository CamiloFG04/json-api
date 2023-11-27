<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ValidateJsonApiDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutJsonApiDocumentFormatting();

        Route::any('test_route', function () {
            return 'ok';
        })->middleware(ValidateJsonApiDocument::class);
    }

    /** @test */
    public function only_accepts_valid_json_api_document(): void
    {
        $this->postJson('/test_route', [
            'data' => [
                'type' => 'string',
                'attributes' => ['name' => 'string'],
            ]
        ])->assertSuccessful();

        $this->patchJson('/test_route', [
            'data' => [
                'id' => '1',
                'type' => 'string',
                'attributes' => ['name' => 'string'],
            ]
        ])->assertSuccessful();
    }

    /** @test */
    public function data_is_required(): void
    {
        $this->postJson('/test_route', [])->dump()->assertJsonApiValidationErrors('data');
        $this->patchJson('/test_route', [])->dump()->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_must_be_an_array(): void
    {
        $this->postJson('/test_route', [
            'data' => 'not an array',
        ])->dump()->assertJsonApiValidationErrors('data');
        $this->patchJson('/test_route', [
            'data' => 'not an array',
        ])->dump()->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_type_is_required(): void
    {
        $this->postJson('/test_route', [
            'data' => [
                'attributes' => []
            ],
        ])->dump()->assertJsonApiValidationErrors('data.type');
        $this->patchJson('/test_route', [
            'data' => [
                'attributes' => []
            ],
        ])->dump()->assertJsonApiValidationErrors('data.type');
    }

    /** @test */
    public function data_type_must_be_a_string(): void
    {
        $this->postJson('/test_route', [
            'data' => [
                'type' => 1
            ],
        ])->dump()->assertJsonApiValidationErrors('data.type');
        $this->patchJson('/test_route', [
            'data' => [
                'type' => 1
            ],
        ])->dump()->assertJsonApiValidationErrors('data.type');
    }

    /** @test */
    public function data_attributes_is_required(): void
    {
        $this->postJson('/test_route', [
            'data' => [
                'type' => 'string',
                'attributes' => []
            ],
        ])->dump()->assertJsonApiValidationErrors('data.attributes');
        $this->patchJson('/test_route', [
            'data' => [
                'id' => '1',
                'type' => 'string',
                'attributes' => []
            ],
        ])->dump()->assertJsonApiValidationErrors('data.attributes');
    }

    /** @test */
    public function data_attributes_must_be_an_array(): void
    {
        $this->postJson('/test_route', [
            'data' => [
                'type' => 'string',
                'attributes' => 'string'
            ],
        ])->dump()->assertJsonApiValidationErrors('data.attributes');
        $this->patchJson('/test_route', [
            'data' => [
                'type' => 'string',
                'attributes' => 'string'
            ],
        ])->dump()->assertJsonApiValidationErrors('data.attributes');
    }

    /** @test */
    public function data_id_is_required(): void
    {
        $this->patchJson('/test_route', [
            'data' => [
                'type' => 'string',
                'attributes' => ['name' => 'test']
            ],
        ])->dump()->assertJsonApiValidationErrors('data.id');
    }

    /** @test */
    public function  data_id_must_be_a_string(): void
    {
        $this->patchJson('/test_route', [
            'data' => [
                'id' => 1,
                'type' => 'string',
                'attributes' => ['name' => 'test']
            ],
        ])->dump()->assertJsonApiValidationErrors('data.id');
    }
}
