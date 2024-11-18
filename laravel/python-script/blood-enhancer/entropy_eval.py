import numpy as np
import cv2
import os
import sys
from scipy.stats import entropy


# def psnr(image_path, preprocessed_images_paths: list[str], original_folder_path, preprocessed_folder_path):
def e(output_path):
    entropy = 0
    output = cv2.imread(output_path)

    entropy = cal_entropy(output)

    return entropy


def cal_entropy(img: np.ndarray):
    gray_image = cv2.cvtColor(img.copy(), cv2.COLOR_BGR2GRAY)
    _bins = 128

    hist, _ = np.histogram(gray_image.ravel(), bins=_bins, range=(0, _bins))
    prob_dist = hist / hist.sum()
    image_entropy = entropy(prob_dist, base=2)

    return image_entropy

if __name__ == "__main__":
    if len(sys.argv) != 2:
        sys.exit(1)

    output_path = sys.argv[1]
    print(e(output_path))
    
    
