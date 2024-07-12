import os
import numpy as np
import cv2
 
 
class MiniBatchLoader(object):
 
    def __init__(self, test_path, image_dir_path):
 
        self.testing_path_infos = self.read_paths(test_path, image_dir_path)
 
    # test ok
    @staticmethod
    def path_label_generator(txt_path, src_path):
        for line in open(txt_path):
            line = line.strip()
            src_full_path = os.path.join(src_path, line)
            if os.path.isfile(src_full_path):
                yield src_full_path
 
    # test ok
    @staticmethod
    def count_paths(path):
        c = 0
        for _ in open(path):
            c += 1
        return c
 
    # test ok
    @staticmethod
    def read_paths(txt_path, src_path):
        cs = []
        for pair in MiniBatchLoader.path_label_generator(txt_path, src_path):
            cs.append(pair)
        return cs
 
    def load_testing_data(self, indices):
        return self.load_data(self.testing_path_infos, indices)
 
    # test ok
    def load_data(self, path_infos, indices, augment=False):
        mini_batch_size = len(indices)
        in_channels = 3

        if mini_batch_size == 1:
            for i, index in enumerate(indices):
                path = path_infos[index]
                
                img = cv2.imread(path,cv2.IMREAD_COLOR)
                # img = cv2.resize(img, (0,0), fx=0.5, fy=0.5) 
                if img is None:
                    raise RuntimeError("invalid image: {i}".format(i=path))

            h, w, _ = img.shape
            xs = np.zeros((mini_batch_size, in_channels, h, w)).astype(np.float32)
            
            img = (img/255).astype(np.float32)
            xs[0, :, :, :] = np.transpose(img, (2,0,1))

        else:
            raise RuntimeError("mini batch size must be 1 when testing")
 
        return xs
