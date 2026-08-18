import os
import json
import joblib
import pandas as pd

from sklearn.pipeline import Pipeline

from sklearn.feature_extraction.text import TfidfVectorizer

from sklearn.naive_bayes import MultinomialNB

from sklearn.svm import LinearSVC

from sklearn.model_selection import (
    train_test_split,
    GridSearchCV
)

from sklearn.metrics import (

    accuracy_score,

    precision_score,

    recall_score,

    f1_score,

    confusion_matrix

)

from services.preprocessing import (
    preprocess_text
)

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# =========================
# LOAD DATASET
# =========================
xlsx_path = os.path.join(BASE_DIR, 'dataset', 'dataset.xlsx')

xls_path = os.path.join(BASE_DIR, 'dataset', 'dataset.xls')

csv_path = os.path.join(BASE_DIR, 'dataset', 'dataset.csv')

# =========================
# CEK FILE
# =========================
if os.path.exists(xlsx_path):

    print('📄 Membaca dataset.xlsx')

    df = pd.read_excel(xlsx_path)

elif os.path.exists(xls_path):

    print('📄 Membaca dataset.xls')

    df = pd.read_excel(xls_path)

elif os.path.exists(csv_path):

    print('📄 Membaca dataset.csv')

    df = pd.read_csv(csv_path)

else:

    raise FileNotFoundError(
        'Dataset tidak ditemukan'
    )

# =========================
# RAPIIKAN HEADER
# =========================
df.columns = df.columns.str.strip()

# =========================
# HAPUS DATA NULL
# =========================
df = df.dropna(
    subset=['content', 'labelling']
)

# =========================
# NORMALISASI LABEL
# Dataset sering punya variasi penulisan
# label (spasi, typo, dsb) seperti
# "negatif ", "negetif", "nrgatif".
# Ini menyamakannya jadi 3 label baku.
# =========================
import difflib

VALID_LABELS = [
    'positif',
    'negatif',
    'netral'
]

def normalize_label(label):

    label = str(label).strip().lower()

    match = difflib.get_close_matches(
        label,
        VALID_LABELS,
        n=1,
        cutoff=0.6
    )

    return match[0] if match else label

df['labelling'] = df['labelling'].apply(
    normalize_label
)

print('📊 Distribusi label setelah normalisasi:')

print(df['labelling'].value_counts())

# =========================
# CEK LABEL YANG TIDAK DIKENALI
# =========================
unknown_labels = (
    set(df['labelling'].unique())
    - set(VALID_LABELS)
)

if unknown_labels:

    print(
        '⚠️  Ada label tidak dikenali, cek manual di dataset:',
        unknown_labels
    )

    df = df[
        df['labelling'].isin(VALID_LABELS)
    ]

# =========================
# BUANG KELAS DENGAN DATA TERLALU SEDIKIT
# (stratify butuh minimal 2 baris per kelas)
# =========================
label_counts = df['labelling'].value_counts()

valid_classes = label_counts[
    label_counts >= 2
].index

df = df[
    df['labelling'].isin(valid_classes)
]

# =========================
# RESET INDEX
# Supaya index df selaras dan gampang dipakai
# untuk menelusuri balik teks asli dari X_test
# (dibutuhkan untuk laporan misclassified/FP-FN)
# =========================
df = df.reset_index(drop=True)

# =========================
# PREPROCESSING TEKS
# WAJIB SAMA DENGAN YANG DIPAKAI
# SAAT PREDIKSI (services/prediction.py),
# supaya vocabulary TF-IDF konsisten
# antara training dan serving.
# =========================
print('🧹 Preprocessing dataset...')

X = df['content'].astype(str).apply(
    preprocess_text
)

y = df['labelling'].astype(str)

# =========================
# SPLIT DATA
# =========================
X_train, X_test, y_train, y_test = train_test_split(

    X,

    y,

    test_size=0.2,

    random_state=42,

    stratify=y

)

# =========================
# PIPELINE + GRIDSEARCH: NAIVE BAYES
# =========================
print('🚀 Training Naive Bayes (GridSearch)...')

