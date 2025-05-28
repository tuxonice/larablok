<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ArticleDetail;
use App\Models\Article;
use App\Services\StoryblokService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Mockery;

class ArticleDetailTest extends TestCase
{
    /**
     * Test the ArticleDetail component renders correctly with an article.
     */
    public function test_component_renders_with_article(): void
    {
        // Create a mock of the StoryblokService
        $mockService = Mockery::mock(StoryblokService::class);
        
        // Create a test article
        $article = $this->createTestArticle('test-article', 'Test Article');
        
        // Set up the mock to return test data
        $mockService->shouldReceive('getArticle')
            ->with('test-article')
            ->andReturn([
                'article' => $article,
                'story' => ['slug' => 'test-article']
            ]);
        
        // Replace the real service with the mock in the container
        $this->app->instance(StoryblokService::class, $mockService);
        
        // Test the component
        Livewire::test(ArticleDetail::class, ['slug' => 'test-article'])
            ->assertSet('slug', 'test-article')
            ->assertSet('article', $article)
            ->assertSet('error', null)
            ->assertSee('Test Article')
            ->assertSee('Test content for Test Article')
            ->assertSee('Test Author');
    }
    
    /**
     * Test the ArticleDetail component handles article not found.
     */
    public function test_component_handles_article_not_found(): void
    {
        // Create a mock of the StoryblokService
        $mockService = Mockery::mock(StoryblokService::class);
        
        // Set up the mock to return error data
        $mockService->shouldReceive('getArticle')
            ->with('non-existent-article')
            ->andReturn([
                'article' => null,
                'error' => 'Article not found'
            ]);
        
        // Replace the real service with the mock in the container
        $this->app->instance(StoryblokService::class, $mockService);
        
        // Test the component with non-existent article
        Livewire::test(ArticleDetail::class, ['slug' => 'non-existent-article'])
            ->assertSet('slug', 'non-existent-article')
            ->assertSet('article', null)
            ->assertSet('error', 'Article not found')
            ->assertSee('Article not found')
            ->assertSee('Back to all articles');
    }
    
    /**
     * Test the ArticleDetail component displays categories correctly.
     */
    public function test_component_displays_categories(): void
    {
        // Create a mock of the StoryblokService
        $mockService = Mockery::mock(StoryblokService::class);
        
        // Create a test article with categories
        $article = $this->createTestArticle('test-article', 'Test Article');
        $article->categories = ['Technology', 'Laravel', 'Storyblok'];
        
        // Set up the mock to return test data
        $mockService->shouldReceive('getArticle')
            ->with('test-article')
            ->andReturn([
                'article' => $article,
                'story' => ['slug' => 'test-article']
            ]);
        
        // Replace the real service with the mock in the container
        $this->app->instance(StoryblokService::class, $mockService);
        
        // Test the component
        Livewire::test(ArticleDetail::class, ['slug' => 'test-article'])
            ->assertSee('Technology')
            ->assertSee('Laravel')
            ->assertSee('Storyblok');
    }
    
    /**
     * Test the ArticleDetail component loads related articles.
     */
    public function test_component_loads_related_articles(): void
    {
        // Create a mock of the StoryblokService
        $mockService = Mockery::mock(StoryblokService::class);
        
        // Create a test article with categories
        $article = $this->createTestArticle('test-article', 'Test Article');
        $article->categories = ['Technology'];
        
        // Set up the mock to return test data
        $mockService->shouldReceive('getArticle')
            ->with('test-article')
            ->andReturn([
                'article' => $article,
                'story' => ['slug' => 'test-article']
            ]);
        
        // Replace the real service with the mock in the container
        $this->app->instance(StoryblokService::class, $mockService);
        
        // Test the component
        $component = Livewire::test(ArticleDetail::class, ['slug' => 'test-article'])
            ->assertSeeLivewire('related-articles');
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
