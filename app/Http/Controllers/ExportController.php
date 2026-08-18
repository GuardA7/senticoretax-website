<?php

namespace App\Http\Controllers;

use App\Services\FlaskApiService;

class ExportController extends Controller
{
    public function laporan(
        FlaskApiService $flask
    ) {

        // =========================
        // FILE PREPROCESSING
        // =========================
        $path = base_path('python-api/dataset/preprocessing_result.json');

        // =========================
        // CEK FILE
        // =========================
        if (!file_exists($path)) {

            return back()->with(
                'error',
                'Data preprocessing tidak ditemukan'
            );
        }

        // =========================
        // LOAD JSON
        // =========================
        $json = json_decode(
            file_get_contents($path),
            true
        );

        // =========================
        // HEADER EXPORT
        // =========================
        $filename =
            'laporan_sentimen.csv';

        header(
            'Content-Type: text/csv'
        );

        header(
            'Content-Disposition: attachment; filename="' . $filename . '"'
        );

        // =========================
        // OUTPUT CSV
        // =========================
        $output =
            fopen('php://output', 'w');

        // =========================
        // HEADER TABLE
        // =========================
        fputcsv($output, [

            'Username',

            'Original',

            'Cleaning',

            'Tokenizing',

            'Stopword Removal',

            'Stemming',

            'Final Preprocessing',

            'Naive Bayes',

            'SVM'

        ]);

        // =========================
        // BATASI EXPORT
        // =========================
        foreach (
            array_slice($json, 0, 500)
            as $row
        ) {

            $final =
                $row['final'] ?? '';

            // =========================
            // PREDIKSI NB
            // =========================
            $nb =
                $flask->predictNB(
                    $final
                );

            // =========================
            // PREDIKSI SVM
            // =========================
            $svm =
                $flask->predictSVM(
                    $final
                );

            // =========================
            // EXPORT ROW
            // =========================
            fputcsv($output, [

                $row['username']
                    ?? '',

                $row['content']
                    ?? '',

                $row['cleaning']
                    ?? '',

                $row['tokenizing']
                    ?? '',

                $row['stopword']
                    ?? '',

                $row['stemming']
                    ?? '',

                $row['final']
                    ?? '',

                $nb['result']
                    ?? '',

                $svm['result']
                    ?? ''

            ]);
        }

        fclose($output);

        exit;
    }
}
