<div class="max-w-4xl mx-auto">
    @if($article)
        <article class="bg-white rounded-lg shadow-md overflow-hidden p-6">
            <div class="mb-6">
                <h1 class="text-3xl font-bold mb-4">{{ $article->title ?? 'Untitled' }}</h1>
                
                <div class="flex items-center justify-between text-gray-500 mb-6">
                    <div>
                        @if($article->published_at)
                            <span>{{ $article->formattedDate('F d, Y') }}</span>
                        @endif
                        
                        @if($article->author)
                            <span class="mx-2">•</span>
                            <span>{{ $article->author }}</span>
                        @endif
                    </div>
                    
                    @if(count($article->categories) > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($article->categories as $category)
                                <a 
                                    href="{{ route('home', ['category' => $category]) }}"
                                    class="text-xs bg-gray-200 hover:bg-gray-300 rounded-full px-3 py-1"
                                >
                                    {{ $category }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            @if($article->featured_image)
                <div class="mb-8">
                    <img 
                        src="{{ $article->featured_image }}" 
                        alt="{{ $article->title ?? 'Article image' }}"
                        class="w-full h-auto rounded-lg"
                    >
                </div>
            @endif
            
            <div class="prose max-w-none">
                @if($article->content)
                    {!! $article->content !!}
                @endif
            </div>
            
            @if($article->categories)
                <livewire:related-articles :current-slug="$article->slug" :categories="$article->categories" />
            @endif
            
            <div class="mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">
                    &larr; Back to all articles
                </a>
            </div>
        </article>
    @else
        <div class="bg-red-100 text-red-700 p-4 rounded-lg">
            <p>{{ $error ?? 'Article not found' }}</p>
            <div class="mt-4">
                <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">
                    &larr; Back to all articles
                </a>
            </div>
        </div>
    @endif
</div>
