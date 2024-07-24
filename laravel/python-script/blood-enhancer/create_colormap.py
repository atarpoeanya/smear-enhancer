import numpy as np
import cv2
import os


def generateColormap(colormap_path: str, new_path: str):
    
    grey_colormap = cv2.imread(colormap_path, cv2.IMREAD_GRAYSCALE)
    # colormap = cv2.applyColorMap(colormap, cv2.COLOR_BGR2GRAY)

    color_mapping = {
        0: [0,255,0],  
        1: [128, 64, 128],  
        2: [244, 35, 232],  # Example: Sidewalk
        3: [70, 70, 70],    # Example: Building
        4: [102, 102, 156], # Example: Wall
        5: [190, 153, 153], # Example: Fence
        6: [153, 153, 153], # Example: Pole
        7: [250, 170, 30],  # Example: Traffic Light
        8: [220, 220, 0],   # Example: Traffic Sign
        9: [107, 142, 35],  # Example: Vegetation
        10: [152, 251, 152],# Example: Terrain
        11: [0, 130, 180],  # Example: Sky
        12: [220, 20, 60],  # Example: Person
        13: [255, 0, 0],     # Example: Rider
        14: [0, 0, 255],     # Blue for Car
        15: [255, 255, 0],   # Yellow for Bicycle
        16: [255, 165, 0],   # Orange for Motorcycl
    }
    # Create an empty RGB image
    rgb_image = np.zeros((grey_colormap.shape[0], grey_colormap.shape[1], 3), dtype=np.uint8)

    # Map the grayscale values to RGB colors
    for gray_value, color in color_mapping.items():
        rgb_image[grey_colormap == gray_value] = color
    
    cv2.imwrite(new_path, rgb_image)
    if os.path.exists(colormap_path):
        os.remove(colormap_path)