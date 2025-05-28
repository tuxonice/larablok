<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Storyblok API Key
    |--------------------------------------------------------------------------
    |
    | This is the API key for your Storyblok space. You can find this in your
    | Storyblok dashboard under Settings > API Keys.
    |
    */
    'api_key' => env('STORYBLOK_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Cache Duration
    |--------------------------------------------------------------------------
    |
    | This value determines how long (in seconds) responses from the Storyblok
    | API will be cached. Set to 0 to disable caching.
    |
    */
    'cache_duration' => env('STORYBLOK_CACHE_DURATION', 3600),

    /*
    |--------------------------------------------------------------------------
    | Content Version
    |--------------------------------------------------------------------------
    |
    | This value determines which version of content to fetch from Storyblok.
    | Use 'published' for production and 'draft' for development.
    |
    */
    'version' => env('STORYBLOK_VERSION', 'published'),

    /*
    |--------------------------------------------------------------------------
    | Content Types
    |--------------------------------------------------------------------------
    |
    | This array defines the content types used in your Storyblok space.
    |
    */
    'content_types' => [
        'article' => [
            'model' => \App\Models\Article::class,
        ],
    ],
];
