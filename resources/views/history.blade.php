<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center max-w-4xl mx-auto">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Writing History') }}
            </h2>
            
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                &larr; Back to Editor
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 flex justify-center space-x-2 overflow-x-auto pb-2">
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
                       class="px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 border
                       {{ $currentFilter === $key 
                           ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' 
                           : 'bg-white text-gray-600 hover:bg-gray-50 border-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="space-y-6">
                @forelse($savedTexts as $text)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 capitalize">
                                {{ $text->type ?? 'General' }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($text->created_at)->diffForHumans() }}
                            </span>
                        </div>

                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Original Text</h3>
                                <div class="flex-grow p-4 bg-gray-50 rounded-lg text-gray-700 text-sm whitespace-pre-wrap border border-gray-200 leading-relaxed">
                                    {{ $text->original_text }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Rewritten Result</h3>
                                
                                <form action="{{ route('history.update', $text->id) }}" method="POST" class="flex-grow flex flex-col">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="filter" value="{{ $currentFilter }}">
                                    
                                    <textarea name="generated_text" rows="6" 
                                        class="w-full flex-grow rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm leading-relaxed p-4"
                                    >{{ $text->generated_text }}</textarea>

                                    <div class="mt-4 flex justify-between items-center">
                                        <button type="submit" form="delete-form-{{ $text->id }}" class="text-sm text-red-500 hover:text-red-700 hover:underline">
                                            Delete
                                        </button>

                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                                            Save Changes
                                        </button>
                                    </div>
                                </form>

                                <form id="delete-form-{{ $text->id }}" action="{{ route('delete', $text->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center border border-gray-200">
                        <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">No history found</h3>
                        <p class="mt-1 text-gray-500">You haven't generated any text for this category yet.</p>
                        <div class="mt-6">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Start Writing
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
            
        </div>
    </div>
</x-app-layout>