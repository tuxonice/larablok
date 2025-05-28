<div class="mt-12 pt-8 border-t border-gray-200">
    <h3 class="text-2xl font-bold mb-6">Related Articles</h3>
    
    @if(count($relatedArticles) > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($relatedArticles as $article)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    @if($article->featured_image)
                        <img 
                            src="{{ $article->featured_image }}" 
                            alt="{{ $article->title ?? 'Article image' }}"
                            class="w-full h-40 object-cover"
                        >
                    @endif
                    
                    <div class="p-4">
                        <h4 class="text-lg font-semibold mb-2">
                            <a href="{{ route('article.show', ['slug' => $article->slug]) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $article->title ?? 'Untitled' }}
                            </a>
                        </h4>
                        
                        <p class="text-sm text-gray-500 mb-2">{{ $article->formattedDate('M d, Y') }}</p>
                        
                        <p class="text-gray-700 text-sm">
                            {{ $article->summary(100) }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500">No related articles found.</p>
    @endif
</div>
