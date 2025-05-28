<?php

namespace App\Livewire;

use App\Models\Article;
use App\Services\StoryblokService;
use Livewire\Component;

class RelatedArticles extends Component
{
    public $currentSlug;
    public $categories = [];
    public $limit = 3;
    
    public function mount($currentSlug, $categories = [], $limit = 3)
    {
        $this->currentSlug = $currentSlug;
        $this->categories = $categories;
        $this->limit = $limit;
    }
    
    public function render()
    {
        $storyblokService = app(StoryblokService::class);
        $relatedArticles = [];
        
        // If we have categories, try to find related articles
        if (!empty($this->categories)) {
            // Use the first category to find related articles
            $category = $this->categories[0];
            $response = $storyblokService->getArticlesByCategory($category, $this->limit + 1, 1);
            
            if (isset($response['articles']) && !empty($response['articles'])) {
                // Filter out the current article
                $relatedArticles = array_filter($response['articles'], function($article) {
                    return $article->slug !== $this->currentSlug;
                });
                
                // Limit the number of related articles
                $relatedArticles = array_slice($relatedArticles, 0, $this->limit);
            }
        }
        
        // If we don't have enough related articles by category, get the latest articles
        if (count($relatedArticles) < $this->limit) {
            $response = $storyblokService->getArticles($this->limit + 1, 1);
            
            if (isset($response['articles']) && !empty($response['articles'])) {
                // Filter out the current article and any already included articles
                $additionalArticles = array_filter($response['articles'], function($article) use ($relatedArticles) {
                    if ($article->slug === $this->currentSlug) {
                        return false;
                    }
                    
                    foreach ($relatedArticles as $relatedArticle) {
                        if ($relatedArticle->slug === $article->slug) {
                            return false;
                        }
                    }
                    
                    return true;
                });
                
                // Add additional articles to reach the limit
                $neededCount = $this->limit - count($relatedArticles);
                $additionalArticles = array_slice($additionalArticles, 0, $neededCount);
                
                $relatedArticles = array_merge($relatedArticles, $additionalArticles);
            }
        }
        
        return view('livewire.related-articles', [
            'relatedArticles' => $relatedArticles
        ]);
    }
}
