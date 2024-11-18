import numpy as np
import cv2
import os
import sys


# def psnr(image_path, preprocessed_images_paths: list[str], original_folder_path, preprocessed_folder_path):
def mse(input_path, output_path):
  mse = 0

  originial = cv2.imread(input_path)
  output = cv2.imread(output_path)

  mse = cal_mse(originial, output)
  
  
  return mse

    # TODO
    # PSNR GROUND TRUTH to RESULT
    # Over over exposure

def cal_mse(image1, image2):
   # Own implementation
    mse = np.mean((image1.astype(np.float32) / 255 - image2.astype(np.float32) / 255) ** 2)
    return mse


if __name__ == '__main__':
    if len(sys.argv) != 3:
        sys.exit(1)

    input_path = sys.argv[1]
    output_path = sys.argv[2]
    print(mse(input_path, output_path))