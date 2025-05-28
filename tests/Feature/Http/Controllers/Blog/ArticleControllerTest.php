<?php

namespace Tests\Feature\Http\Controllers\Blog;

use App\Models\Article;
use App\Services\StoryblokService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use Tests\TestCase;
use Mockery;

class ArticleControllerTest extends TestCase
{
    /**
     * Test the home page loads correctly and includes the ArticleList component.
     */
    public function test_home_page_loads_with_article_list(): void
    {
        // Create a mock of the StoryblokService
        $mockService = Mockery::mock(StoryblokService::class);
        
        // Set up the mock to return test data
        $mockService->shouldReceive('getArticles')
            ->andReturn([
                'articles' => [
                    $this->createTestArticle('article-1', 'Article 1'),
                    $this->createTestArticle('article-2', 'Article 2'),
                ],
                'total' => 2
            ]);
        
        // Replace the real service with the mock in the container
        $this->app->instance(StoryblokService::class, $mockService);
        
        // Visit the home page
        $response = $this->get(route('home'));
        
        // Assert the response is successful
        $response->assertStatus(200);
        
        // Assert the view is correct
        $response->assertViewIs('home');
        
        // Assert Livewire component is rendered
        $response->assertSeeLivewire('article-list');
    }
    
    /**
     * Test the article detail page loads correctly and includes the ArticleDetail component.
     */
    public function test_article_detail_page_loads_with_article_detail(): void
    {
        // Create a mock of the StoryblokService
        $mockService = Mockery::mock(StoryblokService::class);
        
        // Set up the mock to return test data
        $mockService->shouldReceive('getArticle')
            ->with('test-article')
            ->andReturn([
                'article' => $this->createTestArticle('test-article', 'Test Article'),
                'story' => ['slug' => 'test-article']
            ]);
        
        // Replace the real service with the mock in the container
        $this->app->instance(StoryblokService::class, $mockService);
        
        // Visit the article detail page
        $response = $this->get(route('article.show', ['slug' => 'test-article']));
        
        // Assert the response is successful
        $response->assertStatus(200);
        
        // Assert the view is correct
        $response->assertViewIs('article');
        
        // Assert the view has the correct data
        $response->assertViewHas('slug', 'test-article');
        
        // Assert Livewire component is rendered
        $response->assertSeeLivewire('article-detail');
    }
    
    /**
     * Test the webhook endpoint processes Storyblok webhooks correctly.
     */
    public function test_storyblok_webhook_endpoint(): void
    {
        // Create a mock of the StoryblokService
        $mockService = Mockery::mock(StoryblokService::class);
        
        // Set expectations for the mock
        $mockService->shouldReceive('clearArticleCache')
            ->once()
            ->with('test-article');
        
        // Replace the real service with the mock in the container
        $this->app->instance(StoryblokService::class, $mockService);
        
        // Send a webhook request
        $response = $this->postJson(route('webhooks.storyblok'), [
            'action' => 'published',
            'story_id' => '123',
            'story.slug' => 'test-article'
        ]);
        
        // Assert the response is successful
        $response->assertStatus(200);
        
        // Assert the response has the correct structure
        $response->assertJson([
            'status' => 'success',
            'message' => 'Webhook processed successfully'
        ]);
    }
    
    /**
     * Helper method to create a test Article instance.
     */
    private function createTestArticle(string $slug, string $title): Article
    {
        $article = new Article();
        $article->uuid = 'test-uuid-' . $slug;
        $article->slug = $slug;
        $article->title = $title;
        $article->teaser = 'Test teaser for ' . $title;
        $article->content = '<p>Test content for ' . $title . '</p>';
        $article->categories = ['Test', 'Laravel'];
        $article->author = 'Test Author';
        $article->published_at = now();
        
        return $article;
    }
    
    /**
     * Clean up after each test.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
