<?php

namespace Tests\Unit\Services;

use App\Models\Article;
use App\Services\StoryblokService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StoryblokServiceTest extends TestCase
{
    protected StoryblokService $storyblokService;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock the config values
        Config::set('storyblok.api_key', 'test-api-key');
        Config::set('storyblok.cache_duration', 60);
        Config::set('storyblok.version', 'published');
        
        $this->storyblokService = new StoryblokService();
    }
    
    /**
     * Test that getArticles method fetches and caches articles correctly.
     */
    public function test_get_articles(): void
    {
        // Mock the HTTP response
        Http::fake([
            'api.storyblok.com/v2/cdn/stories*' => Http::response([
                'stories' => [
                    [
                        'uuid' => '123',
                        'slug' => 'test-article',
                        'name' => 'Test Article',
                        'content' => [
                            'title' => 'Test Article Title',
                            'teaser' => 'Test teaser',
                        ],
                    ],
                ],
                'total' => 1,
            ], 200),
        ]);
        
        // Clear the cache to ensure a fresh test
        Cache::flush();
        
        // Call the method
        $result = $this->storyblokService->getArticles(10, 1);
        
        // Assert the HTTP request was made correctly
        Http::assertSent(function ($request) {
            return $request->url() == 'https://api.storyblok.com/v2/cdn/stories' &&
                   $request['token'] == 'test-api-key' &&
                   $request['per_page'] == 10 &&
                   $request['page'] == 1 &&
                   $request['content_type'] == 'article' &&
                   $request['version'] == 'published';
        });
        
        // Assert the result structure
        $this->assertArrayHasKey('stories', $result);
        $this->assertArrayHasKey('articles', $result);
        $this->assertArrayHasKey('total', $result);
        
        // Assert the articles were mapped correctly
        $this->assertCount(1, $result['articles']);
        $this->assertInstanceOf(Article::class, $result['articles'][0]);
        $this->assertEquals('test-article', $result['articles'][0]->slug);
        $this->assertEquals('Test Article Title', $result['articles'][0]->title);
        
        // Assert the result was cached
        $cacheKey = 'storyblok_articles_10_1';
        $this->assertTrue(Cache::has($cacheKey));
        
        // Make a second call and assert no HTTP request is made (uses cache)
        Http::fake([
            'api.storyblok.com/v2/cdn/stories*' => Http::response([], 500), // This should not be called
        ]);
        
        $cachedResult = $this->storyblokService->getArticles(10, 1);
        $this->assertEquals($result, $cachedResult);
    }
    
    /**
     * Test that getArticle method fetches and caches a single article correctly.
     */
    public function test_get_article(): void
    {
        // Mock the HTTP response
        Http::fake([
            'api.storyblok.com/v2/cdn/stories/test-article' => Http::response([
                'story' => [
                    'uuid' => '123',
                    'slug' => 'test-article',
                    'name' => 'Test Article',
                    'content' => [
                        'title' => 'Test Article Title',
                        'teaser' => 'Test teaser',
                    ],
                ],
            ], 200),
        ]);
        
        // Clear the cache to ensure a fresh test
        Cache::flush();
        
        // Call the method
        $result = $this->storyblokService->getArticle('test-article');
        
        // Assert the HTTP request was made correctly
        Http::assertSent(function ($request) {
            return $request->url() == 'https://api.storyblok.com/v2/cdn/stories/test-article' &&
                   $request['token'] == 'test-api-key' &&
                   $request['version'] == 'published';
        });
        
        // Assert the result structure
        $this->assertArrayHasKey('story', $result);
        $this->assertArrayHasKey('article', $result);
        
        // Assert the article was mapped correctly
        $this->assertInstanceOf(Article::class, $result['article']);
        $this->assertEquals('test-article', $result['article']->slug);
        $this->assertEquals('Test Article Title', $result['article']->title);
        
        // Assert the result was cached
        $cacheKey = 'storyblok_article_test-article';
        $this->assertTrue(Cache::has($cacheKey));
    }
    
    /**
     * Test that getArticlesByCategory method filters articles by category.
     */
    public function test_get_articles_by_category(): void
    {
        // Mock the HTTP response
        Http::fake([
            'api.storyblok.com/v2/cdn/stories*' => Http::response([
                'stories' => [
                    [
                        'uuid' => '123',
                        'slug' => 'test-article',
                        'name' => 'Test Article',
                        'content' => [
                            'title' => 'Test Article Title',
                            'teaser' => 'Test teaser',
                            'categories' => ['Technology'],
                        ],
                    ],
                ],
                'total' => 1,
            ], 200),
        ]);
        
        // Call the method
        $result = $this->storyblokService->getArticlesByCategory('Technology', 10, 1);
        
        // Assert the HTTP request was made with the correct filter
        Http::assertSent(function ($request) {
            return $request->url() == 'https://api.storyblok.com/v2/cdn/stories' &&
                   isset($request['filter_query']) &&
                   $request['filter_query']['categories']['in'] == 'Technology';
        });
        
        // Assert the result structure
        $this->assertArrayHasKey('stories', $result);
        $this->assertArrayHasKey('articles', $result);
        $this->assertCount(1, $result['articles']);
        $this->assertEquals(['Technology'], $result['articles'][0]->categories);
    }
    
    /**
     * Test that searchArticles method searches articles by query.
     */
    public function test_search_articles(): void
    {
        // Mock the HTTP response
        Http::fake([
            'api.storyblok.com/v2/cdn/stories*' => Http::response([
                'stories' => [
                    [
                        'uuid' => '123',
                        'slug' => 'test-article',
                        'name' => 'Test Article',
                        'content' => [
                            'title' => 'Test Article Title',
                            'teaser' => 'Test teaser with search term',
                        ],
                    ],
                ],
                'total' => 1,
            ], 200),
        ]);
        
        // Call the method
        $result = $this->storyblokService->searchArticles('search term', 10, 1);
        
        // Assert the HTTP request was made with the correct search term
        Http::assertSent(function ($request) {
            return $request->url() == 'https://api.storyblok.com/v2/cdn/stories' &&
                   $request['search_term'] == 'search term';
        });
        
        // Assert the result structure
        $this->assertArrayHasKey('stories', $result);
        $this->assertArrayHasKey('articles', $result);
        $this->assertCount(1, $result['articles']);
    }
    
    /**
     * Test that clearArticleCache method clears the cache for a specific article.
     */
    public function test_clear_article_cache(): void
    {
        // Set up a cache entry
        $cacheKey = 'storyblok_article_test-article';
        Cache::put($cacheKey, 'test data', 60);
        
        // Verify the cache entry exists
        $this->assertTrue(Cache::has($cacheKey));
        
        // Clear the cache
        $this->storyblokService->clearArticleCache('test-article');
        
        // Verify the cache entry is gone
        $this->assertFalse(Cache::has($cacheKey));
    }
    
    /**
     * Test that clearAllCaches method clears all caches.
     */
    public function test_clear_all_caches(): void
    {
        // Set up multiple cache entries
        Cache::put('storyblok_article_test1', 'test data 1', 60);
        Cache::put('storyblok_article_test2', 'test data 2', 60);
        Cache::put('storyblok_articles_10_1', 'test data 3', 60);
        
        // Verify the cache entries exist
        $this->assertTrue(Cache::has('storyblok_article_test1'));
        $this->assertTrue(Cache::has('storyblok_article_test2'));
        $this->assertTrue(Cache::has('storyblok_articles_10_1'));
        
        // Clear all caches
        $this->storyblokService->clearAllCaches();
        
        // Verify all cache entries are gone
        $this->assertFalse(Cache::has('storyblok_article_test1'));
        $this->assertFalse(Cache::has('storyblok_article_test2'));
        $this->assertFalse(Cache::has('storyblok_articles_10_1'));
    }
}
