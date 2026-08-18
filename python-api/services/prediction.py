import os
import joblib

from services.preprocessing import (
    preprocess_text
)

BASE_DIR = os.path.dirname(
    os.path.dirname(
        os.path.abspath(__file__)
    )
)

# =========================
# LOAD MODEL
# Sekarang setiap file adalah PIPELINE UTUH
# (tfidf + classifier), hasil dari
# grid_nb.best_estimator_ / grid_svm.best_estimator_
# yang disimpan di train.py.
# =========================
nb_model = None

svm_model = None

# =========================
# LOAD NB
# =========================
NB_MODEL_PATH = os.path.join(BASE_DIR, 'models', 'nb_model.pkl')
SVM_MODEL_PATH = os.path.join(BASE_DIR, 'models', 'svm_model.pkl')

if os.path.exists(NB_MODEL_PATH):

    nb_model = joblib.load(NB_MODEL_PATH)

# =========================
# LOAD SVM
# =========================
if os.path.exists(SVM_MODEL_PATH):

    svm_model = joblib.load(SVM_MODEL_PATH)

# =========================
# PREDICT NB
# =========================
def predict_nb(text):

    if nb_model is None:

        return 'Model NB belum ditraining'

    text = preprocess_text(text)

    prediction = nb_model.predict(
        [text]
    )[0]

    return prediction

# =========================
# PREDICT SVM
# =========================
def predict_svm(text):

    if svm_model is None:

        return 'Model SVM belum ditraining'

    text = preprocess_text(text)

    prediction = svm_model.predict(
        [text]
    )[0]

    return prediction


def predict_batch(model, texts):

    if model is None:
        return ['Model belum ditraining'] * len(texts)

    processed_texts = [preprocess_text(text) for text in texts]

    return model.predict(processed_texts).tolist()


def predict_nb_batch(texts):
    return predict_batch(nb_model, texts)


def predict_svm_batch(texts):
    return predict_batch(svm_model, texts)
