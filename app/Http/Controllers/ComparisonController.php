<?php

namespace App\Http\Controllers;

class ComparisonController extends Controller
{
    public function index()
    {
        // =========================
        // DEFAULT
        // =========================
        $naiveBayes = [

            'accuracy' => 0,
            'precision' => 0,
            'recall' => 0,
            'f1_score' => 0

        ];

        $svm = [

            'accuracy' => 0,
            'precision' => 0,
            'recall' => 0,
            'f1_score' => 0

        ];

        // =========================
        // FILE AKURASI
        // =========================
        $path = base_path('python-api/models/accuracy.json');

        // =========================
        // CEK FILE
        // =========================
        if (file_exists($path)) {

            $data = json_decode(
                file_get_contents($path),
                true
            );

            // =========================
            // NB
            // =========================
            $naiveBayes = [

                'accuracy' =>

                    $data['naive_bayes']['accuracy']
                    ?? 0,

                'precision' =>

                    $data['naive_bayes']['precision']
                    ?? 0,

                'recall' =>

                    $data['naive_bayes']['recall']
                    ?? 0,

                'f1_score' =>

                    $data['naive_bayes']['f1_score']
                    ?? 0

            ];

            // =========================
            // SVM
            // =========================
            $svm = [

                'accuracy' =>

                    $data['svm']['accuracy']
                    ?? 0,

                'precision' =>

                    $data['svm']['precision']
                    ?? 0,

                'recall' =>

                    $data['svm']['recall']
                    ?? 0,

                'f1_score' =>

                    $data['svm']['f1_score']
                    ?? 0

            ];
        }

        return view(
            'perbandingan',
            compact(
                'naiveBayes',
                'svm'
            )
        );
    }
}
