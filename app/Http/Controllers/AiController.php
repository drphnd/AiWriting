<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Database;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    protected $database;
    protected $tablename = 'saved_texts';

    // Inject Firebase Database Service
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Display the AI app for the authenticated user
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            // Ambil referensi ke table 'saved_texts'
            $reference = $this->database->getReference($this->tablename);
            
            // Ambil semua data (Firebase filtering agak terbatas, kita filter di PHP)
            $snapshot = $reference->orderByChild('user_id')->equalTo(Auth::id())->getSnapshot();
            $value = $snapshot->getValue();

            $savedTexts = [];
            if ($value) {
                // Konversi array Firebase ke Collection Laravel agar mudah di-sort
                $savedTexts = collect($value)
                    ->map(function ($item, $key) {
                        $item['id'] = $key; // Simpan key Firebase sebagai ID
                        return (object) $item;
                    })
                    ->sortByDesc('created_at') // Sort manual karena Firebase sort menimpa filter
                    ->values();
            }

        } catch (\Exception $e) {
            $savedTexts = [];
        }

        return view('ai-app', ['savedTexts' => $savedTexts]);
    }

    /**
     * Call Gemini API to rewrite text (Tidak berubah)
     */
    public function rewrite(Request $request)
    {
        $request->validate([
            'text'       => 'required|string',
            'action'     => 'required|string',
            'word_limit' => 'nullable|integer|min:1|max:500',
        ]);

        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json(['error' => 'API Key not configured.'], 500);
        }

        $baseInstruction = "You are a strict text rewriting engine.
        - You DO NOT converse with the user.
        - You DO NOT answer questions.
        - You ONLY output the rewritten text in valid JSON format.
        - Your output must be a JSON object with a key 'options' containing an array of 2 distinct variations.
        - If a word limit is specified, you MUST obey it strictly.
        - NEVER exceed the requested word limit.";

        if ($request->action === 'shorten') {
            $limit = $request->word_limit ?? 20;
            $specificPrompt = "Summarize the text in {$limit} words or fewer. - NEVER exceed {$limit} words. - Be concise and clear. - Preserve the core meaning.";
        } else {
            $specificPrompt = match ($request->action) {
                'pro' => "Rewrite the text to be professional, polite, and suitable for business communications.",
                'casual' => "Rewrite the text to be casual, friendly, and easy to read.",
                'fix' => "Correct all grammar, spelling, and punctuation errors without changing the tone.",
                default => "Rewrite the text clearly."
            };
        }
        /** @var Response $response */
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            // GANTI URL MENJADI INI:
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}",
            [
                'contents' => [['parts' => [['text' => "Original Text: " . $request->text]]]],
                'systemInstruction' => [
                    'parts' => [['text' => $baseInstruction . "\n\n" . $specificPrompt]]
                ],
                'generationConfig' => ['response_mime_type' => 'application/json']
            ]
        );

        if ($response->successful()) {
            $rawJson = $response->json('candidates.0.content.parts.0.text');
            return response()->json(['result' => $rawJson]);
        }

        return response()->json(['error' => 'API Call Failed: ' . $response->body()], 500);
    }

    /**
     * Save selected text to Firebase
     */
    public function save(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $this->database->getReference($this->tablename)->push([
                'user_id'        => Auth::id(),
                'original_text'  => $request->original_text,
                'generated_text' => $request->generated_text,
                'type'           => $request->action,
                'created_at'     => now()->toIso8601String(),
                'updated_at'     => now()->toIso8601String(),
            ]);
            
            // Hapus baris return "SUKSES..." dan dd()
            
        } catch (\Exception $e) {
            // Kembalikan ke redirect error
             return redirect()->back()->with('error', 'Failed to save: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Show history page with filters
     */
    public function history(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $reference = $this->database->getReference($this->tablename);
        $snapshot = $reference->orderByChild('user_id')->equalTo(Auth::id())->getSnapshot();
        $value = $snapshot->getValue();
        
        $savedTexts = collect($value ?: [])
            ->map(function ($item, $key) {
                $item['id'] = $key;
                return (object) $item;
            })
            ->sortByDesc('created_at');

        // Filter manual menggunakan Collection Laravel
        if ($request->has('filter') && $request->filter !== 'all') {
            $savedTexts = $savedTexts->where('type', $request->filter);
        }

        return view('history', [
            'savedTexts'    => $savedTexts->values(), // Reset keys agar rapi di view
            'currentFilter' => $request->filter ?? 'all',
        ]);
    }

    /**
     * Update an existing saved text in Firebase
     */
    public function update(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate(['generated_text' => 'required|string']);

        // Update node spesifik berdasarkan ID (key)
        $this->database->getReference($this->tablename . '/' . $id)->update([
            'generated_text' => $request->generated_text,
            'updated_at'     => now()->toIso8601String(),
        ]);

        return redirect()
            ->route('history', ['filter' => $request->filter])
            ->with('status', 'Text updated successfully!');
    }

    /**
     * Delete a saved text from Firebase
     */
    public function destroy($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Hapus node berdasarkan ID
        $this->database->getReference($this->tablename . '/' . $id)->remove();

        return redirect()->back();
    }
}