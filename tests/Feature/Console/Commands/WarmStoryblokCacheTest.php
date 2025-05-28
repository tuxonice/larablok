<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\WarmStoryblokCache;
use App\Models\Article;
use App\Services\StoryblokService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Mockery;

class WarmStoryblokCacheTest extends TestCase
{
    /**
     * Test the command warms the cache with articles.
     */
    public function test_command_warms_cache_with_articles(): void
    {
        // Create a mock of the StoryblokService
        $mockService = Mockery::mock(StoryblokService::class);
        
        // Create test articles
        $articles = array_map(function ($i) {
            return $this->createTestArticle("article-$i", "Article $i");
        }, range(1, 10));
        
        // Set up the mock to return test data
        $mockService->shouldReceive('getArticles')
            ->with(10, 1)
            ->once()
            ->andReturn([
                'articles' => $articles,
                'total' => 25 // 3 pages with 10 per page
            ]);
        
        // Mock additional page calls
        $mockService->shouldReceive('getArticles')
            ->with(10, 2)
            ->once()
            ->andReturn([
                'articles' => $articles,
                'total' => 25
            ]);
        
        $mockService->shouldReceive('getArticles')
            ->with(10, 3)
            ->once()
            ->andReturn([
                'articles' => $articles,
                'total' => 25
            ]);
        
        // Mock individual article calls
        foreach ($articles as $article) {
            $mockService->shouldReceive('getArticle')
                ->with($article->slug)
                ->once()
                ->andReturn([
                    'article' => $article,
                    'story' => ['slug' => $article->slug]
                ]);
        }
        
        // Mock category calls
        $mockService->shouldReceive('getArticlesByCategory')
            ->with('Test', 10, 1)
            ->once()
            ->andReturn([
                'articles' => $articles,
                'total' => 10
            ]);
        
        $mockService->shouldReceive('getArticlesByCategory')
            ->with('Laravel', 10, 1)
            ->once()
            ->andReturn([
                'articles' => $articles,
                'total' => 10
            ]);
        
        // Replace the real service with the mock in the container
        $this->app->instance(StoryblokService::class, $mockService);
        
        // Run the command
        $this->artisan('storyblok:warm-cache')
            ->expectsOutput('Warming Storyblok cache...')
            ->expectsOutput('Caching article list (page 1)...')
            ->expectsOutput('Found 25 articles across 3 pages.')
            ->expectsOutput('Caching additional article pages...')
            ->expectsOutput('Caching individual articles...')
            ->expectsOutput('Caching articles by category...')
            ->expectsOutput('Found 2 categories.')
            ->expectsOutput('Storyblok cache warming completed successfully!')
            ->assertExitCode(0);
    }
    
    /**
     * Test the command clears cache before warming when --clear option is used.
     */
    public function test_command_clears_cache_before_warming(): void
    {
        // Create a mock of the StoryblokService
        $mockService = Mockery::mock(StoryblokService::class);
        
        // Set up the mock to clear caches
        $mockService->shouldReceive('clearAllCaches')
            ->once();
        
        // Set up the mock to return minimal test data for warming
        $mockService->shouldReceive('getArticles')
            ->with(10, 1)
            ->once()
            ->andReturn([
                'articles' => [],
                'total' => 0
            ]);
        
        // Replace the real service with the mock in the container
        $this->app->instance(StoryblokService::class, $mockService);
        
        // Run the command with --clear option
        $this->artisan('storyblok:warm-cache', ['--clear' => true])
            ->expectsOutput('Clearing existing Storyblok cache...')
            ->expectsOutput('Cache cleared.')
            ->expectsOutput('Warming Storyblok cache...')
            ->expectsOutput('Caching article list (page 1)...')
            ->expectsOutput('Found 0 articles across 0 pages.')
            ->expectsOutput('Storyblok cache warming completed successfully!')
            ->assertExitCode(0);
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
