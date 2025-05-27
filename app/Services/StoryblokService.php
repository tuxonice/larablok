<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class StoryblokService
{
    protected string $apiKey;
    protected string $apiVersion = 'v2';
    protected string $baseUrl = 'https://api.storyblok.com';
    protected int $cacheTime = 3600; // Cache for 1 hour by default

    public function __construct()
    {
        $this->apiKey = config('services.storyblok.api_key');
    }

    /**
     * Get all articles (stories) from Storyblok
     *
     * @param int $perPage Number of articles per page
     * @param int $page Page number
     * @return array
     */
    public function getArticles(int $perPage = 10, int $page = 1): array
    {
        $cacheKey = "storyblok_articles_{$perPage}_{$page}";

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($perPage, $page) {
            $response = Http::get("{$this->baseUrl}/{$this->apiVersion}/cdn/stories", [
                'token' => $this->apiKey,
                'per_page' => $perPage,
                'page' => $page,
                'content_type' => 'article', // Assuming you have an 'article' content type in Storyblok
                'version' => 'published',
                'sort_by' => 'published_at:desc',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['stories' => [], 'total' => 0, 'error' => $response->status()];
        });
    }

    /**
     * Get a single article by slug
     *
     * @param string $slug The article slug
     * @return array
     */
    public function getArticle(string $slug): array
    {
        $cacheKey = "storyblok_article_{$slug}";

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($slug) {
            $response = Http::get("{$this->baseUrl}/{$this->apiVersion}/cdn/stories/{$slug}", [
                'token' => $this->apiKey,
                'version' => 'published',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['story' => null, 'error' => $response->status()];
        });
    }

    /**
     * Get articles by category/tag
     *
     * @param string $category Category or tag name
     * @param int $perPage Number of articles per page
     * @param int $page Page number
     * @return array
     */
    public function getArticlesByCategory(string $category, int $perPage = 10, int $page = 1): array
    {
        $cacheKey = "storyblok_articles_category_{$category}_{$perPage}_{$page}";

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($category, $perPage, $page) {
            $response = Http::get("{$this->baseUrl}/{$this->apiVersion}/cdn/stories", [
                'token' => $this->apiKey,
                'per_page' => $perPage,
                'page' => $page,
                'content_type' => 'article',
                'filter_query' => [
                    'categories' => [
                        'in' => $category
                    ]
                ],
                'version' => 'published',
                'sort_by' => 'published_at:desc',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['stories' => [], 'total' => 0, 'error' => $response->status()];
        });
    }

    /**
     * Search articles by query
     *
     * @param string $query Search query
     * @param int $perPage Number of articles per page
     * @param int $page Page number
     * @return array
     */
    public function searchArticles(string $query, int $perPage = 10, int $page = 1): array
    {
        $cacheKey = "storyblok_search_{$query}_{$perPage}_{$page}";

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($query, $perPage, $page) {
            $response = Http::get("{$this->baseUrl}/{$this->apiVersion}/cdn/stories", [
                'token' => $this->apiKey,
                'per_page' => $perPage,
                'page' => $page,
                'content_type' => 'article',
                'search_term' => $query,
                'version' => 'published',
                'sort_by' => 'published_at:desc',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['stories' => [], 'total' => 0, 'error' => $response->status()];
        });
    }

    /**
     * Clear the cache for a specific article
     *
     * @param string $slug The article slug
     * @return void
     */
    public function clearArticleCache(string $slug): void
    {
        Cache::forget("storyblok_article_{$slug}");
    }

    /**
     * Clear all article caches
     *
     * @return void
     */
    public function clearAllCaches(): void
    {
        Cache::flush();
    }
}
