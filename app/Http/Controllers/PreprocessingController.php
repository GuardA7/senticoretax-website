<?php

namespace App\Http\Controllers;

class PreprocessingController extends Controller
{
    public function index()
    {
        // =========================
        // PATH JSON
        // =========================
        $path = base_path('python-api/dataset/preprocessing_result.json');

        $results = [];

        // =========================
        // CEK FILE
        // =========================
        if (file_exists($path)) {

            $json = file_get_contents($path);

            $results = json_decode(
                $json,
                true
            ) ?? [];

            // =========================
            // BATASI DATA
            // =========================
            $results = array_slice(
                $results,
                0,
                100
            );
        }

        return view(
            'preprocessing',
            compact('results')
        );
    }
}
