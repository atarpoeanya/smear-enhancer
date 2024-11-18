import cv2
import numpy as np
from math import pi

def apply_gaussian_blur(image, kernel_size):
    """Apply Gaussian blur to the image."""
    return cv2.GaussianBlur(image, (kernel_size, kernel_size), 0)

def apply_motion_blur(image, kernel_size):
    """Apply motion blur to the image."""
    kernel = np.zeros((kernel_size, kernel_size))
    kernel[int((kernel_size - 1)/2), :] = np.ones(kernel_size)
    kernel = kernel / kernel_size
    return cv2.filter2D(image, -1, kernel)

def diminish_color_intensity(image, factor):
    """Diminish color intensity of the image."""
    temp = cv2.cvtColor(image, cv2.COLOR_BGR2HSV)
    temp[:, :, 1] = (temp[:, :, 1] * factor)
    return cv2.cvtColor(temp, cv2.COLOR_HSV2BGR)

def adjust_exposure(image, factor):
    """Adjust exposure of the image. Use factor > 1 to increase exposure, factor < 1 to decrease."""
    hsv = cv2.cvtColor(image, cv2.COLOR_BGR2HSV)
    hsv[:, :, 2] = np.clip(hsv[:, :, 2] * factor, 0, 255)
    return cv2.cvtColor(hsv, cv2.COLOR_HSV2BGR)

def process_image(image, blur_type='', blur_degree=5, color_intensity_factor=1, exposure_factor=1.2):
    """Process the image with given parameters."""
    image = np.transpose(image , (1,2,0))
    image = np.uint8(image * 255)

    if blur_type == 'gaussian':
        image = apply_gaussian_blur(image, blur_degree)
    elif blur_type == 'motion':
        image = apply_motion_blur(image, blur_degree)

    image = diminish_color_intensity(image, color_intensity_factor)
    image = adjust_exposure(image, exposure_factor)

    image = np.float32(image / 255)
    image = np.transpose(image, (2,0,1))
    return image