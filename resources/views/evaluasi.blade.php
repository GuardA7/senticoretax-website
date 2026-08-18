@extends('layouts.app')

@section('title', 'Evaluasi Model')

@section('content')

<h2 class="text-3xl font-bold mb-1 text-slate-800">
    Evaluasi Model
</h2>

<p class="text-slate-500 mb-6">
    Perbandingan performa Naïve Bayes dan SVM berdasarkan data uji.
</p>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- ========================= -->
    <!-- NAIVE BAYES -->
    <!-- ========================= -->
    <div class="bg-white rounded-2xl border border-blue-100 p-6 premium-shadow">

        <div class="flex items-center gap-3 mb-5">

            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-700 text-xl">psychology</span>
            </div>

            <h3 class="text-xl font-bold text-blue-700">
                Naïve Bayes
            </h3>

        </div>

        <!-- Accuracy -->
        <div class="mb-5 p-5 bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl">

            <p class="text-center text-4xl font-bold text-white">
                {{ number_format($nbMetrics['accuracy'], 2) }}%
            </p>

            <p class="text-center text-blue-100 mt-1">
                Accuracy
            </p>

        </div>

        <!-- Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">

            <div class="bg-cyan-50 border border-cyan-100 rounded-xl p-3 text-center">
                <p class="text-lg font-bold text-cyan-700">
                    {{ number_format($nbMetrics['precision'], 2) }}%
                </p>
                <p class="text-xs text-slate-500 mt-1">Precision</p>
            </div>

            <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 text-center">
                <p class="text-lg font-bold text-amber-600">
                    {{ number_format($nbMetrics['recall'], 2) }}%
                </p>
                <p class="text-xs text-slate-500 mt-1">Recall</p>
            </div>

            <div class="bg-pink-50 border border-pink-100 rounded-xl p-3 text-center">
                <p class="text-lg font-bold text-pink-600">
                    {{ number_format($nbMetrics['f1_score'], 2) }}%
                </p>
                <p class="text-xs text-slate-500 mt-1">F1-Score</p>
            </div>

        </div>

        <!-- Confusion Matrix -->
        <h4 class="font-semibold mb-3 text-slate-700 flex items-center gap-2">
            <span class="material-symbols-outlined text-base text-slate-400">grid_on</span>
            Confusion Matrix
        </h4>

        <div class="overflow-x-auto">
        <table class="min-w-[420px] w-full text-sm text-center border-separate border-spacing-1">

            <tr>

                <th class="p-2"></th>

                <th class="p-2 text-xs font-semibold text-slate-500">Positif</th>
                <th class="p-2 text-xs font-semibold text-slate-500">Negatif</th>
                <th class="p-2 text-xs font-semibold text-slate-500">Netral</th>

            </tr>

            @foreach(['positif', 'negatif', 'netral'] as $actual)

                <tr>

                    <th class="p-2 text-xs font-semibold text-slate-500 bg-slate-50 rounded-lg">
                        {{ ucfirst($actual) }}
                    </th>

                    @foreach(['positif', 'negatif', 'netral'] as $pred)

                        <td class="p-2 rounded-lg font-semibold
                            {{ $actual === $pred
                                ? 'bg-blue-100 text-blue-800'
                                : 'bg-slate-50 text-slate-500' }}">

                            {{ $nbConfusion[$actual][$pred] ?? 0 }}

                        </td>

                    @endforeach

                </tr>

            @endforeach

        </table>
        </div>

    </div>

    <!-- ========================= -->
    <!-- SVM -->
    <!-- ========================= -->
    <div class="bg-white rounded-2xl border border-blue-100 p-6 premium-shadow">

        <div class="flex items-center gap-3 mb-5">

            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-600 text-xl">insights</span>
            </div>

            <h3 class="text-xl font-bold text-amber-600">
                SVM
            </h3>

        </div>

        <!-- Accuracy -->
        <div class="mb-5 p-5 bg-gradient-to-br from-amber-400 to-amber-600 rounded-xl">

            <p class="text-center text-4xl font-bold text-white">
                {{ number_format($svmMetrics['accuracy'], 2) }}%
            </p>

            <p class="text-center text-amber-50 mt-1">
                Accuracy
            </p>

        </div>

        <!-- Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">

            <div class="bg-cyan-50 border border-cyan-100 rounded-xl p-3 text-center">
                <p class="text-lg font-bold text-cyan-700">
                    {{ number_format($svmMetrics['precision'], 2) }}%
                </p>
                <p class="text-xs text-slate-500 mt-1">Precision</p>
            </div>

            <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 text-center">
                <p class="text-lg font-bold text-amber-600">
                    {{ number_format($svmMetrics['recall'], 2) }}%
                </p>
                <p class="text-xs text-slate-500 mt-1">Recall</p>
            </div>

            <div class="bg-pink-50 border border-pink-100 rounded-xl p-3 text-center">
                <p class="text-lg font-bold text-pink-600">
                    {{ number_format($svmMetrics['f1_score'], 2) }}%
                </p>
                <p class="text-xs text-slate-500 mt-1">F1-Score</p>
            </div>

        </div>

        <!-- Confusion Matrix -->
        <h4 class="font-semibold mb-3 text-slate-700 flex items-center gap-2">
            <span class="material-symbols-outlined text-base text-slate-400">grid_on</span>
            Confusion Matrix
        </h4>

        <div class="overflow-x-auto">
        <table class="min-w-[420px] w-full text-sm text-center border-separate border-spacing-1">

            <tr>

                <th class="p-2"></th>

                <th class="p-2 text-xs font-semibold text-slate-500">Positif</th>
                <th class="p-2 text-xs font-semibold text-slate-500">Negatif</th>
                <th class="p-2 text-xs font-semibold text-slate-500">Netral</th>

            </tr>

            @foreach(['positif', 'negatif', 'netral'] as $actual)

                <tr>

                    <th class="p-2 text-xs font-semibold text-slate-500 bg-slate-50 rounded-lg">
                        {{ ucfirst($actual) }}
                    </th>

                    @foreach(['positif', 'negatif', 'netral'] as $pred)

                        <td class="p-2 rounded-lg font-semibold
                            {{ $actual === $pred
                                ? 'bg-amber-100 text-amber-800'
                                : 'bg-slate-50 text-slate-500' }}">

                            {{ $svmConfusion[$actual][$pred] ?? 0 }}

                        </td>

                    @endforeach

                </tr>

            @endforeach

        </table>
        </div>

    </div>

</div>

@endsection