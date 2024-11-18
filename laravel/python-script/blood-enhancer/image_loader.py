import cv2
import numpy as np

class ImageLoader(object):
      
      def __init__(self, input_image_path):
          self.input_path = input_image_path
          
      
      def load_data(self) -> np.ndarray:
        in_channels = 3

        img = cv2.imread(self.input_path,cv2.IMREAD_COLOR)
        h, w, _ = img.shape
        img = (img/255).astype(np.float32)
        if h > 750 or w > 750:
          img = cv2.resize(img, (0,0), fx=0.5, fy=0.5)
          h, w, _ = img.shape
          cv2.imwrite(self.input_path, (img * 255).astype(np.uint8))

        xs = xs = np.zeros((1, in_channels, h, w)).astype(np.float32)
        xs[0, :, :, :] = np.transpose(img, (2,0,1))
        return xs