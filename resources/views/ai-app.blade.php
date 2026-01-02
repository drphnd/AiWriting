{{-- resources/views/ai-app.blade.php --}}

<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Writing Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #1e293b;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
    </style>
</head>

<body class="h-full flex flex-col md:flex-row overflow-hidden bg-white">

    <div class="w-full md:w-80 bg-slate-900 text-slate-300 flex flex-col h-1/3 md:h-full flex-shrink-0 shadow-xl z-20">
        <div class="p-6 border-b border-slate-800 bg-slate-900 z-10 flex justify-between items-center">
            <a href="{{ route('history') }}" class="group flex items-center gap-3 cursor-pointer">
                <div class="bg-indigo-600 p-1.5 rounded-lg group-hover:bg-indigo-500 transition-colors">
                    <i data-lucide="library" class="w-4 h-4 text-white"></i>
                </div>
                <div class="flex flex-col">
                    <h2
                        class="font-bold text-white tracking-tight text-sm group-hover:text-indigo-300 transition-colors flex items-center gap-2">
                        History
                        <i data-lucide="external-link" class="w-3 h-3 text-slate-500 group-hover:text-indigo-400"></i>
                    </h2>
                </div>
            </a>
            <span
                class="text-xs font-medium px-2 py-1 rounded-full bg-slate-800 text-slate-400">{{ count($savedTexts) }}
                Saved</span>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
            @forelse($savedTexts as $item)
                <div
                    class="group bg-slate-800/50 hover:bg-slate-800 p-4 rounded-xl border border-slate-700/50 hover:border-indigo-500/30 transition-all duration-200 relative">
                    <div class="flex justify-between items-start mb-2">
                        <span
                            class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border border-slate-600
                            {{ $item->type == 'pro' ? 'bg-indigo-900/30 text-indigo-300 border-indigo-800' : '' }}
                            {{ $item->type == 'casual' ? 'bg-emerald-900/30 text-emerald-300 border-emerald-800' : '' }}
                            {{ $item->type == 'fix' ? 'bg-amber-900/30 text-amber-300 border-amber-800' : '' }}
                            {{ $item->type == 'shorten' ? 'bg-purple-900/30 text-purple-300 border-purple-800' : '' }}
                        ">
                            {{ $item->type ?? 'Draft' }}
                        </span>
                        <form action="{{ route('delete', $item->id) }}" method="POST"
                            onsubmit="return confirm('Delete this item?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-slate-500 hover:text-red-400 transition-colors p-1 opacity-0 group-hover:opacity-100">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>

                    <p
                        class="text-xs text-slate-400 line-clamp-2 leading-relaxed mb-3 group-hover:text-slate-200 transition-colors">
                        {{ $item->generated_text }}
                    </p>

                    <div class="flex justify-between items-center pt-2 border-t border-slate-700/50">
                        <span
                            class="text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans(null, true) }}</span>
                        <button onclick="copyText(`{{ addslashes($item->generated_text) }}`)"
                            class="text-slate-500 hover:text-white text-[10px] flex items-center gap-1.5 transition-colors">
                            <i data-lucide="copy" class="w-3 h-3"></i> Copy
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 px-6">
                    <div class="bg-slate-800 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="inbox" class="w-5 h-5 text-slate-600"></i>
                    </div>
                    <p class="text-slate-500 text-sm">Library is empty.</p>
                </div>
            @endforelse
        </div>

        <div
            class="p-4 border-t border-slate-800 bg-slate-900 text-xs text-slate-500 flex justify-between items-center">
            <span>Logged in as {{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="hover:text-white transition-colors flex items-center gap-1">
                    <i data-lucide="log-out" class="w-3 h-3"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="flex-1 flex flex-col h-2/3 md:h-full relative overflow-hidden bg-slate-50">
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 z-0"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-slate-50 to-purple-50 z-0"></div>

        <div
            class="bg-white/80 backdrop-blur-md border-b border-slate-200 px-8 py-4 flex justify-between items-center z-10 sticky top-0">
            <h1 class="text-lg font-bold text-slate-800 flex items-center gap-2.5">
                <i data-lucide="sparkles" class="w-5 h-5 text-indigo-600 fill-indigo-100"></i>
                AI Writer Assistant
            </h1>
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    System Ready
                </span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6 md:p-10 z-10 relative">
            <div class="max-w-5xl mx-auto space-y-8">

                <div
                    class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <label for="inputText"
                            class="text-xs font-bold text-slate-500 uppercase tracking-wide flex items-center gap-2">
                            <i data-lucide="pen-tool" class="w-3.5 h-3.5"></i> Input Text
                        </label>
                        <span class="text-xs text-slate-400">Enter your draft below</span>
                    </div>

                    <textarea id="inputText" rows="5"
                        class="w-full p-6 border-0 focus:ring-0 text-slate-700 placeholder:text-slate-300 text-base leading-relaxed resize-none"
                        placeholder="Paste your rough notes or draft here to verify grammar, change tone, or summarize..."></textarea>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                        <p class="text-xs font-medium text-slate-400 mb-3 uppercase tracking-wider">Select Action:</p>
                        <div class="flex flex-wrap gap-3">
                            <button onclick="rewrite('pro')"
                                class="group flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-medium shadow-sm hover:border-indigo-400 hover:text-indigo-600 hover:shadow-md hover:-translate-y-0.5 transition-all">
                                <div class="bg-indigo-50 p-1.5 rounded-lg group-hover:bg-indigo-100 transition-colors">
                                    <i data-lucide="briefcase" class="w-4 h-4 text-indigo-600"></i>
                                </div>
                                Professional
                            </button>
                            <button onclick="rewrite('casual')"
                                class="group flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-medium shadow-sm hover:border-emerald-400 hover:text-emerald-600 hover:shadow-md hover:-translate-y-0.5 transition-all">
                                <div
                                    class="bg-emerald-50 p-1.5 rounded-lg group-hover:bg-emerald-100 transition-colors">
                                    <i data-lucide="coffee" class="w-4 h-4 text-emerald-600"></i>
                                </div>
                                Casual
                            </button>
                            <button onclick="rewrite('fix')"
                                class="group flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-medium shadow-sm hover:border-amber-400 hover:text-amber-600 hover:shadow-md hover:-translate-y-0.5 transition-all">
                                <div class="bg-amber-50 p-1.5 rounded-lg group-hover:bg-amber-100 transition-colors">
                                    <i data-lucide="check-circle-2" class="w-4 h-4 text-amber-600"></i>
                                </div>
                                Fix Grammar
                            </button>
                            {{-- <button onclick="rewrite('shorten')" class="group flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-medium shadow-sm hover:border-purple-400 hover:text-purple-600 hover:shadow-md hover:-translate-y-0.5 transition-all"> --}}
                            <button onclick="openShortenModal()"
                                class="group flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-medium shadow-sm hover:border-purple-400 hover:text-purple-600 hover:shadow-md hover:-translate-y-0.5 transition-all">
                                <div class="bg-purple-50 p-1.5 rounded-lg group-hover:bg-purple-100 transition-colors">
                                    <i data-lucide="scissors" class="w-4 h-4 text-purple-600"></i>
                                </div>
                                Shorten
                            </button>
                        </div>
                    </div>
                </div>

                <div id="loadingArea" class="hidden py-12 flex flex-col items-center justify-center">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-full border-4 border-indigo-100 animate-spin border-t-indigo-600">
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i data-lucide="sparkles" class="w-4 h-4 text-indigo-600"></i>
                        </div>
                    </div>
                    <p class="text-slate-500 text-sm font-medium mt-4 animate-pulse">Generative AI is processing...</p>
                </div>

                <div id="resultArea"
                    class="hidden bg-white rounded-2xl shadow-lg shadow-indigo-100/50 border border-indigo-100 overflow-hidden ring-1 ring-indigo-50">
                    <div
                        class="border-b border-indigo-50 bg-gradient-to-r from-indigo-50/50 to-white px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="bg-indigo-600 p-1.5 rounded-lg shadow-sm">
                                <i data-lucide="bot" class="w-4 h-4 text-white"></i>
                            </div>
                            <span class="text-sm font-bold text-indigo-900 tracking-wide">AI Suggestions</span>
                        </div>
                        <button onclick="copyResult()"
                            class="text-slate-400 hover:text-indigo-600 transition-colors p-2 hover:bg-indigo-50 rounded-lg"
                            title="Copy All">
                            <i data-lucide="copy" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <div class="p-0">
                        <table class="w-full text-left border-collapse">
                            <thead
                                class="bg-slate-50/80 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                                <tr>
                                    <th class="py-4 px-6 border-b border-slate-100 w-16 text-center">Pick</th>
                                    <th class="py-4 px-6 border-b border-slate-100 w-24">Variant</th>
                                    <th class="py-4 px-6 border-b border-slate-100">Rewritten Text</th>
                                    <th class="py-4 px-6 border-b border-slate-100 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="outputTableBody" class="text-sm text-slate-700 divide-y divide-slate-50">
                            </tbody>
                        </table>
                    </div>

                    <form id="saveForm" action="{{ route('save') }}" method="POST"
                        class="bg-indigo-50/30 px-6 py-4 border-t border-indigo-50 flex justify-end gap-3">
                        @csrf
                        <input type="hidden" name="original_text" id="formOriginal">
                        <input type="hidden" name="generated_text" id="formGenerated">
                        <input type="hidden" name="action" id="formAction">

                        <span class="text-xs text-slate-400 self-center mr-auto italic flex items-center gap-1.5">
                            <i data-lucide="info" class="w-3 h-3"></i> Select the version you want to keep.
                        </span>

                        <button type="submit" id="saveBtn"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold shadow-md shadow-indigo-200 hover:shadow-lg hover:translate-y-[-1px] transition-all flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Save Selected to Library
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Shorten Word Limit Modal -->
    <div id="shortenModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 w-80 shadow-xl">
            <h3 class="text-sm font-bold text-slate-800 mb-2">
                Shorten Text
            </h3>
            <p class="text-xs text-slate-500 mb-4">
                Enter maximum number of words
            </p>

            <input type="number" id="shortenLimit" min="1" max="500" value="20"
                class="w-full rounded-xl border-slate-200 text-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" />

            <div class="flex justify-end gap-2 mt-4">
                <button onclick="closeShortenModal()"
                    class="text-sm px-3 py-2 rounded-lg text-slate-500 hover:bg-slate-100">
                    Cancel
                </button>
                <button onclick="confirmShorten()"
                    class="text-sm px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                    Apply
                </button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        let currentOptions = []; // Store generated text options globally

        async function rewrite(action, wordLimit = null) {
            const text = document.getElementById('inputText').value;
            const resultArea = document.getElementById('resultArea');
            const loadingArea = document.getElementById('loadingArea');
            const tableBody = document.getElementById('outputTableBody');

            if (!text) return alert('Please enter some text to rewrite.');

            resultArea.classList.add('hidden');
            loadingArea.classList.remove('hidden');
            tableBody.innerHTML = '';
            currentOptions = []; // Reset options

            try {
                const response = await fetch('/rewrite', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        text,
                        action,
                        word_limit: wordLimit
                    })
                });

                const data = await response.json();
                loadingArea.classList.add('hidden');

                if (data.result) {
                    let aiData;
                    try {
                        aiData = JSON.parse(data.result);
                    } catch (e) {
                        aiData = {
                            options: [data.result]
                        };
                    }

                    currentOptions = aiData.options || ["No result generated"];

                    currentOptions.forEach((opt, index) => {
                        // Safe escaping for the copy button logic, not needed for save logic anymore
                        const escapedOpt = opt.replace(/`/g, "\\`").replace(/"/g, "&quot;");

                        const row = `
                            <tr class="group hover:bg-indigo-50/40 transition-colors cursor-pointer" onclick="selectOption(${index})">
                                <td class="py-5 px-6 align-top text-center">
                                    <div class="mt-1">
                                        <input type="radio" name="text_choice" id="opt_radio_${index}" 
                                            class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-600 cursor-pointer accent-indigo-600">
                                    </div>
                                </td>
                                <td class="py-5 px-6 font-semibold text-indigo-600 align-top text-xs pt-6">OPTION 0${index + 1}</td>
                                <td class="py-5 px-6 leading-relaxed text-slate-600 align-top">${opt}</td>
                                <td class="py-5 px-6 text-right align-top">
                                    <button onclick="event.stopPropagation(); copyText(\`${escapedOpt}\`)" class="text-slate-400 hover:text-indigo-600 p-2 rounded-lg hover:bg-white border border-transparent hover:border-slate-200 shadow-sm hover:shadow transition-all" title="Copy">
                                        <i data-lucide="copy" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });

                    // Update form metadata
                    document.getElementById('formOriginal').value = text;
                    document.getElementById('formAction').value = action;

                    // Select the first option by default
                    if (currentOptions.length > 0) {
                        selectOption(0);
                    }

                    resultArea.classList.remove('hidden');
                    lucide.createIcons();
                } else {
                    alert("Error: " + (data.error || "Unknown error"));
                }
            } catch (e) {
                console.error(e);
                loadingArea.classList.add('hidden');
                alert("Request failed. Check console for details.");
            }
        }

        // New function to handle selection
        function selectOption(index) {
            // 1. Visually check the radio button
            const radio = document.getElementById('opt_radio_' + index);
            if (radio) radio.checked = true;

            // 2. Update the hidden input for the Save Form
            document.getElementById('formGenerated').value = currentOptions[index];
        }

        function copyText(text) {
            navigator.clipboard.writeText(text);
        }

        function copyResult() {
            let allText = "";
            currentOptions.forEach((opt, i) => {
                allText += `Option ${i+1}:\n${opt}\n\n`;
            });
            copyText(allText);
            alert("All options copied!");
        }

        function openShortenModal() {
            document.getElementById('shortenModal').classList.remove('hidden');
            document.getElementById('shortenModal').classList.add('flex');
        }

        function closeShortenModal() {
            document.getElementById('shortenModal').classList.add('hidden');
            document.getElementById('shortenModal').classList.remove('flex');
        }

        function confirmShorten() {
            const limit = document.getElementById('shortenLimit').value;
            closeShortenModal();
            rewrite('shorten', limit);
        }
    </script>
</body>

</html>
