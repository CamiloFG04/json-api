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
            'slug' => $article->slug,
            'content' => 'Contenido del articulo editado',
        ])->assertOk();


        $response->assertHeader('Location', route('api.v1.articles.show', $article));

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Actualizar articulo',
                    'slug' => $article->slug,
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
    public function slug_must_be_unique(): void
    {
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article1), [
            'title' => 'Nuevo Articulo',
            'slug' => $article2->slug,
            'content' => 'Contenido del articulo',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_and_dashes(): void
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update',$article), [
            'title' => 'Nuevo Articulo',
            'slug' => '$%^',
            'content' => 'Contenido del articulo',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_contain_underscores(): void
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update',$article), [
            'title' => 'Nuevo Articulo',
            'slug' => 'nuevo_articulo',
            'content' => 'Contenido del articulo',
        ])
            ->assertSee(__('validation.no_underscores', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_start_with_dashes(): void
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update',$article), [
            'title' => 'Nuevo Articulo',
            'slug' => '-nuevo-articulo',
            'content' => 'Contenido del articulo',
        ])
            ->assertSee(__('validation.no_starting_dashes', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_end_with_dashes(): void
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update',$article), [
            'title' => 'Nuevo Articulo',
            'slug' => 'nuevo-articulo-',
            'content' => 'Contenido del articulo',
        ])
            ->assertSee(__('validation.no_ending_dashes', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
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
