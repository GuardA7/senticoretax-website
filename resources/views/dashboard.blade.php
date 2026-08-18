@extends('layouts.app')

@section('title', 'Coretax Sentiment - Dashboard')

@section('content')

@if(session('success'))

<div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">

    {{ session('success') }}

</div>

@endif

<!-- HEADER -->
<div class="flex justify-between items-center mb-5">

    <div>

        <h2 class="text-2xl font-bold text-slate-800">
            Dashboard Utama
        </h2>

        <p class="text-sm text-slate-500 mt-1">
            Ikhtisar analisis sentimen aplikasi Coretax
        </p>

        <div class="mt-3 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-50 border border-green-200 text-green-700 text-xs">

            <span class="w-2 h-2 rounded-full bg-green-500"></span>

            Flask API Connected

        </div>

    </div>

    <!-- EXPORT -->
    <a
        href="{{ route('export.laporan') }}"
        class="flex items-center gap-2 px-4 py-3 bg-blue-700 hover:bg-blue-800 text-white rounded-xl font-semibold transition text-sm"
    >

        <span class="material-symbols-outlined text-base">
            download
        </span>

        Ekspor Laporan

    </a>

</div>

<!-- STATISTIK -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

    <!-- TOTAL -->
    <div class="bg-white border border-blue-100 rounded-xl p-5 premium-shadow">

        <p class="text-sm text-slate-500 mb-3">
            Total Ulasan
        </p>

        <p class="text-4xl font-bold text-slate-800">
            {{ number_format($total ?? 0) }}
        </p>

    </div>

    <!-- POSITIF -->
    <div class="bg-white border border-blue-100 rounded-xl p-5 premium-shadow">

        <p class="text-sm text-slate-500 mb-3">
            Positif
        </p>

        <p class="text-4xl font-bold text-green-600">
            {{ number_format($positif ?? 0) }}
        </p>

    </div>

    <!-- NETRAL -->
    <div class="bg-white border border-blue-100 rounded-xl p-5 premium-shadow">

        <p class="text-sm text-slate-500 mb-3">
            Netral
        </p>

        <p class="text-4xl font-bold text-amber-500">
            {{ number_format($netral ?? 0) }}
        </p>

    </div>

    <!-- NEGATIF -->
    <div class="bg-white border border-blue-100 rounded-xl p-5 premium-shadow">

        <p class="text-sm text-slate-500 mb-3">
            Negatif
        </p>

        <p class="text-4xl font-bold text-red-600">
            {{ number_format($negatif ?? 0) }}
        </p>

    </div>

</div>

<!-- CHART -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-6">

    <!-- AKURASI -->
    <div class="bg-white border border-blue-100 rounded-xl p-5 premium-shadow">

        <h3 class="text-xl font-bold mb-5 text-slate-800">
            Perbandingan Akurasi Model
        </h3>

        <div class="space-y-6">

            <!-- NB -->
            <div>

                <div class="flex justify-between items-center mb-2">

                    <span class="text-base text-slate-700">
                        Naïve Bayes
                    </span>

                    <span class="text-base font-bold text-blue-700">

                        {{ number_format($nbAccuracy ?? 0, 2) }}%

                    </span>

                </div>

                <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">

                    <div
                        class="h-full bg-blue-500 rounded-full transition-all duration-700"
                        style="width: {{ min($nbAccuracy, 100) }}%"
                    ></div>

                </div>

            </div>

            <!-- SVM -->
            <div>

                <div class="flex justify-between items-center mb-2">

                    <span class="text-base text-slate-700">
                        SVM
                    </span>

                    <span class="text-base font-bold text-amber-600">

                        {{ number_format($svmAccuracy ?? 0, 2) }}%

                    </span>

                </div>

                <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">

                    <div
                        class="h-full bg-amber-400 rounded-full transition-all duration-700"
                        style="width: {{ min($svmAccuracy, 100) }}%"
                    ></div>

                </div>

            </div>

        </div>

        <!-- KESIMPULAN -->
        <div class="mt-6 p-4 rounded-xl bg-blue-50 border border-blue-100">

            <p class="text-sm text-slate-600 leading-relaxed">

                @if(($svmAccuracy ?? 0) > ($nbAccuracy ?? 0))

                    Model
                    <span class="text-amber-600 font-bold">
                        SVM
                    </span>

                    memiliki performa lebih baik dibanding
                    Naïve Bayes.

                @elseif(($svmAccuracy ?? 0) < ($nbAccuracy ?? 0))

                    Model
                    <span class="text-blue-700 font-bold">
                        Naïve Bayes
                    </span>

                    memiliki performa lebih baik dibanding
                    SVM.

                @else

                    Kedua model memiliki performa yang sama.

                @endif

            </p>

        </div>

    </div>

    <!-- DISTRIBUSI -->
    <div class="bg-white border border-blue-100 rounded-xl p-5 premium-shadow">

        <h3 class="text-xl font-bold mb-5 text-slate-800">
            Distribusi Sentimen
        </h3>

        <div class="flex justify-center">

            <div class="width-[240px]">

                <canvas id="sentimentChart"></canvas>

            </div>

        </div>

        <!-- LEGEND -->
        <div class="flex justify-center gap-6 mt-5 flex-wrap">

            <div class="flex items-center gap-2">

                <div class="w-3 h-3 rounded-full bg-green-500"></div>

                <span class="text-sm text-slate-600">

                    Positif
                    ({{ number_format($positifPercent ?? 0, 1) }}%)

                </span>

            </div>

            <div class="flex items-center gap-2">

                <div class="w-3 h-3 rounded-full bg-amber-400"></div>

                <span class="text-sm text-slate-600">

                    Netral
                    ({{ number_format($netralPercent ?? 0, 1) }}%)

                </span>

            </div>

            <div class="flex items-center gap-2">

                <div class="w-3 h-3 rounded-full bg-red-500"></div>

                <span class="text-sm text-slate-600">

                    Negatif
                    ({{ number_format($negatifPercent ?? 0, 1) }}%)

                </span>

            </div>

        </div>

    </div>

