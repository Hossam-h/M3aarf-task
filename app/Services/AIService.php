<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AIService
 *
 * Handles communication with the OpenAI API to generate
 * educational course title suggestions for given categories.
 */
class AIService
{
    /**
     * OpenAI API endpoint.
     */
    private const API_URL = 'https://api.openai.com/v1/chat/completions';

    /**
     * The OpenAI API key.
     */
    private string $apiKey;

    /**
     * Create a new AIService instance.
     */
    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', '');
    }

    /**
     * Generate educational course title search queries for a given category.
     *
     * Calls the OpenAI Chat Completions API with a prompt to generate
     * unique YouTube playlist search queries. Returns an array of title strings.
     *
     * @param string $category The educational category to generate titles for
     * @return array Array of course title strings
     */
    public function generateCourseTitles(string $category): array
    {
        try {
            // Validate API key is configured
            if (empty($this->apiKey)) {
                Log::error('AIService: OpenAI API key is not configured.');
                return $this->getFallbackTitles($category);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(30)->post(self::API_URL, [
                'model'    => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => 'You are a helpful assistant that generates educational YouTube search queries. Always respond with a valid JSON array of strings only.',
                    ],
                    [
                        'role'    => 'user',
                        'content' => "Generate 10 unique educational YouTube playlist search queries for the category: {$category}. Return only a JSON array of strings, no explanation.",
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens'  => 500,
            ]);

            // Check for successful response
            if ($response->failed()) {
                Log::error('AIService: OpenAI API request failed.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return $this->getFallbackTitles($category);
            }

            $body = $response->json();

            // Extract the content from the response
            $content = $body['choices'][0]['message']['content'] ?? '';

            // Parse JSON array from the response
            $titles = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($titles)) {
                Log::warning('AIService: Failed to parse OpenAI response as JSON.', [
                    'content' => $content,
                ]);
                return $this->getFallbackTitles($category);
            }

            // Filter to only string values and limit to 20
            $titles = array_filter($titles, 'is_string');
            $titles = array_slice(array_values($titles), 0, 20);

            Log::info("AIService: Generated " . count($titles) . " titles for category: {$category}");

            return $titles;

        } catch (\Exception $e) {
            Log::error('AIService: Exception occurred while generating course titles.', [
                'category' => $category,
                'error'    => $e->getMessage(),
            ]);
            return $this->getFallbackTitles($category);
        }
    }

    /**
     * Provide fallback course titles when the API is unavailable.
     *
     * Generates basic search queries using the category name
     * so the app can still function without an API key.
     *
     * @param string $category
     * @return array
     */
    private function getFallbackTitles(string $category): array
    {
        return [
            "{$category} complete course",
            "{$category} tutorial for beginners",
            "{$category} advanced course",
            "learn {$category} step by step",
            "{$category} full course playlist",
            "{$category} crash course",
            "{$category} masterclass",
            "{$category} project tutorial",
            "best {$category} course",
            "{$category} bootcamp",
        ];
    }
}
