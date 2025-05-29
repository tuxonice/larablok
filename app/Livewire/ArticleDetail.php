<?php

namespace App\Livewire;

use App\Models\Article;
use App\Services\StoryblokService;
use Livewire\Component;

class ArticleDetail extends Component
{
    public $slug;
    public $article = null;
    public $error = null;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->loadArticle();
    }

    protected function loadArticle()
    {
        $storyblokService = app(StoryblokService::class);
        $response = $storyblokService->getArticle($this->slug);

        if (isset($response['article'])) {
            $this->article = $response['article'];
        } else {
            $this->error = $response['error'] ?? 'Article not found';
        }
    }

    public function render()
    {
        return view('livewire.article-detail', [
            'article' => $this->article,
            'error' => $this->error,
        ]);
    }
}
