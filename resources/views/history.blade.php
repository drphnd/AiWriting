{{-- resources/views/history.blade.php --}}

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - AI Writing Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #1e293b; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #64748b; }
        
        /* Light scrollbar for main content */
        .content-scrollbar::-webkit-scrollbar { width: 6px; }
        .content-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .content-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .content-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="h-full flex flex-col md:flex-row overflow-hidden bg-white">

    <div class="w-full md:w-80 bg-slate-900 text-slate-300 flex flex-col h-auto md:h-full flex-shrink-0 shadow-xl z-20">
        <div class="p-6 border-b border-slate-800 bg-slate-900 z-10 flex justify-between items-center">
            <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 cursor-pointer">
                <div class="bg-slate-800 p-1.5 rounded-lg group-hover:bg-indigo-600 transition-colors border border-slate-700 group-hover:border-indigo-500">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-white"></i>
                </div>
                <div class="flex flex-col">
                    <h2 class="font-bold text-white tracking-tight text-sm group-hover:text-indigo-300 transition-colors flex items-center gap-2">
                        Back
                    </h2>
                </div>
            </a>
        </div>
        
        <div class="p-6">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Quick Stats</h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-800 p-4 rounded-xl border border-slate-700">
                    <span class="text-2xl font-bold text-white block">{{ count($savedTexts) }}</span>
                    <span class="text-xs text-slate-400">Total Saved</span>
                </div>
                <div class="bg-slate-800 p-4 rounded-xl border border-slate-700">
                    <span class="text-2xl font-bold text-indigo-400 block">{{ $savedTexts->where('type', 'pro')->count() }}</span>
                    <span class="text-xs text-slate-400">Professional</span>
                </div>
            </div>
        </div>

        <div class="mt-auto p-4 border-t border-slate-800 bg-slate-900 text-xs text-slate-500 flex justify-between items-center">
            <span>Logged in as {{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="hover:text-white transition-colors flex items-center gap-1">
                    <i data-lucide="log-out" class="w-3 h-3"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="flex-1 flex flex-col h-full relative overflow-hidden bg-slate-50">
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 z-0"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-slate-50 to-purple-50 z-0"></div>

        <div class="bg-white/80 backdrop-blur-md border-b border-slate-200 px-8 py-4 flex justify-between items-center z-10 sticky top-0">
            <h1 class="text-lg font-bold text-slate-800 flex items-center gap-2.5">
                <i data-lucide="library" class="w-5 h-5 text-indigo-600 fill-indigo-100"></i>
                Writing History
            </h1>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500">Manage your saved generations</span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6 md:p-10 z-10 relative content-scrollbar">
            <div class="max-w-5xl mx-auto space-y-8">
                
                <div class="flex justify-center space-x-2 overflow-x-auto pb-2">
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
                           class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 border shadow-sm
                           {{ $currentFilter === $key 
                               ? 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-200 ring-2 ring-indigo-100' 
                               : 'bg-white text-slate-600 hover:bg-slate-50 hover:text-indigo-600 border-slate-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <div class="space-y-6">
                    @forelse($savedTexts as $text)
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                <div class="flex items-center gap-3">
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border
                                        {{ $text->type == 'pro' ? 'bg-indigo-50 text-indigo-600 border-indigo-100' : '' }}
                                        {{ $text->type == 'casual' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : '' }}
                                        {{ $text->type == 'fix' ? 'bg-amber-50 text-amber-600 border-amber-100' : '' }}
                                        {{ $text->type == 'shorten' ? 'bg-purple-50 text-purple-600 border-purple-100' : '' }}
                                        {{ !in_array($text->type, ['pro', 'casual', 'fix', 'shorten']) ? 'bg-slate-100 text-slate-600 border-slate-200' : '' }}
                                    ">
                                        {{ $text->type ?? 'General' }}
                                    </span>
                                    <span class="text-xs text-slate-400 flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        {{ \Carbon\Carbon::parse($text->created_at)->diffForHumans() }}
                                    </span>
                                </div>
                                <form action="{{ route('delete', $text->id) }}" method="POST" onsubmit="return confirm('Delete this item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors p-1.5 hover:bg-red-50 rounded-lg">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>

                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="flex flex-col">
                                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Original
                                    </h3>
                                    <div class="flex-grow p-4 bg-slate-50 rounded-xl text-slate-600 text-sm whitespace-pre-wrap border border-slate-100 leading-relaxed font-medium">{{ trim($text->original_text) }}</div>
                                </div>

                                <div class="flex flex-col">
                                    <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Rewritten Result
                                    </h3>
                                    
                                    <form action="{{ route('history.update', $text->id) }}" method="POST" class="flex-grow flex flex-col group">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="filter" value="{{ $currentFilter }}">
                                        
                                        <textarea name="generated_text" rows="6" 
                                            class="w-full flex-grow rounded-xl border-slate-200 bg-white text-slate-700 text-sm leading-relaxed p-4 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 resize-none transition-all"
                                        >{{ $text->generated_text }}</textarea>

                                        <div class="mt-4 flex justify-end opacity-0 group-focus-within:opacity-100 transition-opacity duration-200">
                                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                                                <i data-lucide="save" class="w-3.5 h-3.5"></i> Save Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-20">
                            <div class="bg-slate-50 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                <i data-lucide="ghost" class="w-8 h-8 text-slate-300"></i>
                            </div>
                            <h3 class="text-lg font-medium text-slate-900">No history found</h3>
                            <p class="mt-1 text-slate-500">You haven't generated any text for this category yet.</p>
                            <div class="mt-6">
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all">
                                    <i data-lucide="pen-tool" class="w-4 h-4"></i>
                                    Start Writing
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
                
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>