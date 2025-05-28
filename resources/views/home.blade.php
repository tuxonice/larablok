@extends('layouts.app')

@section('title', 'Blog')

@section('content')
    <div class="flex flex-col md:flex-row gap-8">
        <div class="md:w-3/4">
            <livewire:article-list />
        </div>
        
        <div class="md:w-1/4">
            <livewire:category-list :active-category="request()->query('category', '')" />
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-4">About</h3>
                <p class="text-gray-700">
                    Welcome to LaraBlok, a modern blog built with Laravel, Livewire, and Storyblok CMS. 
                    Browse our articles and discover great content.
                </p>
            </div>
        </div>
    </div>
@endsection
