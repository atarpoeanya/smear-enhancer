import numpy as np
import cv2
from skimage.metrics import structural_similarity
from PIL import Image
from PIL import ImageEnhance as ie

# claheFilter_2 = cv2.createCLAHE(clipLimit=0.3, tileGridSize=(8,8))


def contrast(image: np.ndarray, b):
  image_rgb = (image.copy() * 255).astype(np.uint8)
  mean = np.mean(image_rgb)
  degenerate = np.zeros(image_rgb.shape)+mean

  image_rgb = b * image_rgb + (1-b) * degenerate
  image_rgb = np.clip(image_rgb,0,1)
  return (image_rgb / 255).astype(np.float32)

def new_contrast(image: np.ndarray, b, gamma=0):
  image_rgb = (image.copy() * 255).astype(np.uint8)

  image_rgb = np.clip(b * image_rgb + gamma, 0, 255)
  return (image_rgb / 255).astype(np.float32)

def legacy_contrast(image, contrast_factor):
    # Open the image and convert it to an array
    image_rgb = (image.copy() * 255).astype(np.uint8)
    image_rgb = np.transpose(image_rgb , (1,2,0))
    img_pil = Image.fromarray(image_rgb)
    
    obj = ie.Contrast(img_pil)
    
    manual_contrast_img = obj.enhance(contrast_factor)

    np_image = np.array(manual_contrast_img)
    

    np_image = np.float32(np_image / 255)
    np_image = np.transpose(np_image, (2,0,1))
    return np_image

def clahe_lab(image: np.ndarray, clip=0.3, tileSize=(8,8)):
  claheFilter_2 = cv2.createCLAHE(clip, tileSize)
  temp = np.zeros(image.shape, image.dtype)
  temp = cv2.cvtColor(image, cv2.COLOR_BGR2LAB)
  
  temp[...,0] = claheFilter_2.apply(temp[...,0])
  temp = cv2.cvtColor(temp, cv2.COLOR_LAB2BGR)

  return temp


def clahe_hsv(image: np.ndarray, clip=0.8, tileSize=(4,4)):
  claheFilter_2 = cv2.createCLAHE(clip, tileSize)

  temp1 = (image.copy() * 255).astype(np.uint8)

  temp = cv2.cvtColor(temp1, cv2.COLOR_BGR2HSV)
  
  temp[...,0] = claheFilter_2.apply(temp[...,2])
  temp = cv2.cvtColor(temp, cv2.COLOR_HSV2BGR)

  return (temp / 255).astype(np.float32)

def HE(image: np.ndarray, mean, contrast_factor):

  temp = np.uint8(image.copy() * 255)
  temp = np.transpose(temp, (1,2,0))
  temp = cv2.cvtColor(temp, cv2.COLOR_BGR2HSV)
  
  temp[...,2] = (temp[...,2] - mean) * contrast_factor + mean
  temp = cv2.cvtColor(temp, cv2.COLOR_HSV2BGR)
  temp = np.transpose(temp, (2,0,1))
  return np.float32(temp / 255)

def umf(image: np.ndarray, SIGMA=0.8):
    img = (image.copy() * 255).astype(np.uint8)
    hsi = toHSI(img)
    # h,s,i = cv2.split(hsi) 
    
    filterGaus = hsi.copy()
    filterGaus[...,2] = cv2.GaussianBlur(filterGaus[...,2], (9,9), SIGMA)

    # hsi -= SIGMA*filterGaus
    edge = hsi[...,2] - filterGaus[...,2]

    hsi[...,2] += SIGMA * edge
    # hsi = cv2.merge([h,s,i])
    return (backBGR(hsi) /255 ).astype(np.float32)

def stretching(image: np.ndarray, **kwargs):
    # Split the image into channels
    temp1 = (image.copy() * 255).astype(np.uint8)
    b, g, r = cv2.split(temp1)

    
    # Apply dark stretching to each channel
    b_stretched = dark_channel(b, **kwargs)
    g_stretched = dark_channel(g, **kwargs)
    r_stretched = dark_channel(r, **kwargs)
    
    # Merge the channels back into an RGB image
    stretched_image = cv2.merge([b_stretched, g_stretched, r_stretched])
    
    return (stretched_image / 255).astype(np.float32)



