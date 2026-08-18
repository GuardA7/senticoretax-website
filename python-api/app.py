from flask import (
    Flask,
    request,
    jsonify
)

from services.preprocessing import (
    preprocess_detail
)

from services.prediction import (
    predict_nb,
    predict_svm,
    predict_nb_batch,
    predict_svm_batch
)

app = Flask(__name__)

# =========================
# PREPROCESSING
# =========================
@app.route(
    '/preprocessing',
    methods=['POST']
)
def preprocessing():

    data = request.get_json()

    text = data['text']

    result = preprocess_detail(text)

    return jsonify(result)

# =========================
# NAIVE BAYES
# =========================
@app.route(
    '/predict/nb',
    methods=['POST']
)
def predict_naive_bayes():

    data = request.get_json()

    text = data['content']

    result = predict_nb(text)

    return jsonify({
        'result': result
    })

# =========================
# SVM
# =========================
@app.route(
    '/predict/svm',
    methods=['POST']
)
def predict_support_vector_machine():

    data = request.get_json()

    text = data['content']

    result = predict_svm(text)

    return jsonify({
        'result': result
    })


@app.route('/predict/nb/batch', methods=['POST'])
def predict_naive_bayes_batch():

    data = request.get_json() or {}
    contents = data.get('contents', [])

    return jsonify({
        'results': predict_nb_batch(contents)
    })


@app.route('/predict/svm/batch', methods=['POST'])
def predict_support_vector_machine_batch():

    data = request.get_json() or {}
    contents = data.get('contents', [])

    return jsonify({
        'results': predict_svm_batch(contents)
    })

# =========================
# RUN
# =========================
if __name__ == '__main__':

    print("🚀 Flask API Running")

    app.run(
        debug=True
    )
