<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use RuntimeException;

class FlaskApiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            config('services.flask_api.url', 'http://127.0.0.1:5000'),
            '/'
        );
    }

    // =========================
    // NAIVE BAYES
    // =========================
    public function predictNB($text)
    {
        return $this->post(
            $this->baseUrl . '/predict/nb',
            $text
        );
    }

    // =========================
    // SVM
    // =========================
    public function predictSVM($text)
    {
        return $this->post(
            $this->baseUrl . '/predict/svm',
            $text
        );
    }

    public function predictNBBatch(array $texts): array
    {
        return $this->postBatch(
            $this->baseUrl . '/predict/nb/batch',
            $texts
        );
    }

    public function predictSVMBatch(array $texts): array
    {
        return $this->postBatch(
            $this->baseUrl . '/predict/svm/batch',
            $texts
        );
    }

    private function post(string $url, string $text): array
    {
        $response = Http::acceptJson()
            ->withOptions([
                'verify' => filter_var(
                    config('services.flask_api.verify_ssl', true),
                    FILTER_VALIDATE_BOOLEAN
                )
            ])
            ->timeout(30)
            ->post($url, ['content' => $text]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Flask API returned HTTP ' . $response->status() . ': ' . $response->body()
            );
        }

        return $response->json() ?? [];
    }

    private function postBatch(string $url, array $texts): array
    {
        $client = Http::acceptJson()
            ->withOptions([
                'verify' => filter_var(
                    config('services.flask_api.verify_ssl', true),
                    FILTER_VALIDATE_BOOLEAN
                )
            ])
            ->timeout(30);

        $response = $client->post($url, ['contents' => array_values($texts)]);

        if ($response->status() === 404) {
            return $this->postBatchFallback($client, $url, $texts);
        }

        if ($response->failed()) {
            throw new RuntimeException(
                'Flask API returned HTTP ' . $response->status() . ': ' . $response->body()
            );
        }

        return $response->json('results', []);
    }

    private function postBatchFallback($client, string $batchUrl, array $texts): array
    {
        $singleUrl = str_replace('/batch', '', $batchUrl);

        $responses = Http::pool(function (Pool $pool) use ($client, $singleUrl, $texts) {
            return array_map(
                fn ($text) => $pool
                    ->withOptions($client->getOptions())
                    ->acceptJson()
                    ->timeout(30)
                    ->post($singleUrl, ['content' => $text]),
                array_values($texts)
            );
        });

        $results = [];

        foreach ($responses as $response) {
            if ($response->failed()) {
                throw new RuntimeException(
                    'Flask API returned HTTP ' . $response->status() . ': ' . $response->body()
                );
            }

            $results[] = $response->json('result');
        }

        return $results;
    }
}
