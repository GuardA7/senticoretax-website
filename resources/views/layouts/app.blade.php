<!DOCTYPE html>
<html class="" lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Coretax Sentiment - Dashboard Utama')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        body { background-color: #f8fafc; }
        .sidebar-gradient { background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); }
        .premium-shadow { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.06), 0 2px 4px -1px rgba(0, 0, 0, 0.03); }
        .premium-shadow-hover:hover { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04); }
        @media (max-width: 767px) {
            .mobile-sidebar { transform: translateX(-100%); transition: transform 180ms ease; }
            .mobile-sidebar.is-open { transform: translateX(0); }
        }
    </style>
</head>
<!-- LOADING OVERLAY -->
<div id="loadingOverlay"
     class="hidden fixed inset-0 bg-black/40 z-[9999] flex items-center justify-center">

    <div class="bg-white px-6 py-4 rounded-xl text-center border border-gray-200 shadow-lg">

        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-500 mx-auto mb-3"></div>

        <p class="text-sm text-gray-600">
            Memproses Analisis...
        </p>

    </div>

</div>
<body class="font-sans text-gray-700">

<!-- Sidebar -->
<div id="sidebarBackdrop" class="fixed inset-0 bg-slate-950/50 z-40 hidden md:hidden" onclick="toggleSidebar(false)"></div>

<aside id="appSidebar" class="fixed left-0 top-0 h-screen w-72 sidebar-gradient flex flex-col py-6 px-4 z-50 mobile-sidebar md:translate-x-0">
    <div class="flex items-center gap-3 px-2 mb-8">
        <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center">
            <span class="material-symbols-outlined text-white">psychology</span>
        </div>
        <div>
            <h1 class="text-xl font-bold">Coretax</h1>
            <p class="text-[10px] text-gray-400 uppercase tracking-wider">Sentiment Analys</p>
        </div>
    </div>

    <nav class="flex flex-col gap-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-all">
            <span class="material-symbols-outlined text-sm">dashboard</span>
            <span class="text-sm">Dashboard</span>
        </a>
        <a href="#" onclick="openUploadModal()" class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
            <span class="material-symbols-outlined text-sm">upload_file</span>
            <span class="text-sm">Upload Dataset</span>
        </a>
        <a href="#" onclick="openManualModal()" class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
            <span class="material-symbols-outlined text-sm">edit_note</span>
            <span class="text-sm">Input Manual</span>
        </a>

        <div class="my-3 border-t border-gray-700"></div>

        <a href="{{ route('preprocessing') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg {{ request()->routeIs('preprocessing') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-all">
            <span class="material-symbols-outlined text-sm">auto_fix_high</span>
            <span class="text-sm">Preprocessing</span>
        </a>
        <a href="{{ route('klasifikasi.nb') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg {{ request()->routeIs('klasifikasi.nb') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-all">
            <span class="material-symbols-outlined text-sm">analytics</span>
            <span class="text-sm">Naïve Bayes</span>
        </a>
        <a href="{{ route('klasifikasi.svm') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg {{ request()->routeIs('klasifikasi.svm') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-all">
            <span class="material-symbols-outlined text-sm">insights</span>
            <span class="text-sm">SVM</span>
        </a>

        <div class="my-3 border-t border-gray-700"></div>

        <a href="{{ route('evaluasi') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg {{ request()->routeIs('evaluasi') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-all">
            <span class="material-symbols-outlined text-sm">task_alt</span>
            <span class="text-sm">Evaluasi Model</span>
        </a>
        <a href="{{ route('eucs') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg {{ request()->routeIs('eucs') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-all">
            <span class="material-symbols-outlined text-sm">psychology</span>
            <span class="text-sm">Analisis Kepuasan</span>
        </a>
        <a href="{{ route('perbandingan') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg {{ request()->routeIs('perbandingan') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-all">
            <span class="material-symbols-outlined text-sm">compare_arrows</span>
            <span class="text-sm">Perbandingan</span>
        </a>

        <div class="my-3 border-t border-gray-700"></div>

        <a href="{{ route('clear.data') }}" onclick="return confirm('Hapus semua data?')" class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all">
            <span class="material-symbols-outlined text-sm">delete_forever</span>
            <span class="text-sm">Hapus Semua Data</span>
        </a>
    </nav>

    <div class="mt-auto pt-4">
        <div class="bg-gray-800/50 rounded-xl p-3 border border-gray-700">
            <p class="text-xs text-gray-400 mb-2">Butuh bantuan?</p>
            <a href="{{ route('dashboard') }}" class="block w-full py-1.5 bg-gray-700 hover:bg-gray-600 text-center rounded-lg text-xs font-medium transition-colors">
                Refresh Data
            </a>
        </div>
    </div>
</aside>

<!-- Mobile Header -->
<header class="sticky top-0 z-30 flex items-center justify-between bg-slate-900 px-4 py-3 text-white md:hidden">
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-blue-400">psychology</span>
        <span class="font-semibold">Coretax</span>
    </div>
    <button type="button" aria-label="Buka menu" onclick="toggleSidebar(true)" class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-800">
        <span class="material-symbols-outlined">menu</span>
    </button>
</header>

<!-- Main Content -->
<main class="min-h-screen p-3 sm:p-6 md:ml-72">
    <div class="max-w-7xl mx-auto">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-300 rounded-lg text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-300 rounded-lg text-red-700 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>
</main>

