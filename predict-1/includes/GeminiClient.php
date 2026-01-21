<?php

class GeminiClient {
    private $apiKey;
    private $baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/";

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    private function callApi($model, $payload, $stream = false) {
        $endpoint = $this->baseUrl . $model . ":generateContent?key=" . $this->apiKey;

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            throw new Exception("CURL Error: " . $err);
        }

        return json_decode($response, true);
    }

    public function getTeamSuggestions($input) {
        $season = getCurrentSeasonRange();
        $model = "gemini-1.5-flash-latest"; // Using a stable model

        $payload = [
            "contents" => [[
                "parts" => [[
                    "text" => "Search for professional football teams matching \"$input\".
                    DATA ACCURACY: Return the league the team is playing in for the $season season.
                    CRITICAL: For $season, verify promotion/relegation.
                    Example: If a team was promoted to the Premier League for $season, list 'Premier League'.
                    Return 5 results as JSON."
                ]]
            ]],
            "generationConfig" => [
                "responseMimeType" => "application/json",
                "responseSchema" => [
                    "type" => "ARRAY",
                    "items" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "name" => ["type" => "STRING"],
                            "league" => ["type" => "STRING"],
                            "country" => ["type" => "STRING"]
                        ],
                        "required" => ["name", "league", "country"]
                    ]
                ]
            ]
        ];

        $response = $this->callApi($model, $payload);

        if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
            return json_decode($response['candidates'][0]['content']['parts'][0]['text'], true);
        }

        return [];
    }

    public function getPrediction($home, $away) {
        $season = getCurrentSeasonRange();
        $model = "gemini-1.5-pro-latest";

        $payload = [
            "contents" => [[
                "parts" => [[
                    "text" => "Perform a deep tactical analysis for $home (Home) vs $away (Away) for the $season season.
                    Verify their $season league membership and latest squad transfers.

                    CRITICAL PROBABILITY GUIDELINES:
                    - Provide a probability percentage from 1 to 100.
                    - 50% means a coin flip.
                    - 70-80% is high confidence.
                    - 90%+ is near certain.
                    - Use whole numbers for clarity.

                    Provide:
                    1. Main outcome (1X2)
                    2. Over/Under 2.5 goals prediction
                    3. Probabilities for both (1-100)
                    4. Detailed reasoning including tactical shifts for the $season campaign
                    5. Expected final score
                    6. Key match stats for $season."
                ]]
            ]],
            "generationConfig" => [
                "responseMimeType" => "application/json",
                "responseSchema" => [
                    "type" => "OBJECT",
                    "properties" => [
                        "mainPrediction" => ["type" => "STRING"],
                        "mainProbability" => ["type" => "NUMBER"],
                        "overUnderPrediction" => ["type" => "STRING"],
                        "overUnderProbability" => ["type" => "NUMBER"],
                        "reasoning" => ["type" => "STRING"],
                        "expectedScore" => ["type" => "STRING"],
                        "keyStats" => [
                            "type" => "ARRAY",
                            "items" => ["type" => "STRING"]
                        ]
                    ],
                    "required" => ["mainPrediction", "mainProbability", "overUnderPrediction", "overUnderProbability", "reasoning", "expectedScore", "keyStats"]
                ]
            ]
        ];

        $response = $this->callApi($model, $payload);

        if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
            return json_decode($response['candidates'][0]['content']['parts'][0]['text'], true);
        }

        throw new Exception("Failed to generate prediction.");
    }
}
?>
