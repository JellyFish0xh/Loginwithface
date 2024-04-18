import sys
import cv2
import face_recognition
import mysql.connector
import numpy as np


class SimpleFaceRecognizer:
    def __init__(self):
        self.mydb = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="loginface"
        )
        self.mycursor = self.mydb.cursor()
    def load_encoding_images(self,_image_path,_id):
        try:
            img = cv2.imread(_image_path)
            if img is None:
                print("Camera Not Working !")
            rgb_img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
            face_encodings = face_recognition.face_encodings(rgb_img)[0]
            img_encoding_str = np.array2string(face_encodings, separator=',')
            sql = "INSERT INTO facefeatures (id, face_enc) VALUES (%s, %s)"
            val = (_id,img_encoding_str)
            self.mycursor.execute(sql, val)
            self.mydb.commit()
            # os.remove(img_path)
        except Exception:
            print(str("No face found in the image"))
        finally:
            exit()


image_path = sys.argv[1]
id = sys.argv[2]
recognizer = SimpleFaceRecognizer()
recognizer.load_encoding_images(image_path,id)
# recognizer.load_encoding_images()

