import os
import sys
import mysql.connector
import cv2
import face_recognition
import numpy as np


# from Simple import SimpleFaceRecognizer


def compare_faces(_image_path, _id):
    mydb = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="loginface"
    )
    mycursor = mydb.cursor()
    mycursor.execute("SELECT * FROM facefeatures WHERE id = %s LIMIT 1", (_id,))
    result = mycursor.fetchone()
    face_enc = result[1]
    face_enc = np.fromstring(face_enc[1:-1], sep=',')
    frame = cv2.imread(_image_path)
    rgb_img = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
    real_time_image = face_recognition.face_encodings(rgb_img)
    result = face_recognition.compare_faces(face_enc, real_time_image)
    # os.remove(_image_path)
    if result[0]:
        return True
    else:
        return False


image_path = sys.argv[1]
user_id = sys.argv[2]
final_result = compare_faces(image_path, user_id)
print(final_result)
