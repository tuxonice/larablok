<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ArticleList;
use App\Models\Article;
use App\Services\StoryblokService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Mockery;

class ArticleListTest extends TestCase
{
    /**
     * Test the ArticleList component renders correctly with articles.
     */
    public function test_component_renders_with_articles(): void
    {
        // Create a mock of the StoryblokService
        $mockService = Mockery::mock(StoryblokService::class);
        
        // Create test articles
        $article1 = $this->createTestArticle('article-1', 'Article 1');
        $article2 = $this->createTestArticle('article-2', 'Article 2');
        
        // Set up the mock to return test data
        $mockService->shouldReceive('getArticles')
            ->andReturn([
                'articles' => [$article1, $article2],
                'total' => 2
            ]);
        
        // Replace the real service with the mock in the container
        $this->app->instance(StoryblokService::class, $mockService);
        
        // Test the component
        Livewire::test(ArticleList::class)
            ->assertViewHas('articles', [$article1, $article2])
            ->assertViewHas('total', 2)
            ->assertSee('Article 1')
            ->assertSee('Article 2');
    }
    
    /**
     * Test the ArticleList component filters by category.
     */
    public function test_component_filters_by_category(): void
    {
        // Create a mock of the StoryblokService
        $mockService = Mockery::mock(StoryblokService::class);
        
        // Create test articles
        $article = $this->createTestArticle('article-1', 'Article 1');
        $article->categories = ['Technology'];
        
        // Set up the mock to return test data for category filter
        $mockService->shouldReceive('getArticlesByCategory')
            ->with('Technology', 10, 1)
            ->andReturn([
                'articles' => [$article],
                'total' => 1
            ]);
        
        // Replace the real service with the mock in the container
        $this->app->instance(StoryblokService::class, $mockService);
        
        // Test the component with category filter
        Livewire::test(ArticleList::class, ['category' => 'Technology'])
            ->assertSet('category', 'Technology')
            ->assertViewHas('articles', [$article])
            ->assertViewHas('total', 1)
            ->assertSee('Article 1')
            ->assertSee('Technology');
    }
    
    /**
     * Test the ArticleList component searches articles.
     */
    public function test_component_searches_articles(): void
    {
        // Create a mock of the StoryblokService
        $mockService = Mockery::mock(StoryblokService::class);
        
        // Create test articles
        $article = $this->createTestArticle('search-result', 'Search Result');
        
        // Set up the mock to return test data for search
        $mockService->shouldReceive('searchArticles')
            ->with('test query', 10, 1)
            ->andReturn([
                'articles' => [$article],
                'total' => 1
            ]);
        
        // Replace the real service with the mock in the container
        $this->app->instance(StoryblokService::class, $mockService);
        
        // Test the component with search query
        Livewire::test(ArticleList::class)
            ->set('searchQuery', 'test query')
            ->assertSet('searchQuery', 'test query')
            ->assertViewHas('articles', [$article])
            ->assertViewHas('total', 1)
            ->assertSee('Search Result');
    }
    
    /**
     * Test the pagination view method generates correct HTML.
     */
    public function test_pagination_view_method(): void
    {
        // Create a mock of the StoryblokService
        $mockService = Mockery::mock(StoryblokService::class);
        
        // Set up the mock to return test data with multiple pages
        $mockService->shouldReceive('getArticles')
            ->andReturn([
                'articles' => array_map(function ($i) {
                    return $this->createTestArticle("article-$i", "Article $i");
                }, range(1, 10)),
                'total' => 25 // Total of 3 pages with 10 per page
            ]);
        
        // Replace the real service with the mock in the container
        $this->app->instance(StoryblokService::class, $mockService);
        
        // Test the component's pagination
        $component = Livewire::test(ArticleList::class);
        
        // Get the pagination HTML
        $paginationHtml = $component->instance()->paginationView();
        
        // Assert pagination contains expected elements
        $this->assertStringContainsString('page-1', $paginationHtml);
        $this->assertStringContainsString('page-2', $paginationHtml);
        $this->assertStringContainsString('page-3', $paginationHtml);
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
