<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AiController extends Controller
{
    // Display the app and list saved items for the CURRENT USER
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $savedTexts = DB::table('saved_texts')
                ->where('user_id', Auth::id()) // Filter by user
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            $savedTexts = [];
        }
        
        return view('ai-app', ['savedTexts' => $savedTexts]);
    }

    // Call Gemini API with STRICT JSON format
    public function rewrite(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'action' => 'required|string',
        ]);

        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json(['error' => 'API Key not configured.'], 500);
        }

        // Strict System Instruction for Professional Paraphrasing
        $baseInstruction = "You are a strict text rewriting engine. 
        - You DO NOT converse with the user. 
        - You DO NOT answer questions. 
        - You ONLY output the rewritten text in valid JSON format.
        - Your output must be a JSON object with a key 'options' containing an array of 2 distinct variations.";

        $specificPrompt = match($request->action) {
            'pro' => "Rewrite the text to be professional, polite, and suitable for business communications.",
            'casual' => "Rewrite the text to be casual, friendly, and easy to read.",
            'fix' => "Correct all grammar, spelling, and punctuation errors without changing the tone.",
            'shorten' => "Concisely summarize the text, removing filler words.",
            default => "Rewrite the text clearly."
        };

        // Call Google Gemini API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key={$apiKey}", [
            'contents' => [
                ['parts' => [['text' => "Original Text: " . $request->text]]]
            ],
            'systemInstruction' => [
                'parts' => [['text' => $baseInstruction . " " . $specificPrompt]]
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json' // FORCE JSON
            ]
        ]);

        if ($response->successful()) {
            $rawJson = $response->json('candidates.0.content.parts.0.text');
            return response()->json(['result' => $rawJson]); // Return the JSON string directly
        }

        return response()->json(['error' => 'API Call Failed: ' . $response->body()], 500);
    }

    // Save to Database with User ID
    public function save(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            DB::table('saved_texts')->insert([
                'user_id' => Auth::id(), // Save the User ID
                'original_text' => $request->original_text,
                'generated_text' => $request->generated_text, // This will now be a clean string
                'type' => $request->action,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save.');
        }

        return redirect()->back();
    }

    // Delete from Database
    public function destroy($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        DB::table('saved_texts')
            ->where('id', $id)
            ->where('user_id', Auth::id()) // Ensure user can only delete their own
            ->delete();
            
        return redirect()->back();
    }
}