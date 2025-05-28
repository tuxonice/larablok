<?php

namespace App\Livewire;

use App\Services\StoryblokService;
use Livewire\Component;

class CategoryList extends Component
{
    public $activeCategory = '';
    
    public function mount($activeCategory = '')
    {
        $this->activeCategory = $activeCategory;
    }
    
    public function setCategory($category)
    {
        return redirect()->route('home', ['category' => $category]);
    }
    
    public function render()
    {
        $storyblokService = app(StoryblokService::class);
        $categories = [];
        
        // Get all articles to extract categories
        $response = $storyblokService->getArticles(100, 1);
        
        if (isset($response['articles']) && !empty($response['articles'])) {
            // Extract all categories from articles
            $allCategories = [];
            foreach ($response['articles'] as $article) {
                if (!empty($article->categories)) {
                    foreach ($article->categories as $category) {
                        if (!isset($allCategories[$category])) {
                            $allCategories[$category] = 0;
                        }
                        $allCategories[$category]++;
                    }
                }
            }
            
            // Sort categories by count (most used first)
            arsort($allCategories);
            
            // Convert to the format needed for the view
            foreach ($allCategories as $name => $count) {
                $categories[] = [
                    'name' => $name,
                    'count' => $count,
                    'active' => $name === $this->activeCategory
                ];
            }
        }
        
        return view('livewire.category-list', [
            'categories' => $categories
        ]);
    }
}
