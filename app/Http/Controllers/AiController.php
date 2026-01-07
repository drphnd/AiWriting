<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Kreait\Firebase\Contract\Database;

class AiController extends Controller
{
    protected Database $database;
    protected string $tablename = 'saved_texts';

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $savedTexts = collect();

        try {
            $snapshot = $this->database
                ->getReference($this->tablename)
                ->orderByChild('user_id')
                ->equalTo(Auth::id())
                ->getSnapshot();

            $savedTexts = collect($snapshot->getValue() ?: [])
                ->map(function ($item, $key) {
                    $item['id'] = $key;
                    return (object) $item;
                })
                ->sortByDesc('created_at')
                ->values();
        } catch (\Exception $e) {
        }

        return view('ai-app', ['savedTexts' => $savedTexts]);
    }

    public function rewrite(Request $request)
    {
        $request->validate([
            'text'       => 'required|string',
            'action'     => 'required|string',
            'word_limit' => 'nullable|integer|min:1|max:500',
        ]);

        if ($request->action === 'shorten') {
            $limit = $request->word_limit ?? 20;
            $count = str_word_count(strip_tags($request->text));

            if ($limit >= $count) {
                return response()->json(['error' => 'Invalid word limit'], 422);
            }
        }

        $instruction = match ($request->action) {
            'pro'     => 'Rewrite professionally.',
            'casual'  => 'Rewrite casually.',
            'fix'     => 'Fix grammar only.',
            'shorten' => 'Summarize clearly.',
            default   => 'Rewrite clearly.',
        };

        if ($request->action === 'shorten') {
            $instruction .= " Limit to {$request->word_limit} words.";
        }

        $schema = [
            'type' => 'object',
            'properties' => [
                'options' => [
                    'type' => 'array',
                    'minItems' => 2,
                    'maxItems' => 2,
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'text' => ['type' => 'string'],
                        ],
                        'required' => ['text'],
                    ],
                ],
            ],
            'required' => ['options'],
        ];

        /** @var Response $response */
        $response = Http::post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . env('GEMINI_API_KEY'),
            [
                'contents' => [[
                    'role' => 'user',
                    'parts' => [['text' => $request->text]],
                ]],
                'systemInstruction' => [
                    'parts' => [['text' => $instruction]],
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                    'response_schema' => $schema,
                ],
            ]
        );

        if (!$response->successful()) {
            return response()->json(['error' => 'Gemini error'], 500);
        }

        return response()->json([
            'result' => json_decode(
                $response->json('candidates.0.content.parts.0.text'),
                true
            ),
        ]);
    }

    public function save(Request $request)
    {
        $this->database->getReference($this->tablename)->push([
            'user_id'        => Auth::id(),
            'original_text'  => $request->original_text,
            'generated_text' => $request->generated_text,
            'type'           => $request->action,
            'created_at'     => now()->toIso8601String(),
        ]);

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

        $snapshot = $this->database
            ->getReference($this->tablename)
            ->orderByChild('user_id')
            ->equalTo(Auth::id())
            ->getSnapshot();

        $value = $snapshot->getValue();

        $savedTexts = collect($value ?: [])
            ->map(function ($item, $key) {
                $item['id'] = $key;
                return (object) $item;
            })
            ->sortByDesc('created_at');

        if ($request->filter && $request->filter !== 'all') {
            $savedTexts = $savedTexts->where('type', $request->filter);
        }

        return view('history', [
            'savedTexts'    => $savedTexts->values(),
            'currentFilter' => $request->filter ?? 'all',
        ]);
    }

    /**
     * Update a saved text
     */
    public function update(Request $request, string $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'generated_text' => 'required|string',
        ]);

        $this->database
            ->getReference("{$this->tablename}/{$id}")
            ->update([
                'generated_text' => $request->generated_text,
                'updated_at'     => now()->toIso8601String(),
            ]);

        return redirect()
            ->route('history', ['filter' => $request->filter])
            ->with('status', 'Text updated successfully!');
    }

    /**
     * Delete a saved text
     */
    public function destroy(string $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->database
            ->getReference("{$this->tablename}/{$id}")
            ->remove();

        return redirect()->back();
    }
}
