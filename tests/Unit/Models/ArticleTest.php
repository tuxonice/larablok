<?php

namespace Tests\Unit\Models;

use App\Models\Article;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Carbon;

class ArticleTest extends TestCase
{
    /**
     * Test that an Article can be created from Storyblok data.
     */
    public function test_article_from_storyblok(): void
    {
        // Mock Storyblok data
        $storyblokData = [
            'uuid' => '123-456-789',
            'slug' => 'test-article',
            'name' => 'Test Article',
            'published_at' => '2023-01-01T12:00:00Z',
            'created_at' => '2023-01-01T10:00:00Z',
            'updated_at' => '2023-01-01T11:00:00Z',
            'content' => [
                'title' => 'Test Article Title',
                'teaser' => 'This is a test article teaser',
                'content' => '<p>This is the article content</p>',
                'featured_image' => [
                    'filename' => 'https://example.com/image.jpg'
                ],
                'categories' => ['Technology', 'Laravel'],
                'author' => 'John Doe'
            ]
        ];

        // Create Article from Storyblok data
        $article = Article::fromStoryblok($storyblokData);

        // Assert basic properties
        $this->assertEquals('123-456-789', $article->uuid);
        $this->assertEquals('test-article', $article->slug);
        $this->assertEquals('Test Article', $article->name);
        $this->assertEquals('Test Article Title', $article->title);
        $this->assertEquals('This is a test article teaser', $article->teaser);
        $this->assertEquals('<p>This is the article content</p>', $article->content);
        $this->assertEquals('https://example.com/image.jpg', $article->featured_image);
        $this->assertEquals(['Technology', 'Laravel'], $article->categories);
        $this->assertEquals('John Doe', $article->author);

        // Assert dates are Carbon instances
        $this->assertInstanceOf(Carbon::class, $article->published_at);
        $this->assertInstanceOf(Carbon::class, $article->created_at);
        $this->assertInstanceOf(Carbon::class, $article->updated_at);

        // Assert formatted date
        $this->assertEquals('Jan 01, 2023', $article->formattedDate());
        $this->assertEquals('01/01/2023', $article->formattedDate('m/d/Y'));
    }

    /**
     * Test the summary method of Article.
     */
    public function test_article_summary(): void
    {
        // Create an Article with teaser
        $articleWithTeaser = new Article();
        $articleWithTeaser->teaser = 'This is a teaser';
        $articleWithTeaser->content = '<p>This is a much longer content that should not be used when teaser is available</p>';

        // Create an Article without teaser
        $articleWithoutTeaser = new Article();
        $articleWithoutTeaser->content = '<p>This is the content that should be used for summary when no teaser is available</p>';

        // Test summary with teaser
        $this->assertEquals('This is a teaser', $articleWithTeaser->summary());

        // Test summary without teaser
        $this->assertEquals('This is the content that should be used for summary when no teaser is available', $articleWithoutTeaser->summary());

        // Test summary with length limit
        $this->assertEquals('This is the...', $articleWithoutTeaser->summary(10));
    }
}
