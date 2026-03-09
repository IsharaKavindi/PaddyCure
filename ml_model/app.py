from flask import Flask, render_template, request
from werkzeug.utils import secure_filename
import tensorflow as tf
import numpy as np
import os

app = Flask(__name__)

# -----------------------------
# CONFIG (MUST match training)
# -----------------------------
IMAGE_SIZE = 255

# ✅ EXACT class order from training
class_names = [
    'bacterial_leaf_blight',
    'bacterial_leaf_streak',
    'bacterial_panicle_blight',
    'blast',
    'brown_spot',
    'dead_heart',
    'downy_mildew',
    'hispa',
    'normal',
    'tungro'
]

# -----------------------------
# Load trained model
# -----------------------------
model = tf.keras.models.load_model(
    "model.keras",
    compile=False
)

# -----------------------------
# Prediction function
# -----------------------------
def predict(img):
    img_array = tf.keras.preprocessing.image.img_to_array(img)
    img_array = tf.expand_dims(img_array, 0)  # (1, 255, 255, 3)

    predictions = model.predict(img_array)

    predicted_class = class_names[np.argmax(predictions[0])]
    confidence = round(float(np.max(predictions[0])) * 100, 2)

    return predicted_class, confidence

# -----------------------------
# Home route
# -----------------------------
@app.route('/', methods=['GET', 'POST'])
def home():
    if request.method == 'POST':

        if 'file' not in request.files:
            return render_template('index.html', message='No file selected')

        file = request.files['file']

        if file.filename == '':
            return render_template('index.html', message='No file selected')

        if file and allowed_file(file.filename):
            filename = secure_filename(file.filename)

            # ✅ Ensure 'static' folder exists
            save_folder = 'static'
            if not os.path.exists(save_folder):
                os.makedirs(save_folder)

            filepath = os.path.join(save_folder, filename)
            file.save(filepath)

            img = tf.keras.preprocessing.image.load_img(
                filepath,
                target_size=(IMAGE_SIZE, IMAGE_SIZE)
            )

            predicted_class, confidence = predict(img)

            return render_template(
                'index.html',
                image_path=filepath,
                predicted_label=predicted_class,
                confidence=confidence
            )

    return render_template('index.html')

# -----------------------------
# File validation
# -----------------------------
def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in {'jpg', 'jpeg', 'png'}

# -----------------------------
# Run app
# -----------------------------
if __name__ == '__main__':
    app.run(debug=True)