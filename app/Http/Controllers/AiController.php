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

        if ($request->action === 'shorten') {
            $limit = $request->word_limit ?? 20;
            $originalWordCount = str_word_count(strip_tags($request->text));

            if ($limit >= $originalWordCount) {
                return response()->json([
                    'error' => "Word limit ({$limit}) must be less than original text length ({$originalWordCount})."
                ], 422);
            }
        }

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'API Key not configured.'], 500);
        }

        $limit = $request->word_limit ?? null;

        $prompt = match ($request->action) {
            'pro'     => 'Rewrite the following text in a professional and polite business tone.',
            'casual'  => 'Rewrite the following text in a casual and friendly tone.',
            'fix'     => 'Fix grammar and spelling errors without changing tone.',
            'shorten' => "Rewrite the following text to {$limit} words or fewer.",
            default   => 'Rewrite the following text clearly.'
        };

        $responseSchema = [
            'type' => 'object',
            'properties' => [
                'options' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'minItems' => 2,
                    'maxItems' => 2,
                ],
            ],
            'required' => ['options'],
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
            [
                'contents' => [[
                    'role' => 'user',
                    'parts' => [[
                        'text' => "{$prompt}\n\nText:\n{$request->text}"
                    ]]
                ]],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'responseJsonSchema' => $responseSchema
                ]
            ]
        );

        if (!$response->successful()) {
            return response()->json([
                'error' => 'API call failed',
                'details' => $response->body()
            ], 500);
        }

        $result = $response->json('candidates.0.content.parts.0');

        if (isset($result['text'])) {
            $result = json_decode($result['text'], true);
        }

        return response()->json([
            'result' => $result
        ]);
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
