<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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
        $text = strip_tags($this->getRenderedContent());
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . '...';
    }
    
    /**
     * Render the Storyblok rich text content as HTML
     *
     * @return string
     */
    public function getRenderedContent(): string
    {
        // If content is already a string, return it
        if (is_string($this->content)) {
            return $this->content;
        }
        
        // If content is null or empty, return empty string
        if (empty($this->content)) {
            return '';
        }
        
        // If content is a rich text object from Storyblok
        if (is_array($this->content) && isset($this->content['content'])) {
            return $this->renderRichText($this->content);
        }
        
        // Fallback: convert to string if possible
        return (string) $this->content;
    }
    
    /**
     * Recursively render Storyblok rich text content
     *
     * @param array $richText
     * @return string
     */
    protected function renderRichText(array $richText): string
    {
        $html = '';
        
        // Process the content array
        if (isset($richText['content']) && is_array($richText['content'])) {
            foreach ($richText['content'] as $block) {
                $html .= $this->renderBlock($block);
            }
        }
        
        return $html;
    }
    
    /**
     * Render a single rich text block
     *
     * @param array $block
     * @return string
     */
    protected function renderBlock(array $block): string
    {
        $type = $block['type'] ?? '';
        $content = '';
        
        // Recursively process child content
        if (isset($block['content']) && is_array($block['content'])) {
            foreach ($block['content'] as $child) {
                $content .= $this->renderBlock($child);
            }
        } elseif (isset($block['text'])) {
            $content = htmlspecialchars($block['text']);
        }
        
        // Apply marks (bold, italic, etc.)
        if (isset($block['marks']) && is_array($block['marks'])) {
            foreach ($block['marks'] as $mark) {
                $markType = $mark['type'] ?? '';
                
                switch ($markType) {
                    case 'bold':
                        $content = '<strong>' . $content . '</strong>';
                        break;
                    case 'italic':
                        $content = '<em>' . $content . '</em>';
                        break;
                    case 'underline':
                        $content = '<u>' . $content . '</u>';
                        break;
                    case 'strike':
                        $content = '<s>' . $content . '</s>';
                        break;
                    case 'code':
                        $content = '<code>' . $content . '</code>';
                        break;
                    case 'link':
                        $attrs = $mark['attrs'] ?? [];
                        $href = $attrs['href'] ?? '#';
                        $target = isset($attrs['target']) ? ' target="' . $attrs['target'] . '"' : '';
                        $content = '<a href="' . $href . '"' . $target . '>' . $content . '</a>';
                        break;
                }
            }
        }
        
        // Wrap content based on block type
        switch ($type) {
            case 'heading':
                $level = $block['attrs']['level'] ?? 1;
                return "<h{$level}>{$content}</h{$level}>";
            case 'paragraph':
                return "<p>{$content}</p>";
            case 'bullet_list':
                return "<ul>{$content}</ul>";
            case 'ordered_list':
                return "<ol>{$content}</ol>";
            case 'list_item':
                return "<li>{$content}</li>";
            case 'blockquote':
                return "<blockquote>{$content}</blockquote>";
            case 'code_block':
                return "<pre><code>{$content}</code></pre>";
            case 'image':
                $attrs = $block['attrs'] ?? [];
                $src = $attrs['src'] ?? '';
                $alt = $attrs['alt'] ?? '';
                return "<img src=\"{$src}\" alt=\"{$alt}\" class=\"w-full h-auto rounded-lg\">";
            case 'hard_break':
                return "<br>";
            case 'horizontal_rule':
                return "<hr>";
            default:
                return $content;
        }
    }
}
