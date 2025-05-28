<div>
    <div class="mb-6">
        <div class="flex flex-col md:flex-row gap-4 justify-between items-start mb-6">
            <h1 class="text-3xl font-bold">Blog Articles</h1>
            
            <div class="w-full md:w-1/3">
                <input 
                    wire:model.live.debounce.300ms="searchQuery" 
                    type="text" 
                    placeholder="Search articles..." 
                    class="w-full px-4 py-2 border rounded-lg"
                >
            </div>
        </div>

        @if(!empty($category))
            <div class="mb-4">
                <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700">
                    Category: {{ $category }}
                    <button wire:click="$set('category', '')" class="ml-1 text-gray-500 hover:text-gray-700">×</button>
                </span>
            </div>
        @endif
    </div>

    @if(count($articles) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articles as $article)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    @if($article->featured_image)
                        <img 
                            src="{{ $article->featured_image }}" 
                            alt="{{ $article->title ?? 'Article image' }}"
                            class="w-full h-48 object-cover"
                        >
                    @endif
                    
                    <div class="p-6">
                        <h2 class="text-xl font-bold mb-2">
                            <a href="{{ route('article.show', ['slug' => $article->slug]) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $article->title ?? 'Untitled' }}
                            </a>
                        </h2>
                        
                        @if($article->teaser)
                            <p class="text-gray-700 mb-4">{{ $article->teaser }}</p>
                        @else
                            <p class="text-gray-700 mb-4">{{ $article->summary() }}</p>
                        @endif
                        
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-500">
                                {{ $article->formattedDate('M d, Y') }}
                            </div>
                            
                            @if(count($article->categories) > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($article->categories as $cat)
                                        <button 
                                            wire:click="$set('category', '{{ $cat }}')" 
                                            class="text-xs bg-gray-200 hover:bg-gray-300 rounded-full px-2 py-1"
                                        >
                                            {{ $cat }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $this->paginationView() }}
        </div>
    @else
        <div class="bg-gray-100 rounded-lg p-6 text-center">
            <p class="text-gray-700">No articles found. {{ !empty($searchQuery) ? 'Try a different search term.' : '' }}</p>
        </div>
    @endif
</div>