pipeline_nb = Pipeline([
    ('tfidf', TfidfVectorizer()),
    ('nb', MultinomialNB())
])

param_nb = {

    'tfidf__ngram_range': [(1, 1), (1, 2), (1, 3)],

    'tfidf__max_features': [5000, 10000, 15000, 20000, None],

    'tfidf__min_df': [1, 2, 3],

    'tfidf__max_df': [0.85, 0.90, 0.95],

    'nb__alpha': [0.001, 0.01, 0.1, 0.5, 1, 2]

}

grid_nb = GridSearchCV(

    pipeline_nb,

    param_nb,

    cv=5,

    scoring='accuracy',

    n_jobs=-1,

    verbose=1

)

grid_nb.fit(X_train, y_train)

nb_model = grid_nb.best_estimator_

print('   Best params NB :', grid_nb.best_params_)

nb_prediction = nb_model.predict(X_test)

# =========================
# METRIK NB
# =========================
nb_accuracy = accuracy_score(
    y_test,
    nb_prediction
)

nb_precision = precision_score(
    y_test,
    nb_prediction,
    average='weighted'
)

nb_recall = recall_score(
    y_test,
    nb_prediction,
    average='weighted'
)

nb_f1 = f1_score(
    y_test,
    nb_prediction,
    average='weighted'
)

# =========================
# PIPELINE + GRIDSEARCH: SVM
# =========================
print('🚀 Training SVM (GridSearch)...')

pipeline_svm = Pipeline([
    ('tfidf', TfidfVectorizer()),
    ('svm', LinearSVC(random_state=42, class_weight='balanced'))
])

param_svm = {

    'tfidf__ngram_range': [(1, 1), (1, 2), (1, 3)],

    'tfidf__max_features': [5000, 10000, 15000, 20000, None],

    'tfidf__min_df': [1, 2, 3],

    'tfidf__max_df': [0.85, 0.90, 0.95],

    'svm__C': [0.01, 0.1, 1, 10, 100]

}

grid_svm = GridSearchCV(

    pipeline_svm,

    param_svm,

    cv=5,

    scoring='accuracy',

    n_jobs=-1,

    verbose=1

)

grid_svm.fit(X_train, y_train)

svm_model = grid_svm.best_estimator_

print('   Best params SVM:', grid_svm.best_params_)

svm_prediction = svm_model.predict(X_test)

# =========================
# METRIK SVM
# =========================
svm_accuracy = accuracy_score(
    y_test,
    svm_prediction
)

svm_precision = precision_score(
    y_test,
    svm_prediction,
    average='weighted'
)

svm_recall = recall_score(
    y_test,
    svm_prediction,
    average='weighted'
)

svm_f1 = f1_score(
    y_test,
    svm_prediction,
    average='weighted'
)

# =========================
# CONFUSION MATRIX (ASLI, BUKAN DUMMY)
# Diformat sebagai nested dict:
# { actual_label: { predicted_label: jumlah } }
# supaya langsung cocok dibaca oleh
# EvaluationController.php / evaluasi.blade.php
# =========================
LABELS = ['positif', 'negatif', 'netral']

def build_confusion_matrix(y_true, y_pred):

    cm = confusion_matrix(
        y_true,
        y_pred,
        labels=LABELS
    )

    matrix = {}

    for i, actual in enumerate(LABELS):

        matrix[actual] = {}

        for j, pred in enumerate(LABELS):

            matrix[actual][pred] = int(cm[i][j])

    return matrix

nb_confusion = build_confusion_matrix(
    y_test,
    nb_prediction
)

svm_confusion = build_confusion_matrix(
    y_test,
    svm_prediction
)

print('📐 Confusion Matrix Naive Bayes:', nb_confusion)

print('📐 Confusion Matrix SVM:', svm_confusion)

