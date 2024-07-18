import numpy as np
import cv2
import os
import sys


# def psnr(image_path, preprocessed_images_paths: list[str], original_folder_path, preprocessed_folder_path):
def psnr(var = "This is  a test"):
  psnr = var

  # originial = cv2.imread(os.path.join(original_folder_path, image_path))
  # for path in preprocessed_images_paths:
  #   temp_image = cv2.imread(os.path.join(preprocessed_folder_path,  path))
  #   psnr_value = cv2.PSNR(originial, temp_image)
  #   psnr[path]= psnr_value
  

  return psnr
    # TODO
    # PSNR GROUND TRUTH to RESULT
    # Over over exposure


if __name__ == '__main__':
    if len(sys.argv) != 2:
        sys.exit(1)

    input_path = sys.argv[1]
    psnr(input_path)