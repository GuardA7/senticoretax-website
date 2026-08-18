import os
import json
import pandas as pd

from services.preprocessing import preprocess_detail

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# =========================
# PATH DATASET
# =========================
xlsx_path = os.path.join(BASE_DIR, 'dataset', 'dataset.xlsx')

xls_path = os.path.join(BASE_DIR, 'dataset', 'dataset.xls')

csv_path = os.path.join(BASE_DIR, 'dataset', 'dataset.csv')

# =========================
# LOAD DATASET
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
# HASIL
# =========================
results = []

# =========================
# LOOP DATA
# =========================
for _, row in df.iterrows():

    try:

        text = str(
            row['content']
        )

        preprocess = preprocess_detail(
            text
        )

        results.append({

            'username':
                str(
                    row['username']
                ),

            'content':
                text,

            'cleaning':
                str(
                    preprocess.get(
                        'cleaning',
                        ''
                    )
                ),

            'tokenizing':
                ', '.join(
                    preprocess.get(
                        'tokenizing',
                        []
                    )
                ),

            'stopword':
                ', '.join(
                    preprocess.get(
                        'stopword',
                        []
                    )
                ),

            'stemming':
                ', '.join(
                    preprocess.get(
                        'stemming',
                        []
                    )
                ),

            'final':
                str(
                    preprocess.get(
                        'final',
                        ''
                    )
                )

        })

    except Exception as e:

        print('ERROR:', e)

# =========================
# SIMPAN JSON
# =========================
with open(
    os.path.join(BASE_DIR, 'dataset', 'preprocessing_result.json'),
    'w',
    encoding='utf-8'
) as f:

    json.dump(
        results,
        f,
        ensure_ascii=False,
        indent=4
    )

print(len(results))

print('✅ Preprocessing selesai')
