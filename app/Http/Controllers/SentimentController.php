<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\FlaskApiService;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SentimentController extends Controller
{
    // =========================
    // PATH DATASET
    // (disamakan dengan yang dibaca DashboardController)
    // =========================
    private $datasetPath =
        '';

    public function __construct()
    {
        $this->datasetPath = base_path('python-api/dataset/dataset.xlsx');
    }

    // =========================
    // NAIVE BAYES
    // =========================
    public function naiveBayes(
        FlaskApiService $flask
    ) {

        $accuracy = 0;

        $results = [];

        $accuracyPath = base_path('python-api/models/accuracy.json');

        if (file_exists($accuracyPath)) {

            $data = json_decode(
                file_get_contents($accuracyPath),
                true
            );

            $accuracy = floatval(
                $data['naive_bayes']['accuracy'] ?? 0
            );
        }

        if (file_exists($this->datasetPath)) {

            $spreadsheet =
                IOFactory::load($this->datasetPath);

            $sheet =
                $spreadsheet->getActiveSheet();

            $rows =
                $sheet->toArray();

            array_shift($rows);

            $sampleRows = array_slice($rows, 0, 100);
            $contents = array_map(fn ($row) => $row[1] ?? '', $sampleRows);
            $predictions = $flask->predictNBBatch($contents);

            foreach ($contents as $index => $content) {
                $results[] = [
                    'content' => $content,
                    'result' => $predictions[$index] ?? null
                ];
            }
        }

        return view(
            'klasifikasi_nb',
            compact('accuracy', 'results')
        );
    }

    // =========================
    // SVM
    // =========================
    public function svm(
        FlaskApiService $flask
    ) {

        $accuracy = 0;

        $results = [];

        $accuracyPath = base_path('python-api/models/accuracy.json');

        if (file_exists($accuracyPath)) {

            $data = json_decode(
                file_get_contents($accuracyPath),
                true
            );

            $accuracy = floatval(
                $data['svm']['accuracy'] ?? 0
            );
        }

        if (file_exists($this->datasetPath)) {

            $spreadsheet =
                IOFactory::load($this->datasetPath);

            $sheet =
                $spreadsheet->getActiveSheet();

            $rows =
                $sheet->toArray();

            array_shift($rows);

            $sampleRows = array_slice($rows, 0, 100);
            $contents = array_map(fn ($row) => $row[1] ?? '', $sampleRows);
            $predictions = $flask->predictSVMBatch($contents);

            foreach ($contents as $index => $content) {
                $results[] = [
                    'content' => $content,
                    'result' => $predictions[$index] ?? null
                ];
            }
        }

        return view(
            'klasifikasi_svm',
            compact('accuracy', 'results')
        );
    }

    // =========================
    // INPUT MANUAL
    // =========================
    public function manualInput(
        Request $request,
        FlaskApiService $flask
    ) {

        $request->validate([
            'content' => 'required|string|max:150'
        ], [
            'content.max' => 'Ulasan tidak boleh lebih dari 150 karakter.'
        ]);

        $content = $request->content;

        try {

            $nb = $flask->predictNB($content);

            $svm = $flask->predictSVM($content);

        } catch (\Exception $e) {

            report($e);

            // =========================
            // RESPON UNTUK REQUEST AJAX
            // =========================
            if ($request->wantsJson()) {

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses analisis sentimen. Pastikan Flask API aktif.'
                ], 500);
            }

            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Gagal memproses analisis sentimen. Pastikan Flask API aktif.'
                );
        }

        $nbResult = $nb['result'] ?? null;

        $svmResult = $svm['result'] ?? null;

        // =========================
        // SIMPAN KE SESSION
        // (untuk card "Hasil Analisis Manual" di dashboard)
        // =========================
        session([
            'manualText' => $content,
            'nbResult' => $nbResult,
            'svmResult' => $svmResult
        ]);

        // =========================
        // TULIS KE DATASET.XLSX
        // (supaya ikut terhitung di statistik Dashboard)
        // 2 baris terpisah: satu untuk hasil NB, satu untuk hasil SVM
        // =========================
        $this->appendToDataset(
            'Manual (NB)',
            $content,
            $nbResult
        );

        $this->appendToDataset(
            'Manual (SVM)',
            $content,
            $svmResult
        );

        // =========================
        // RESPON UNTUK REQUEST AJAX
        // Hasil langsung dikembalikan tanpa redirect,
        // supaya bisa ditampilkan langsung di modal
        // =========================
        if ($request->wantsJson()) {

            return response()->json([
                'success' => true,
                'content' => $content,
                'nbResult' => $nbResult,
                'svmResult' => $svmResult
            ]);
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Analisis sentimen berhasil diproses.'
            );
    }

    // =========================
    // HELPER: APPEND BARIS BARU KE DATASET.XLSX
    // =========================
    private function appendToDataset(
        string $username,
        string $content,
        ?string $label
    ) {

        // =========================
        // LABEL WAJIB ADA
        // =========================
        if (empty($label)) {
            return;
        }

        $label = strtolower(trim($label));

        // =========================
        // LOAD ATAU BUAT BARU
        // =========================
        if (file_exists($this->datasetPath)) {

            $spreadsheet =
                IOFactory::load($this->datasetPath);

            $sheet =
                $spreadsheet->getActiveSheet();

        } else {

            $spreadsheet = new Spreadsheet();

            $sheet =
                $spreadsheet->getActiveSheet();

            // =========================
            // HEADER BARU
            // (hanya dibuat kalau file belum ada sama sekali)
            // =========================
            $sheet->fromArray(
                ['username', 'content', 'label'],
                null,
                'A1'
            );
        }

        // =========================
        // CARI BARIS KOSONG BERIKUTNYA
        // =========================
        $nextRow =
            $sheet->getHighestRow() + 1;

        $sheet->setCellValue('A' . $nextRow, $username);
        $sheet->setCellValue('B' . $nextRow, $content);
        $sheet->setCellValue('C' . $nextRow, $label);

        // =========================
        // PASTIKAN FOLDER TUJUAN ADA
        // =========================
        $dir = dirname($this->datasetPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $writer =
            IOFactory::createWriter($spreadsheet, 'Xlsx');

        $writer->save($this->datasetPath);
    }
}