@extends('layouts.app')

@section('title', 'Analisis Kepuasan')

@section('content')

<h2 class="text-3xl font-bold mb-2 text-slate-800">
    Analisis Kepuasan
</h2>

<p class="text-slate-500 mb-6">
    Evaluasi kepuasan pengguna aplikasi Coretax
    berdasarkan metode End User Computing Satisfaction.
</p>

{{-- =========================
UPLOAD
========================= --}}
<div class="bg-white border border-blue-100 rounded-2xl p-5 mb-6 premium-shadow">

    <form method="POST"
          action="{{ route('upload.eucs') }}"
          enctype="multipart/form-data"
          class="flex flex-col md:flex-row gap-4 items-center">

        @csrf

        <input type="file"
               name="file"
               required
               class="text-sm text-slate-600">

        <button type="submit"
                class="bg-blue-700 hover:bg-blue-800 text-white transition px-5 py-2 rounded-xl font-semibold">

            Upload Kuesioner

        </button>

    </form>

</div>

{{-- =========================
KETERANGAN KATEGORI
========================= --}}
<div class="bg-white border border-blue-100 rounded-2xl p-5 mb-6 premium-shadow">

    <h3 class="text-sm font-semibold text-slate-600 mb-3">
        Keterangan Kategori Skor Rata-rata
    </h3>

    <div class="grid grid-cols-1 min-[420px]:grid-cols-2 md:grid-cols-5 gap-3">

        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-red-500 shrink-0"></span>
            <span class="text-xs text-slate-500">
                1,00 – 1,80<br>
                <span class="font-semibold text-slate-800">Sangat Tidak Puas</span>
            </span>
        </div>

        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-orange-500 shrink-0"></span>
            <span class="text-xs text-slate-500">
                1,81 – 2,60<br>
                <span class="font-semibold text-slate-800">Tidak Puas</span>
            </span>
        </div>

        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-amber-400 shrink-0"></span>
            <span class="text-xs text-slate-500">
                2,61 – 3,40<br>
                <span class="font-semibold text-slate-800">Cukup Puas</span>
            </span>
        </div>

        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-blue-500 shrink-0"></span>
            <span class="text-xs text-slate-500">
                3,41 – 4,20<br>
                <span class="font-semibold text-slate-800">Puas</span>
            </span>
        </div>

        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-blue-700 shrink-0"></span>
            <span class="text-xs text-slate-500">
                4,21 – 5,00<br>
                <span class="font-semibold text-slate-800">Sangat Puas</span>
            </span>
        </div>

    </div>

</div>

@if(isset($average))

@php

    function kategori($nilai)
    {
        if ($nilai >= 4.21) {
            return 'Sangat Puas';
        }
        elseif ($nilai >= 3.41) {
            return 'Puas';
        }
        elseif ($nilai >= 2.61) {
            return 'Cukup Puas';
        }
        elseif ($nilai >= 1.81) {
            return 'Tidak Puas';
        }

        return 'Sangat Tidak Puas';
    }

    function warna($nilai)
    {
        if ($nilai >= 4.21) {
            return 'bg-blue-700';
        }
        elseif ($nilai >= 3.41) {
            return 'bg-blue-500';
        }
        elseif ($nilai >= 2.61) {
            return 'bg-amber-400';
        }
        elseif ($nilai >= 1.81) {
            return 'bg-orange-500';
        }

        return 'bg-red-500';
    }

    // =========================
    // WARNA HEX UNTUK CHART.JS
    // Nilai ini HARUS senada dengan
    // fungsi warna() dan keterangan
    // kategori di atas, supaya warna
    // batang grafik konsisten dengan
    // badge kategori.
    // =========================
    function warnaHex($nilai)
    {
        if ($nilai >= 4.21) {
            return '#1d4ed8'; // blue-700 - Sangat Puas
        }
        elseif ($nilai >= 3.41) {
            return '#3b82f6'; // blue-500 - Puas
        }
        elseif ($nilai >= 2.61) {
            return '#fbbf24'; // amber-400 - Cukup Puas
        }
        elseif ($nilai >= 1.81) {
            return '#f97316'; // orange-500 - Tidak Puas
        }

        return '#ef4444'; // red-500 - Sangat Tidak Puas
    }

    // Skor 1.00 - 5.00 dikonversi ke lebar progress bar (0% - 100%)
    function barWidth($nilai)
    {
        return (($nilai - 1) / 4) * 100;
    }

    function formatSkor($nilai)
    {
        return number_format($nilai, 2, ',', '.');
    }

@endphp

