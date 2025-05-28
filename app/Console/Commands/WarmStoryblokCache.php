<?php

namespace App\Console\Commands;

use App\Services\StoryblokService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class WarmStoryblokCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storyblok:warm-cache {--clear : Clear existing cache before warming}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm the cache with Storyblok content';

    /**
     * The Storyblok service.
     *
     * @var \App\Services\StoryblokService
     */
    protected $storyblokService;

    /**
     * Create a new command instance.
     */
    public function __construct(StoryblokService $storyblokService)
    {
        parent::__construct();
        $this->storyblokService = $storyblokService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('clear')) {
            $this->info('Clearing existing Storyblok cache...');
            $this->storyblokService->clearAllCaches();
            $this->info('Cache cleared.');
        }

        $this->info('Warming Storyblok cache...');

        // Get all articles (first page)
        $this->info('Caching article list (page 1)...');
        $articlesResponse = $this->storyblokService->getArticles(10, 1);
        $totalArticles = $articlesResponse['total'] ?? 0;
        $totalPages = ceil($totalArticles / 10);

        $this->info("Found {$totalArticles} articles across {$totalPages} pages.");

        // Cache additional pages if needed
        if ($totalPages > 1) {
            $this->info('Caching additional article pages...');
            $bar = $this->output->createProgressBar($totalPages - 1);
            $bar->start();

            for ($page = 2; $page <= $totalPages; $page++) {
                $this->storyblokService->getArticles(10, $page);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
        }

        // Cache individual articles
        if (isset($articlesResponse['articles']) && count($articlesResponse['articles']) > 0) {
            $this->info('Caching individual articles...');
            $bar = $this->output->createProgressBar(count($articlesResponse['articles']));
            $bar->start();

            foreach ($articlesResponse['articles'] as $article) {
                $this->storyblokService->getArticle($article->slug);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
        }

        // Extract and cache categories
        if (isset($articlesResponse['articles']) && count($articlesResponse['articles']) > 0) {
            $this->info('Caching articles by category...');
            
            // Extract unique categories
            $categories = [];
            foreach ($articlesResponse['articles'] as $article) {
                if (!empty($article->categories)) {
                    foreach ($article->categories as $category) {
                        if (!in_array($category, $categories)) {
                            $categories[] = $category;
                        }
                    }
                }
            }
            
            $this->info('Found ' . count($categories) . ' categories.');
            
            if (count($categories) > 0) {
                $bar = $this->output->createProgressBar(count($categories));
                $bar->start();
                
                foreach ($categories as $category) {
                    $this->storyblokService->getArticlesByCategory($category, 10, 1);
                    $bar->advance();
                }
                
                $bar->finish();
                $this->newLine();
            }
        }

        $this->info('Storyblok cache warming completed successfully!');
        return Command::SUCCESS;
    }
}
