import numpy as np
import cv2
import os
import sys


# def psnr(image_path, preprocessed_images_paths: list[str], original_folder_path, preprocessed_folder_path):
def psnr(input_path, output_path):
  psnr = 0

  originial = cv2.imread(input_path)
  output = cv2.imread(output_path)

  psnr = cal_psnr(originial, output)
  
  
  return psnr
    # TODO
    # PSNR GROUND TRUTH to RESULT
    # Over over exposure

def cal_psnr(image1, image2):
   # Own implementation
    mse = np.mean((image1.astype(np.float32) / 255 - image2.astype(np.float32) / 255) ** 2)
    return 10 * np.log10(1. / mse)


if __name__ == '__main__':
    if len(sys.argv) != 3:
        sys.exit(1)

    input_path = sys.argv[1]
    output_path = sys.argv[2]
    print(psnr(input_path, output_path))