</div>

<!-- HASIL MANUAL -->
@if(isset($manualText))

<div class="bg-white border border-blue-100 rounded-xl p-5 mb-6 premium-shadow">

    <div class="flex items-center justify-between mb-5">

        <div>

            <h3 class="text-xl font-bold text-slate-800">
                Hasil Analisis Manual
            </h3>

            <p class="text-sm text-slate-500 mt-1">
                Prediksi realtime berdasarkan ulasan pengguna
            </p>

        </div>

        <div class="px-3 py-1 rounded-full bg-green-50 border border-green-200 text-green-700 text-xs font-semibold">

            LIVE ANALYSIS

        </div>

    </div>

    <!-- STATUS -->
    <div class="bg-slate-50 rounded-xl p-4 mb-5">

        <p class="text-sm text-slate-500 mb-2">
            Status
        </p>

        <p class="text-lg font-semibold text-green-600">
            Berhasil Diproses
        </p>

    </div>

    <!-- ULASAN -->
    <div class="bg-slate-50 rounded-xl p-4 mb-5">

        <p class="text-sm text-slate-500 mb-2">
            Ulasan Pengguna
        </p>

        <p class="text-sm leading-relaxed text-slate-700">
            {{ $manualText }}
        </p>

    </div>

    <!-- HASIL -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <!-- NB -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">

            <h4 class="text-lg font-bold text-blue-700 mb-3">
                Naïve Bayes
            </h4>

            <p class="text-3xl font-bold mb-2 text-slate-800">

                {{ ucfirst($nbResult) }}

            </p>

            <p class="text-sm text-slate-600">
                Prediksi menggunakan algoritma Naïve Bayes.
            </p>

        </div>

        <!-- SVM -->
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">

            <h4 class="text-lg font-bold text-amber-600 mb-3">
                SVM
            </h4>

            <p class="text-3xl font-bold mb-2 text-slate-800">

                {{ ucfirst($svmResult) }}

            </p>

            <p class="text-sm text-slate-600">
                Prediksi menggunakan Support Vector Machine.
            </p>

        </div>

    </div>

</div>

@endif

<!-- FLOAT BUTTON -->
<button
    onclick="openManualModal()"
    class="fixed bottom-6 right-6 w-16 h-16 rounded-full bg-blue-700 hover:bg-blue-800 shadow-2xl flex items-center justify-center z-50 transition-all duration-300 hover:scale-110"
>

    <span class="material-symbols-outlined text-white text-3xl">
        edit
    </span>

</button>

<!-- MODAL -->
<div
    id="manualModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4"
