<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class DashboardController extends Controller
{
    public function index()
    {
        // =========================
        // DEFAULT
        // =========================
        $total = 0;

        $positif = 0;

        $negatif = 0;

        $netral = 0;

        // =========================
        // PATH DATASET
        // =========================
        $xlsx = base_path('python-api/dataset/dataset.xlsx');

        $xls = base_path('python-api/dataset/dataset.xls');

        $csv = base_path('python-api/dataset/dataset.csv');

        // =========================
        // LOAD XLSX
        // =========================
        if (file_exists($xlsx)) {

            $spreadsheet =
                IOFactory::load($xlsx);

            $sheet =
                $spreadsheet->getActiveSheet();

            $rows =
                $sheet->toArray();
        }

        // =========================
        // LOAD XLS
        // =========================
        elseif (file_exists($xls)) {

            $spreadsheet =
                IOFactory::load($xls);

            $sheet =
                $spreadsheet->getActiveSheet();

            $rows =
                $sheet->toArray();
        }

        // =========================
        // LOAD CSV
        // =========================
        elseif (file_exists($csv)) {

            $rows = array_map(
                'str_getcsv',
                file($csv)
            );
        }

        else {

            $rows = [];
        }

        // =========================
        // HAPUS HEADER
        // =========================
        if (!empty($rows)) {

            array_shift($rows);

            $total = count($rows);

            foreach ($rows as $row) {

                $rawLabel =
                    strtolower(
                        trim($row[2] ?? '')
                    );

                $label =
                    $this->normalizeLabel($rawLabel);

                if ($label == 'positif') {

                    $positif++;
                }

                elseif ($label == 'negatif') {

                    $negatif++;
                }

                elseif ($label == 'netral') {

                    $netral++;
                }
            }
        }

        // =========================
        // PERSENTASE
        // =========================
        $positifPercent =
            $total > 0
            ?
            ($positif / $total) * 100
            :
            0;

        $negatifPercent =
            $total > 0
            ?
            ($negatif / $total) * 100
            :
            0;

        $netralPercent =
            $total > 0
            ?
            ($netral / $total) * 100
            :
            0;

        // =========================
        // DEFAULT AKURASI
        // =========================
        $nbAccuracy = 0;

        $svmAccuracy = 0;

        // =========================
        // FILE AKURASI
        // =========================
        $accuracyPath = base_path('python-api/models/accuracy.json');

        // =========================
        // CEK FILE
        // =========================
        if (file_exists($accuracyPath)) {

            $accuracy = json_decode(
                file_get_contents($accuracyPath),
                true
            );

            // =========================
            // NAIVE BAYES
            // =========================
            $nbAccuracy = floatval(

                $accuracy['naive_bayes']['accuracy']
                ?? 0

            );

            // =========================
            // SVM
            // =========================
            $svmAccuracy = floatval(

                $accuracy['svm']['accuracy']
                ?? 0

            );
        }

        // =========================
        // SESSION MANUAL
        // =========================
        $manualUser =
            session('manualUser');

        $manualText =
            session('manualText');

        $nbResult =
            session('nbResult');

        $svmResult =
            session('svmResult');

        // =========================
        // RETURN
        // =========================
        return view(
            'dashboard',
            compact(

                'total',

                'positif',

                'negatif',

                'netral',

                'positifPercent',

                'negatifPercent',

                'netralPercent',

                'nbAccuracy',

                'svmAccuracy',

                'manualUser',

                'manualText',

                'nbResult',

                'svmResult'
            )
        );
    }

    // =========================
    // NORMALISASI LABEL
    // Menerima berbagai variasi format dari Flask API
    // (Inggris/Indonesia, singkatan, dsb) dan
    // mengembalikannya sebagai salah satu dari:
    // 'positif', 'negatif', 'netral', atau string aslinya
    // kalau tidak dikenali.
    // =========================
    private function normalizeLabel(string $label): string
    {
        $label = strtolower(trim($label));

        $positifVariants = [
            'positif', 'positive', 'pos', '1'
        ];

        $negatifVariants = [
            'negatif', 'negative', 'neg', '-1', '0'
        ];

        $netralVariants = [
            'netral', 'neutral', 'net'
        ];

        if (in_array($label, $positifVariants)) {
            return 'positif';
        }

        if (in_array($label, $negatifVariants)) {
            return 'negatif';
        }

        if (in_array($label, $netralVariants)) {
            return 'netral';
        }

        return $label;
    }
}
