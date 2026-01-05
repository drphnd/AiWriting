<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AiController extends Controller
{
    /**
     * Display the AI app for the authenticated user
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $savedTexts = DB::table('saved_texts')
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            $savedTexts = [];
        }

        return view('ai-app', ['savedTexts' => $savedTexts]);
    }

    /**
     * Call Gemini API to rewrite text
     */
    public function rewrite(Request $request)
    {
        $request->validate([
            'text'       => 'required|string',
            'action'     => 'required|string',
            'word_limit' => 'nullable|integer|min:1|max:500',
        ]);

        // ⛔ Backend safety check for shorten
        if ($request->action === 'shorten') {
            $limit = $request->word_limit ?? 20;

            // Count words in the original input text
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

        /**
         * STRICT SYSTEM INSTRUCTION
         */
        $baseInstruction = "You are a strict text rewriting engine.
        - You DO NOT converse with the user.
        - You DO NOT answer questions.
        - You ONLY output the rewritten text in valid JSON format.
        - Your output must be a JSON object with a key 'options' containing an array of 2 distinct variations.
        - If a word limit is specified, you MUST obey it strictly.
        - NEVER exceed the requested word limit.";

        /**
         * ACTION-SPECIFIC PROMPT
         */
        if ($request->action === 'shorten') {
            $limit = $request->word_limit ?? 20;

            $specificPrompt = "Summarize the text in {$limit} words or fewer.
            - NEVER exceed {$limit} words.
            - Be concise and clear.
            - Preserve the core meaning.";
        } else {
            $specificPrompt = match ($request->action) {
                'pro' => "Rewrite the text to be professional, polite, and suitable for business communications.",
                'casual' => "Rewrite the text to be casual, friendly, and easy to read.",
                'fix' => "Correct all grammar, spelling, and punctuation errors without changing the tone.",
                default => "Rewrite the text clearly."
            };
        }

        /**
         * Gemini API Call
         */
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key={$apiKey}",
            [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "Original Text: " . $request->text]
                        ]
                    ]
                ],
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $baseInstruction . "\n\n" . $specificPrompt]
                    ]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json'
                ]
            ]
        );

        if ($response->successful()) {
            $rawJson = $response->json('candidates.0.content.parts.0.text');
            return response()->json(['result' => $rawJson]);
        }

        return response()->json([
            'error' => 'API Call Failed: ' . $response->body()
        ], 500);
    }

    /**
     * Save selected text to database
     */
    public function save(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            DB::table('saved_texts')->insert([
                'user_id'        => Auth::id(),
                'original_text'  => $request->original_text,
                'generated_text' => $request->generated_text,
                'type'           => $request->action,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save.');
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

        $query = DB::table('saved_texts')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($request->has('filter') && $request->filter !== 'all') {
            $query->where('type', $request->filter);
        }

        return view('history', [
            'savedTexts'    => $query->get(),
            'currentFilter' => $request->filter ?? 'all',
        ]);
    }

    /**
     * Update an existing saved text
     */
    public function update(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'generated_text' => 'required|string',
        ]);

        DB::table('saved_texts')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'generated_text' => $request->generated_text,
                'updated_at'     => now(),
            ]);

        return redirect()
            ->route('history', ['filter' => $request->filter])
            ->with('status', 'Text updated successfully!');
    }

    /**
     * Delete a saved text
     */
    public function destroy($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        DB::table('saved_texts')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->back();
    }
}