# =========================
# KUMPULKAN CONTOH SALAH KLASIFIKASI (FP/FN)
# Untuk setiap baris di data uji yang diprediksi
# SALAH, simpan teks aslinya (sebelum preprocessing),
# label sebenarnya, dan label hasil prediksi.
# Ini bahan untuk bagian "Contoh Kesalahan Klasifikasi"
# di halaman Evaluasi.
# =========================
def collect_misclassified(
    y_true,
    y_pred,
    test_index,
    dataframe,
    model_name,
    limit=100
):

    results = []

    y_true_list = list(y_true)

    y_pred_list = list(y_pred)

    idx_list = list(test_index)

    for actual, predicted, idx in zip(
        y_true_list,
        y_pred_list,
        idx_list
    ):

        if actual != predicted:

            results.append({

                'model': model_name,

                # =========================
                # TEKS ASLI (BELUM DI-PREPROCESS)
                # Supaya gampang dibaca manusia
                # saat dianalisis manual
                # =========================
                'content':
                    str(dataframe.loc[idx, 'content']),

                'actual': actual,

                'predicted': predicted,

                # =========================
                # KATEGORI KESALAHAN
                # FP untuk kelas hasil prediksi,
                # FN untuk kelas yang sebenarnya
                # =========================
                'error_type':
                    f'FP untuk "{predicted}" / FN untuk "{actual}"'

            })

    return results[:limit] if limit else results

nb_misclassified = collect_misclassified(
    y_test,
    nb_prediction,
    X_test.index,
    df,
    'naive_bayes'
)

svm_misclassified = collect_misclassified(
    y_test,
    svm_prediction,
    X_test.index,
    df,
    'svm'
)

print(
    f'❌ Naive Bayes salah klasifikasi: {len(nb_misclassified)} dari {len(y_test)} data uji'
)

print(
    f'❌ SVM salah klasifikasi: {len(svm_misclassified)} dari {len(y_test)} data uji'
)

# =========================
# BUAT FOLDER MODEL
# =========================
models_dir = os.path.join(BASE_DIR, 'models')

if not os.path.exists(models_dir):

    os.makedirs(models_dir)

# =========================
# SIMPAN MODEL
# Sekarang masing-masing PIPELINE UTUH
# (tfidf + classifier jadi satu file),
# vectorizer.pkl terpisah TIDAK dipakai lagi.
# =========================
joblib.dump(
    nb_model,
    os.path.join(models_dir, 'nb_model.pkl')
)

joblib.dump(
    svm_model,
    os.path.join(models_dir, 'svm_model.pkl')
)

# =========================
# SIMPAN HASIL EVALUASI
# (accuracy, precision, recall, f1, confusion matrix)
# =========================
accuracy = {

    'naive_bayes': {

        'accuracy':
            round(nb_accuracy * 100, 2),

        'precision':
            round(nb_precision * 100, 2),

        'recall':
            round(nb_recall * 100, 2),

        'f1_score':
            round(nb_f1 * 100, 2),

        'best_params':
            grid_nb.best_params_,

        'confusion_matrix':
            nb_confusion

    },

    'svm': {

        'accuracy':
            round(svm_accuracy * 100, 2),

        'precision':
            round(svm_precision * 100, 2),

        'recall':
            round(svm_recall * 100, 2),

        'f1_score':
            round(svm_f1 * 100, 2),

        'best_params':
            grid_svm.best_params_,

        'confusion_matrix':
            svm_confusion

    }

}

with open(
    os.path.join(models_dir, 'accuracy.json'),
    'w'
) as f:

    json.dump(
        accuracy,
        f,
        default=str
    )

# =========================
# SIMPAN CONTOH KESALAHAN KLASIFIKASI (FP/FN)
# File terpisah karena isinya bisa panjang
# (daftar kalimat), beda kebutuhan dengan
# accuracy.json yang cuma angka ringkasan.
# =========================
misclassified = {

    'naive_bayes': nb_misclassified,

    'svm': svm_misclassified

}

with open(
    os.path.join(models_dir, 'misclassified.json'),
    'w',
    encoding='utf-8'
) as f:

    json.dump(
        misclassified,
        f,
        ensure_ascii=False,
        indent=2
    )

print(accuracy)

print('✅ Training selesai')
