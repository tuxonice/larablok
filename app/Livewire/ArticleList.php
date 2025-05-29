<?php

namespace App\Livewire;

use App\Services\StoryblokService;
use Livewire\Component;
use Livewire\WithPagination;

class ArticleList extends Component
{
    use WithPagination;

    public $perPage = 10;
    public $category = null;
    public $searchQuery = '';
    
    protected $queryString = [
        'category' => ['except' => ''],
        'searchQuery' => ['except' => ''],
    ];

    public function mount()
    {
        // Initialize component
    }

    public function updatedSearchQuery()
    {
        // Reset pagination when search query changes
        $this->resetPage();
    }

    public function updatedCategory()
    {
        // Reset pagination when category changes
        $this->resetPage();
    }

    public function render()
    {
        $storyblokService = app(StoryblokService::class);
        $page = $this->getPage();
        
        // Get articles based on filters
        if (!empty($this->searchQuery)) {
            $response = $storyblokService->searchArticles($this->searchQuery, $this->perPage, $page);
        } elseif (!empty($this->category)) {
            $response = $storyblokService->getArticlesByCategory($this->category, $this->perPage, $page);
        } else {
            $response = $storyblokService->getArticles($this->perPage, $page);
        }
        
        return view('livewire.article-list', [
            'articles' => $response['articles'] ?? [],
            'total' => $response['total'] ?? 0,
        ]);
    }
    
    /**
     * Generate a custom pagination view for Storyblok API results
     *
     * @return string
     */
    public function paginationView()
    {
        $storyblokService = app(StoryblokService::class);
        $page = $this->getPage();
        
        // Get total from the appropriate method based on filters
        if (!empty($this->searchQuery)) {
            $result = $storyblokService->searchArticles($this->searchQuery, $this->perPage, 1);
        } elseif (!empty($this->category)) {
            $result = $storyblokService->getArticlesByCategory($this->category, $this->perPage, 1);
        } else {
            $result = $storyblokService->getArticles($this->perPage, 1);
        }
        
        $total = $result['total'] ?? 0;
        $lastPage = ceil($total / $this->perPage);
        
        if ($lastPage <= 1) {
            return '';
        }
        
        $html = '<div class="flex justify-center"><nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">';
        
        // Previous Page Link
        if ($page > 1) {
            $html .= '<button wire:click="setPage(' . ($page - 1) . ')" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">';
            $html .= '<span class="sr-only">Previous</span>';
            $html .= '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
            $html .= '</button>';
        } else {
            $html .= '<span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400">';
            $html .= '<span class="sr-only">Previous</span>';
            $html .= '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
            $html .= '</span>';
        }
        
        // Page Links
        $window = 2; // How many links to show on each side of the current page
        
        // Calculate the start and end of the pagination window
        $start = max(1, $page - $window);
        $end = min($lastPage, $page + $window);
        
        // First Page Link (if not in window)
        if ($start > 1) {
            $html .= '<button wire:click="setPage(1)" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</button>';
            if ($start > 2) {
                $html .= '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
            }
        }
        
        // Page Window
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $page) {
                $html .= '<span aria-current="page" class="relative inline-flex items-center px-4 py-2 border border-indigo-500 bg-indigo-50 text-sm font-medium text-indigo-600">' . $i . '</span>';
            } else {
                $html .= '<button wire:click="setPage(' . $i . ')" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">' . $i . '</button>';
            }
        }
        
        // Last Page Link (if not in window)
        if ($end < $lastPage) {
            if ($end < $lastPage - 1) {
                $html .= '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
            }
            $html .= '<button wire:click="setPage(' . $lastPage . ')" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">' . $lastPage . '</button>';
        }
        
        // Next Page Link
        if ($page < $lastPage) {
            $html .= '<button wire:click="setPage(' . ($page + 1) . ')" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">';
            $html .= '<span class="sr-only">Next</span>';
            $html .= '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>';
            $html .= '</button>';
        } else {
            $html .= '<span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400">';
            $html .= '<span class="sr-only">Next</span>';
            $html .= '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>';
            $html .= '</span>';
        }
        
        $html .= '</nav></div>';
        
        return $html;
    }
}