{{-- =========================
CARD EUCS
========================= --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-5 mb-8">

    {{-- CONTENT --}}
    <div class="bg-white border border-blue-100 rounded-2xl p-5 premium-shadow">

        <p class="text-slate-500 text-sm mb-2">
            Content
        </p>

        <h3 class="text-4xl font-bold mb-2 text-slate-800">
            {{ formatSkor($content) }}
        </h3>

        <p class="text-sm text-slate-500 mb-3">
            Kelengkapan informasi aplikasi
        </p>

        <div class="w-full bg-slate-100 rounded-full h-2 mb-2">

            <div class="{{ warna($content) }} h-2 rounded-full"
                 style="width: {{ barWidth($content) }}%">

            </div>

        </div>

        <span class="text-sm font-semibold text-slate-700">
            {{ kategori($content) }}
        </span>

    </div>

    {{-- ACCURACY --}}
    <div class="bg-white border border-blue-100 rounded-2xl p-5 premium-shadow">

        <p class="text-slate-500 text-sm mb-2">
            Accuracy
        </p>

        <h3 class="text-4xl font-bold mb-2 text-slate-800">
            {{ formatSkor($accuracy) }}
        </h3>

        <p class="text-sm text-slate-500 mb-3">
            Keakuratan informasi aplikasi
        </p>

        <div class="w-full bg-slate-100 rounded-full h-2 mb-2">

            <div class="{{ warna($accuracy) }} h-2 rounded-full"
                 style="width: {{ barWidth($accuracy) }}%">

            </div>

        </div>

        <span class="text-sm font-semibold text-slate-700">
            {{ kategori($accuracy) }}
        </span>

    </div>

    {{-- FORMAT --}}
    <div class="bg-white border border-blue-100 rounded-2xl p-5 premium-shadow">

        <p class="text-slate-500 text-sm mb-2">
            Format
        </p>

        <h3 class="text-4xl font-bold mb-2 text-slate-800">
            {{ formatSkor($format) }}
        </h3>

        <p class="text-sm text-slate-500 mb-3">
            Tampilan aplikasi
        </p>

        <div class="w-full bg-slate-100 rounded-full h-2 mb-2">

            <div class="{{ warna($format) }} h-2 rounded-full"
                 style="width: {{ barWidth($format) }}%">

            </div>

        </div>

        <span class="text-sm font-semibold text-slate-700">
            {{ kategori($format) }}
        </span>

    </div>

    {{-- EASE OF USE --}}
    <div class="bg-white border border-blue-100 rounded-2xl p-5 premium-shadow">

        <p class="text-slate-500 text-sm mb-2">
            Ease of Use
        </p>

        <h3 class="text-4xl font-bold mb-2 text-slate-800">
            {{ formatSkor($ease) }}
        </h3>

        <p class="text-sm text-slate-500 mb-3">
            Kemudahan penggunaan aplikasi
        </p>

        <div class="w-full bg-slate-100 rounded-full h-2 mb-2">

            <div class="{{ warna($ease) }} h-2 rounded-full"
                 style="width: {{ barWidth($ease) }}%">

            </div>

        </div>

        <span class="text-sm font-semibold text-slate-700">
            {{ kategori($ease) }}
        </span>

    </div>

    {{-- TIMELINESS --}}
    <div class="bg-white border border-blue-100 rounded-2xl p-5 premium-shadow">

        <p class="text-slate-500 text-sm mb-2">
            Timeliness
        </p>

        <h3 class="text-4xl font-bold mb-2 text-slate-800">
            {{ formatSkor($time) }}
        </h3>

        <p class="text-sm text-slate-500 mb-3">
            Kecepatan respon aplikasi
        </p>

        <div class="w-full bg-slate-100 rounded-full h-2 mb-2">

            <div class="{{ warna($time) }} h-2 rounded-full"
                 style="width: {{ barWidth($time) }}%">

            </div>

        </div>

        <span class="text-sm font-semibold text-slate-700">
            {{ kategori($time) }}
        </span>

    </div>

</div>

{{-- =========================
KESIMPULAN
========================= --}}
<div class="bg-gradient-to-r from-blue-50 to-amber-50 border border-blue-200 rounded-2xl p-6 mb-8">

    <h3 class="text-xl font-bold mb-3 text-slate-800">
        Kesimpulan Analisis
    </h3>

    <p class="text-slate-600 leading-8">

        Berdasarkan hasil analisis metode
        <strong class="text-slate-800">End User Computing Satisfaction (EUCS)</strong>,
        tingkat kepuasan pengguna aplikasi
        <strong class="text-slate-800">Coretax</strong>

        memperoleh skor rata-rata sebesar

        <strong class="text-2xl text-blue-700">
            {{ formatSkor($average) }}
        </strong>

        dengan kategori

        <strong class="text-slate-800">
            "{{ kategori($average) }}"
        </strong>.

    </p>

</div>

{{-- =========================
CHART
========================= --}}
<div class="bg-white border border-blue-100 rounded-2xl p-6 premium-shadow">

    <h3 class="text-xl font-bold mb-6 text-slate-800">
        Grafik EUCS
    </h3>

    <canvas id="eucsChart" height="120"></canvas>

</div>

<script>

new Chart(
    document.getElementById('eucsChart'),
    {

        type: 'bar',

        data: {

            labels: [

                'Content',
                'Accuracy',
                'Format',
                'Ease of Use',
                'Timeliness'

            ],

            datasets: [{

                label: 'Skor Rata-rata',

                data: [

                    {{ $content }},
                    {{ $accuracy }},
                    {{ $format }},
                    {{ $ease }},
                    {{ $time }}

                ],

                backgroundColor: [

                    '{{ warnaHex($content) }}',
                    '{{ warnaHex($accuracy) }}',
                    '{{ warnaHex($format) }}',
                    '{{ warnaHex($ease) }}',
                    '{{ warnaHex($time) }}'

                ],

                borderColor: [

                    '{{ warnaHex($content) }}',
                    '{{ warnaHex($accuracy) }}',
                    '{{ warnaHex($format) }}',
                    '{{ warnaHex($ease) }}',
                    '{{ warnaHex($time) }}'

                ],

                borderWidth: 1

            }]

        },

        options: {

            responsive: true,

            scales: {

                y: {

                    beginAtZero: true,

                    min: 1,

                    max: 5,

                    ticks: {

                        stepSize: 1

                    }

                }

            }

        }

    }

);

</script>

@endif

@endsection