<!-- Modal Upload -->
<div id="uploadModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
    <div class="max-h-[calc(100vh-2rem)] w-[calc(100%-2rem)] overflow-y-auto rounded-xl bg-white p-4 sm:p-6 max-w-md border border-gray-200 shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Upload Dataset</h3>
            <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-700">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('upload.dataset') }}" method="POST" enctype="multipart/form-data">

            @csrf

            <div class="mb-4">

                <label class="block text-sm font-medium mb-2 text-gray-700">
                    File Dataset
                </label>

                <input
                    type="file"
                    name="file"
                    id="datasetFileInput"
                    accept=".csv,.xlsx,.xls"
                    onchange="validateDatasetFileSize()"
                    class="w-full p-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700"
                    required
                >

                <p id="datasetFileSizeError" class="hidden text-xs text-red-500 mt-2">
                    Ukuran file melebihi 15 MB. Silakan pilih file lain.
                </p>

                <p class="text-xs text-gray-500 mt-2">
                    Format didukung: CSV, XLSX, XLS <br>
                    Ukuran maksimal: 15 MB <br>
                    Kolom wajib: username, content, score
                </p>

            </div>

            <div class="flex gap-3">

                <button
                    type="button"
                    onclick="closeUploadModal()"
                    class="py-2 px-4 bg-gray-100 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-200 transition-colors"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    id="uploadDatasetSubmitBtn"
                    class="flex-1 py-2 bg-blue-600 rounded-lg text-sm font-semibold text-white hover:bg-blue-700 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    Upload Dataset
                </button>

            </div>

        </form>
    </div>
</div>

<!-- Modal Manual Input -->
<div id="manualModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
    <div class="max-h-[calc(100vh-2rem)] w-[calc(100%-2rem)] overflow-y-auto rounded-xl bg-white p-4 sm:p-6 max-w-md border border-gray-200 shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Input Ulasan Manual</h3>
            <button onclick="closeManualModal()" class="text-gray-400 hover:text-gray-700">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('manual.input') }}" method="POST">

    @csrf

    <!-- USERNAME -->
    <div class="mb-4">

        <label class="block text-sm font-medium mb-2 text-gray-700">
            Username
        </label>

        <input
            type="text"
            name="userName"
            class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-700"
            placeholder="Masukkan username"
            required
        >

    </div>

    <!-- CONTENT -->
    <div class="mb-4">

        <label class="block text-sm font-medium mb-2 text-gray-700">
            Ulasan
        </label>

        <textarea
            name="content"
            id="manualContentInput"
            rows="4"
            maxlength="150"
            oninput="updateManualCharCount()"
            class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-700"
            placeholder="Masukkan ulasan pengguna (maks. 150 karakter)"
        ></textarea>

        <p class="text-xs text-gray-500 mt-1 text-right">
            <span id="manualCharCount">0</span>/150 karakter
        </p>

    </div>

    <!-- SCORE -->
    <div class="mb-4">

        <label class="block text-sm font-medium mb-2 text-gray-700">
            Score
        </label>

        <select
            name="score"
            class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-700"
            required
        >
            <option value="">Pilih Score</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>

    </div>

    <button
        type="submit"
        class="w-full py-3 bg-blue-600 rounded-lg font-semibold text-white hover:bg-blue-700 transition-colors"
    >
        Analisis Sentimen
    </button>

</form>
    </div>
</div>

<script>
    function toggleSidebar(open) {
        const sidebar = document.getElementById('appSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        sidebar.classList.toggle('is-open', open);
        backdrop.classList.toggle('hidden', !open);
    }

    function openUploadModal() {
        toggleSidebar(false);
        document.getElementById('uploadModal').classList.remove('hidden');
        document.getElementById('uploadModal').classList.add('flex');
    }
    function closeUploadModal() {
        document.getElementById('uploadModal').classList.add('hidden');
        document.getElementById('uploadModal').classList.remove('flex');
    }
    function validateDatasetFileSize() {
        const input = document.getElementById('datasetFileInput');
        const errorBox = document.getElementById('datasetFileSizeError');
        const submitBtn = document.getElementById('uploadDatasetSubmitBtn');
        const maxSizeBytes = 15 * 1024 * 1024; // 15 MB

        if (input.files.length > 0 && input.files[0].size > maxSizeBytes) {
            errorBox.classList.remove('hidden');
            submitBtn.disabled = true;
        } else {
            errorBox.classList.add('hidden');
            submitBtn.disabled = false;
        }
    }
    function openManualModal() {
        toggleSidebar(false);
        document.getElementById('manualModal').classList.remove('hidden');
        document.getElementById('manualModal').classList.add('flex');
    }
    function closeManualModal() {
        document.getElementById('manualModal').classList.add('hidden');
        document.getElementById('manualModal').classList.remove('flex');
    }
    function updateManualCharCount() {
        const input = document.getElementById('manualContentInput');
        const counter = document.getElementById('manualCharCount');
        if (input && counter) {
            counter.textContent = input.value.length;
        }
    }
    window.onclick = function(event) {
        if (event.target === document.getElementById('uploadModal')) closeUploadModal();
        if (event.target === document.getElementById('manualModal')) closeManualModal();
    }
</script>
@stack('scripts')
<script>

document.querySelectorAll('form').forEach(form => {

    form.addEventListener('submit', () => {

        document
            .getElementById('loadingOverlay')
            .classList.remove('hidden');

    });

});

</script>
</body>
</html>
