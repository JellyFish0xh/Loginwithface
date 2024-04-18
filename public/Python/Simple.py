import glob
import os
import sys

import cv2
import face_recognition
import mysql.connector
import numpy as np


class SimpleFaceRecognizer:
    def __init__(self):
        try:
            self.mydb = mysql.connector.connect(
                host="127.0.0.1",
                user="root",
                password="",
                database="loginface"
            )
            self.mycursor = self.mydb.cursor()
        except mysql.connector.Error as err:
            print("Error connecting to MySQL:", err)

    def load_encoding_images(self, images_dir):
        try:
            images_paths = glob.glob(os.path.join(images_dir, "*.*"))
            print("{} image(s) found for encoding.".format(len(images_paths)))
            for img_path in images_paths:
                try:
                    img = cv2.imread(img_path)
                    if img is None:
                        print("Error loading image:", img_path)
                        continue

                    # Extract user ID and name from filename
                    filename = os.path.basename(img_path)
                    filename, rest = os.path.splitext(filename)
                    user_id, name = filename.split('.')
                    print(name)

                    # Extract facial encoding
                    rgb_img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
                    img_encoding = face_recognition.face_encodings(rgb_img)[0]
                    img_encoding_str = np.array2string(img_encoding, separator=',')

                    # Insert into database
                    sql = "INSERT INTO facefeatures (id, name, face_enc) VALUES (%s, %s, %s)"
                    val = (user_id, name, img_encoding_str)
                    self.mycursor.execute(sql, val)
                    self.mydb.commit()
                    print("Record inserted for:", user_id, name)
                    # os.remove(img_path)
                except Exception as e:
                    print("Error processing image:", img_path, "-", e)
        except Exception as e:
            print("Error loading images:", e)
        finally:
            if self.mydb.is_connected():
                self.mycursor.close()
                self.mydb.close()
                print("Database connection closed.")


# Create an instance of SimpleFaceRecognizer and load images for encoding
image_dir = sys.argv[1]
print(image_dir)
recognizer = SimpleFaceRecognizer()
recognizer.load_encoding_images(image_dir)
