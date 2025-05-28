<?php

namespace App\Models;

use Illuminate\Support\Carbon;

class Article
{
    public $uuid;
    public $slug;
    public $name;
    public $title;
    public $teaser;
    public $content;
    public $featured_image;
    public $categories = [];
    public $author;
    public $published_at;
    public $created_at;
    public $updated_at;

    /**
     * Create a new Article instance from Storyblok data
     *
     * @param array $storyblokData
     * @return static
     */
    public static function fromStoryblok(array $storyblokData): self
    {
        $article = new static();
        
        // Map Storyblok fields to our model
        $article->uuid = $storyblokData['uuid'] ?? null;
        $article->slug = $storyblokData['slug'] ?? null;
        $article->name = $storyblokData['name'] ?? null;
        
        // Content fields
        $content = $storyblokData['content'] ?? [];
        $article->title = $content['title'] ?? $article->name;
        $article->teaser = $content['teaser'] ?? null;
        $article->content = $content['content'] ?? null;
        $article->featured_image = $content['featured_image']['filename'] ?? null;
        $article->categories = $content['categories'] ?? [];
        $article->author = $content['author'] ?? null;
        
        // Dates
        $article->published_at = $storyblokData['published_at'] 
            ? Carbon::parse($storyblokData['published_at']) 
            : null;
        
        $article->created_at = $storyblokData['created_at'] 
            ? Carbon::parse($storyblokData['created_at']) 
            : null;
        
        $article->updated_at = $storyblokData['updated_at'] 
            ? Carbon::parse($storyblokData['updated_at']) 
            : null;
        
        return $article;
    }

    /**
     * Format the published date
     *
     * @param string $format
     * @return string|null
     */
    public function formattedDate(string $format = 'F d, Y'): ?string
    {
        return $this->published_at ? $this->published_at->format($format) : null;
    }

    /**
     * Get a summary of the content
     *
     * @param int $length
     * @return string
     */
    public function summary(int $length = 150): string
    {
        if ($this->teaser) {
            return $this->teaser;
        }
        
        // Strip HTML and truncate content
        $text = strip_tags($this->content ?? '');
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . '...';
    }
}