>

    <div
        class="w-full max-w-xl bg-white border border-blue-100 rounded-2xl shadow-2xl overflow-hidden"
    >

        <!-- HEADER -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-blue-100">

            <div>

                <h3 class="text-2xl font-bold text-slate-800">
                    Input Ulasan Manual
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    Analisis sentimen otomatis
                </p>

            </div>

            <button
                onclick="closeManualModal()"
                class="text-slate-400 hover:text-slate-700 transition"
            >

                <span class="material-symbols-outlined text-3xl">
                    close
                </span>

            </button>

        </div>

        <!-- FORM -->
        <form
            id="manualInputForm"
            action="{{ route('manual.input', [], false) }}"
            method="POST"
            class="p-6"
        >

            @csrf

            <!-- ULASAN -->
            <div class="mb-2">

                <label class="block text-sm font-medium mb-2 text-slate-700">

                    Ulasan

                </label>

                <textarea
                    name="content"
                    id="dashManualContentInput"
                    rows="6"
                    maxlength="150"
                    oninput="updateDashManualCharCount()"
                    placeholder="Masukkan ulasan pengguna (maks. 150 karakter)"
                    required
                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                ></textarea>

            </div>

            <p class="text-xs text-slate-500 mb-6 text-right">
                <span id="dashManualCharCount">0</span>/150 karakter
            </p>

            <!-- ERROR -->
            <div id="manualInputError" class="hidden mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm"></div>

            <!-- BUTTON -->
            <button
                type="submit"
                id="manualInputSubmitBtn"
                class="w-full py-4 rounded-xl bg-blue-700 hover:bg-blue-800 transition text-white font-bold text-lg disabled:opacity-60 disabled:cursor-not-allowed"
            >

                Analisis Sentimen

            </button>

        </form>

        <!-- HASIL (langsung tampil di modal, tanpa redirect) -->
        <div id="manualInputResult" class="hidden p-6 border-t border-blue-100">

            <div class="flex items-center justify-between mb-4">

                <h4 class="text-lg font-bold text-slate-800">
                    Hasil Analisis
                </h4>

                <div class="px-3 py-1 rounded-full bg-green-50 border border-green-200 text-green-700 text-xs font-semibold">
                    SELESAI
                </div>

            </div>

            <div class="bg-slate-50 rounded-xl p-4 mb-4">

                <p class="text-sm text-slate-500 mb-2">Ulasan</p>
                <p id="manualResultContent" class="text-sm text-slate-700 leading-relaxed"></p>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <p class="text-sm font-bold text-blue-700 mb-1">Naïve Bayes</p>
                    <p id="manualResultNb" class="text-2xl font-bold text-slate-800"></p>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <p class="text-sm font-bold text-amber-600 mb-1">SVM</p>
                    <p id="manualResultSvm" class="text-2xl font-bold text-slate-800"></p>
                </div>

            </div>

            <button
                onclick="resetManualForm()"
                class="w-full py-3 rounded-xl bg-slate-100 hover:bg-slate-200 transition text-slate-700 font-semibold"
            >
                Input Ulasan Lain
            </button>

        </div>

    </div>

</div>

<!-- CHART -->
<script>

new Chart(
    document.getElementById('sentimentChart'),
    {

        type: 'doughnut',

        data: {

            labels: [

                'Positif',
                'Negatif',
                'Netral'

            ],

            datasets: [{

                data: [

                    {{ $positif ?? 0 }},
                    {{ $negatif ?? 0 }},
                    {{ $netral ?? 0 }}

                ],

                backgroundColor: [

                    '#22c55e',
                    '#ef4444',
                    '#f5c451'

                ],

                borderWidth: 0

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: true,

            plugins: {

                legend: {

                    display: false

                }

            }

        }

    }

);

function openManualModal()
{
    document
        .getElementById('manualModal')
        .classList
        .remove('hidden');

    document
        .getElementById('manualModal')
        .classList
        .add('flex');
}

function closeManualModal()
{
    document
        .getElementById('manualModal')
        .classList
        .remove('flex');

    document
        .getElementById('manualModal')
        .classList
        .add('hidden');
}

function updateDashManualCharCount()
{
    const input = document.getElementById('dashManualContentInput');
    const counter = document.getElementById('dashManualCharCount');
    if (input && counter) {
        counter.textContent = input.value.length;
    }
}

// =========================
// SUBMIT MANUAL INPUT VIA AJAX
// Hasil ditampilkan langsung di modal,
// tanpa redirect/scroll ke dashboard
// =========================
document.getElementById('manualInputForm').addEventListener('submit', function (e) {

    e.preventDefault();

    const form = e.target;
    const btn = document.getElementById('manualInputSubmitBtn');
    const errorBox = document.getElementById('manualInputError');
    const overlay = document.getElementById('loadingOverlay');

    errorBox.classList.add('hidden');
    btn.disabled = true;
    btn.textContent = 'Memproses...';

    if (overlay) {
        overlay.classList.remove('hidden');
    }

    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async (response) => {

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Terjadi kesalahan.');
        }

        return data;
    })
    .then((data) => {

        // Tampilkan hasil, sembunyikan form
        document.getElementById('manualResultContent').textContent = data.content;
        document.getElementById('manualResultNb').textContent = data.nbResult
            ? data.nbResult.charAt(0).toUpperCase() + data.nbResult.slice(1)
            : '-';
        document.getElementById('manualResultSvm').textContent = data.svmResult
            ? data.svmResult.charAt(0).toUpperCase() + data.svmResult.slice(1)
            : '-';

        form.classList.add('hidden');
        document.getElementById('manualInputResult').classList.remove('hidden');
    })
    .catch((err) => {

        errorBox.textContent = err.message;
        errorBox.classList.remove('hidden');
    })
    .finally(() => {

        btn.disabled = false;
        btn.textContent = 'Analisis Sentimen';

        if (overlay) {
            overlay.classList.add('hidden');
        }
    });
});

function resetManualForm()
{
    const form = document.getElementById('manualInputForm');

    form.reset();
    updateDashManualCharCount();

    document.getElementById('manualInputResult').classList.add('hidden');
    form.classList.remove('hidden');
}

</script>

@endsection
