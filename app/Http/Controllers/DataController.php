<?php

namespace App\Http\Controllers;

class DataController extends Controller
{
    // =========================
    // CLEAR DATA
    // =========================
    public function clear()
    {
        $files = [

            // =========================
            // HASIL ANALISIS
            // =========================
            storage_path('app/public/preprocessing.json'),
            storage_path('app/public/nb_results.json'),
            storage_path('app/public/svm_results.json'),
            storage_path('app/public/evaluation.json'),
            storage_path('app/public/comparison.json'),
            storage_path('app/public/eucs.json'),

            // =========================
            // AKURASI MODEL
            // (dibaca DashboardController untuk nbAccuracy & svmAccuracy)
            // =========================
            base_path('python-api/models/accuracy.json'),

            // =========================
            // DATASET
            // Path disamakan persis dengan yang dibaca DashboardController,
            // dan mencakup ketiga kemungkinan format (xlsx, xls, csv)
            // =========================
            base_path('python-api/dataset/dataset.xlsx'),
            base_path('python-api/dataset/dataset.xls'),
            base_path('python-api/dataset/dataset.csv'),

            // =========================
            // MODEL AI
            // =========================
            base_path('python-api/models/nb_model.pkl'),
            base_path('python-api/models/svm_model.pkl'),

        ];

        foreach ($files as $file) {

            if (file_exists($file)) {

                unlink($file);

            }
        }

        // =========================
        // BERSIHKAN SESSION
        // Dashboard juga menampilkan data dari session
        // (input manual, hasil NB/SVM), jadi harus ikut direset
        // =========================
        session()->forget([
            'manualUser',
            'manualText',
            'nbResult',
            'svmResult',
        ]);

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Semua data dan model berhasil dihapus'
            );
    }
}
