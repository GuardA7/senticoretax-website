<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls,txt|max:15360'
        ], [
            'file.max' => 'Ukuran file tidak boleh lebih dari 15 MB.'
        ]);

        $file = $request->file('file');

        // =========================
        // FOLDER DATASET PYTHON
        // =========================
        $destination = base_path('python-api/dataset');

        // =========================
        // BUAT FOLDER
        // =========================
        if (!file_exists($destination)) {

            mkdir(
                $destination,
                0777,
                true
            );
        }

        // =========================
        // HAPUS DATASET LAMA
        // =========================
        $oldFiles = [

            $destination . '/dataset.csv',
            $destination . '/dataset.xlsx',
            $destination . '/dataset.xls'

        ];

        foreach ($oldFiles as $oldFile) {

            if (file_exists($oldFile)) {

                unlink($oldFile);

            }
        }

        // =========================
        // HAPUS HASIL PREPROCESSING
        // =========================
        $preprocessFile =
            $destination .
            '/preprocessing_result.csv';

        if (file_exists($preprocessFile)) {

            unlink($preprocessFile);

        }

        // =========================
        // EXTENSION FILE
        // =========================
        $extension =
            $file->getClientOriginalExtension();

        // =========================
        // SIMPAN FILE ASLI
        // =========================
        $file->move(
            $destination,
            'dataset.' . $extension
        );

        // =========================
        // AUTO PREPROCESSING
        // =========================
        $pythonScript = escapeshellarg(
            base_path('python-api/preprocess_dataset.py')
        );

        $command = PHP_OS_FAMILY === 'Windows'
            ? 'start /B python ' . $pythonScript
            : 'python3 ' . $pythonScript . ' > /dev/null 2>&1 &';

        pclose(popen($command, 'r'));

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Dataset berhasil diupload.'
            );
    }
}
