<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_update_articles(): void
    {
        $this->withoutExceptionHandling();

        $article =  Article::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Actualizar articulo',
            'slug' => 'actualizar-articulo',
            'content' => 'Contenido del articulo editado',
        ])->assertOk();


        $response->assertHeader('Location', route('api.v1.articles.show', $article));

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Actualizar articulo',
                    'slug' => 'actualizar-articulo',
                    'content' => 'Contenido del articulo editado',
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article)
                ]
            ]
        ]);
    }

    /** @test */
    public function title_is_required(): void
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [
            'slug' => 'articulo-editado',
            'content' => 'Contenido del articulo editado',
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function title_must_be_at_least_4_characters(): void
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Art',
            'slug' => 'articulo-editado',
            'content' => 'Contenido del articulo editado',
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required(): void
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Articulo Editado',
            'content' => 'Contenido del articulo editado',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required(): void
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Articulo Editado',
            'slug' => 'articulo-editado',
        ])->assertJsonApiValidationErrors('content');
    }
}
