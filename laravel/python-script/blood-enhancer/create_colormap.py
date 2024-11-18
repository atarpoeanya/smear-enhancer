import numpy as np
import cv2
import os


def generateColormap(colormap_path: str, new_path: str):
    
    grey_colormap = cv2.imread(colormap_path, cv2.IMREAD_GRAYSCALE)
    # colormap = cv2.applyColorMap(colormap, cv2.COLOR_BGR2GRAY)

    color_mapping = {
        0: [0,255,0],       # rgb(0, 255, 0)
        1: [128, 64, 128],  # rgb(128, 64, 128)
        2: [244, 35, 232],  # rgb(244, 35, 232)
        3: [70, 70, 70],    # rgb(70,70,70)
        4: [102, 102, 156], # rgb(102,102,156)
        5: [190, 153, 153], # rgb(190,153,53)
        6: [153, 153, 153], # rgb(153,153,153)
        7: [250, 170, 30],  # rgb(250,170,30)
        8: [220, 220, 0],   # rgb(220,220,0)
        9: [107, 142, 35],  # rgb(107,142,35)
        10: [152, 251, 152],# rgb(152,251,152)
        11: [0, 130, 180],  # rgb(0,130,180)
        12: [220, 20, 60],  # rgb(220,20,60)
        13: [255, 0, 0],     # rgb(255,0,0)
        14: [0, 0, 255],     # rgb(0,0,255)
        15: [255, 255, 0],   # rgb(255,255,0)
        16: [50, 165, 100],   # rgb(50,165,100)
    }
    # Create an empty RGB image
    rgb_image = np.zeros((grey_colormap.shape[0], grey_colormap.shape[1], 3), dtype=np.uint8)

    # Map the grayscale values to RGB colors
    for gray_value, color in color_mapping.items():
        rgb_image[grey_colormap == gray_value] = color
    
    cv2.imwrite(new_path, rgb_image)
    if os.path.exists(colormap_path):
        os.remove(colormap_path)