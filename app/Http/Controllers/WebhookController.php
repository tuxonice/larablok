<?php

namespace App\Http\Controllers;

use App\Services\StoryblokService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle Storyblok webhook requests
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Services\StoryblokService $storyblokService
     * @return \Illuminate\Http\Response
     */
    public function storyblok(Request $request, StoryblokService $storyblokService)
    {
        // Validate the webhook signature if needed
        // $signature = $request->header('X-Storyblok-Signature');
        
        // Log the webhook event
        Log::info('Storyblok webhook received', [
            'action' => $request->input('action'),
            'story_id' => $request->input('story_id'),
            'story_slug' => $request->input('story.slug')
        ]);
        
        // Get the action and story data
        $action = $request->input('action');
        $storySlug = $request->input('story.slug');
        
        // Handle different webhook actions
        switch ($action) {
            case 'published':
            case 'unpublished':
            case 'deleted':
                if ($storySlug) {
                    // Clear cache for the specific story
                    $storyblokService->clearArticleCache($storySlug);
                    Log::info("Cache cleared for story: {$storySlug}");
                } else {
                    // If no specific story, clear all caches
                    $storyblokService->clearAllCaches();
                    Log::info("All caches cleared due to Storyblok webhook");
                }
                break;
                
            default:
                // For other actions, clear all caches to be safe
                $storyblokService->clearAllCaches();
                Log::info("All caches cleared due to unknown Storyblok webhook action: {$action}");
                break;
        }
        
        return response()->json(['status' => 'success', 'message' => 'Webhook processed successfully']);
    }
}
