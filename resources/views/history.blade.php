<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Writing History') }}
            </h2>
            
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                &larr; Back to Editor
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 flex space-x-2 overflow-x-auto pb-2">
                @php
                    $filters = [
                        'all' => 'All History',
                        'pro' => 'Professional', 
                        'casual' => 'Casual', 
                        'fix' => 'Fix Grammar', 
                        'shorten' => 'Shorten'
                    ];
                @endphp

                @foreach($filters as $key => $label)
                    <a href="{{ route('history', ['filter' => $key]) }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 
                       {{ $currentFilter === $key 
                           ? 'bg-indigo-600 text-white shadow-md' 
                           : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="space-y-6">
                @forelse($savedTexts as $text)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 capitalize">
                                {{ $text->type ?? 'General' }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($text->created_at)->diffForHumans() }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Original Text</h3>
                                <div class="p-3 bg-gray-50 rounded-md text-gray-700 text-sm h-full whitespace-pre-wrap border border-gray-200">
                                    {{ $text->original_text }}
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Rewritten Result (Editable)</h3>
                                <form action="{{ route('history.update', $text->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="filter" value="{{ $currentFilter }}">
                                    
                                    <textarea name="generated_text" rows="4" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm mb-2"
                                    >{{ $text->generated_text }}</textarea>

                                    <div class="flex justify-end space-x-2">
                                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                                
                                <form action="{{ route('delete', $text->id) }}" method="POST" class="mt-2 flex justify-end">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-900 underline">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                        <p class="text-gray-500">No history found for this category.</p>
                        <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:underline mt-2 inline-block">Create a new text</a>
                    </div>
                @endforelse
            </div>
            
        </div>
    </div>
</x-app-layout>