def dark_channel(channel: np.ndarray, gamma=0, multi=1):
    # Find minimum and maximum pixel values

    I = channel.copy()
    FS,th2 = cv2.threshold(I,0,255,cv2.THRESH_OTSU+cv2.THRESH_BINARY)

    lower = I[I < FS]
    upper = I[I > FS]
    
    max_upper, min_upper = max(upper, default=0), min(upper, default=0)
    max_lower, min_lower = max(lower, default=0), min(lower, default=0)

    # print(max_upper, min_upper, max_lower, min_lower)
    upper_f =lambda i: ((FS + gamma) * multi) + (i - min_upper) * ((255 - ((FS + gamma) * multi)) / ((max_upper - min_upper) + 0.00001))
    lower_f =lambda i: (i - min_lower) * (((FS + gamma) * multi) / ((max_lower - min_lower) + 0.00001))
    
    # If pixel value is more than threshold
    new_I = np.where(I > FS, 
                        upper_f(I),
                        lower_f(I)
                     )


    stretched_channel = new_I

    # print(max_upper, min_upper, max_lower, min_lower)

    return stretched_channel


def lower_contrast(image: np.ndarray, contrast_factor=0.3, b=0):
    """
    Lower the contrast of an image.
    """
    b += (1.0 * (1 - contrast_factor) / 2)
    temp = cv2.addWeighted(image, contrast_factor, image, 0, b)
    return temp

def lower_contrast_batch(image: np.ndarray, contrast_factor=0.3, b=0):
    """
    Lower the contrast of an image.
    """
    b += int(round(255*(1-contrast_factor)/2))
    image = cv2.addWeighted(image, contrast_factor, image, 0, b)

from math import pi

def toHSI(imageInput: np.ndarray):
    t = np.copy(imageInput)
    bgr = np.int32(cv2.split(t))
    
    blue = bgr[0]
    green = bgr[1]
    red = bgr[2]

    intensity = np.divide(blue + green + red, 3)

    minimum = np.minimum(np.minimum(red, green), blue)
    saturation = 1 - 3 * np.divide(minimum, red + green + blue)

    sqrt_calc = np.sqrt(((red - green) * (red - green)) + ((red - blue) * (green - blue))) + 0.00001
    

    sieve = [green < blue]

    hue = np.arccos((0.5 * ((red-green) + (red - blue)) / sqrt_calc))
    # print(hue, '\n')
    hue[np.all(np.array(sieve), axis=0)] = 2*pi - hue[np.all(np.array(sieve), axis=0)]
    # print(hue)
    # print('\n')

    hue = hue*180/pi

    # print("hue with rad")
    # print(hue)    
    # print('\n')

    hsi = cv2.merge((hue, saturation, intensity))
    return hsi


def backBGR(hsi):
  bgr = np.zeros(hsi.shape, np.int8)
  b,g,r = cv2.split(bgr)

  p = np.copy(hsi)
  h,s,i = cv2.split(p)
  h_no_rad = h * pi/180
  
 

#   h_no_rad = np.array([
#     [4.16789012, 2.34567890],
#     [0.98765432, 4.56789012]
#     # [3 ,2 ] [ 1, 3]
# ])

  # print(h_no_rad)
  # print('\n')
  hb = np.where((h_no_rad >= 4 * pi/3) & (h_no_rad < 2 * pi), 
                h_no_rad.copy() - 4 * pi/3 , 
                h_no_rad)
  # print(hb)
  # print('\n')
  hb = np.where((h_no_rad >= 2 * pi/3) & (h_no_rad < 4 * pi/3), h_no_rad.copy() - 2 * pi/3 , hb)
  # print(hb)
  # print('\n')
  
  # pool of pixel
  x = i * (1 - s)
  y = i *(1 + (s * np.cos(hb)) / (np.cos(pi/3 - hb)))
  z = 3*i - (x + y)

  sieveA = [(h_no_rad >= 4 * pi/3) & (h_no_rad < 2 * pi)]
  sieveB = [((h_no_rad >= 2 * pi/3) & (h_no_rad < 4 * pi/3))]

  b = x.copy()
  b[np.all(np.array(sieveA), axis=0)] = y[np.all(np.array(sieveA), axis=0)].copy()
  b[np.all(np.array(sieveB), axis=0)] = z[np.all(np.array(sieveB), axis=0)].copy()

  g = z.copy()
  g[np.all(np.array(sieveA), axis=0)] = x[np.all(np.array(sieveA), axis=0)].copy()
  g[np.all(np.array(sieveB), axis=0)] = y[np.all(np.array(sieveB), axis=0)].copy()

  r = y.copy()
  r[np.all(np.array(sieveA), axis=0)] = z[np.all(np.array(sieveA), axis=0)].copy()
  r[np.all(np.array(sieveB), axis=0)] = x[np.all(np.array(sieveB), axis=0)].copy()


  bgr = cv2.merge([b,g,r])
  bgr = np.uint8(np.round(bgr))
  
  return bgr