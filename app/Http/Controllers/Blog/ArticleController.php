<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Services\StoryblokService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected $storyblokService;

    public function __construct(StoryblokService $storyblokService)
    {
        $this->storyblokService = $storyblokService;
    }

    /**
     * Display the home page with article listing
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Display a specific article by slug
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        return view('article', [
            'slug' => $slug
        ]);
    }
}
