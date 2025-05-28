<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-bold mb-4">Categories</h3>
    
    @if(count($categories) > 0)
        <div class="flex flex-wrap gap-2">
            <button 
                wire:click="setCategory('')"
                class="text-sm px-3 py-1 rounded-full {{ empty($activeCategory) ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700' }}"
            >
                All
            </button>
            
            @foreach($categories as $category)
                <button 
                    wire:click="setCategory('{{ $category['name'] }}')"
                    class="text-sm px-3 py-1 rounded-full {{ $category['active'] ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700' }}"
                >
                    {{ $category['name'] }} ({{ $category['count'] }})
                </button>
            @endforeach
        </div>
    @else
        <p class="text-gray-500">No categories found.</p>
    @endif
</div>
