import image_loader
from chainer import serializers
from MyFCN import *
from chainer import cuda, optimizers, Variable
import sys
import time
import datetime
import os
from state_b import State
from pixl_cpu import *

#_/_/_/ paths _/_/_/ 
#_/_/_/ training parameters _/_/_/ 
LEARNING_RATE    = 0.001
# TEST_BATCH_SIZE  = 1 #must be 1
DISCOUNT_FACTOR = 0.95 # discount factor

# EPISODE_LEN = 5

N_ACTIONS = 17

GPU_ID = 0

def test(loader: image_loader.ImageLoader, 
         agent, 
         image_result_path: str, 
         episode_len: int):
    sum_psnr   = 0
    sum_reward = 0
    
    current_state = State()

    raw_x = loader.load_data()
    raw_n = raw_x.copy()
    current_state.reset(raw_n)
    # raw_n = smallFunc.lower_contrast(raw_n)
    reward = np.zeros(raw_x.shape, raw_x.dtype)*255
    
    for t in range(0, episode_len):
        # print(f'Current step {t}')
        previous_image = current_state.image.copy()
        action = agent.act(current_state.image)
        current_state.step(action)

        # Write colormap image
        # a = action.astype(np.uint8)
        # a = np.transpose(a, (1,2,0))
        # cv2.imwrite(image_result_path, ("cm-" + str(datetime.datetime.now().timestamp()) + ".png"), a)

        reward = np.square(raw_x - previous_image)*255 - np.square(raw_x - current_state.image)*255
        sum_reward += np.mean(reward)*np.power(DISCOUNT_FACTOR,t)

        p = np.maximum(0,current_state.image)
        p = np.minimum(1,p)
        p = (p*255+0.5).astype(np.uint8)
        # Write per episode
        cv2.imwrite(image_result_path, np.transpose(p[0], (1,2,0)))


    agent.stop_episode()
    I = np.maximum(0,raw_x)
    I = np.minimum(1,I)
    I = (I*255+0.5).astype(np.uint8)
    sum_psnr += cv2.PSNR(p, I)
    
    return image_result_path
 
 
def main(input_path: str, image_result_path: str, model_used_path: str, episode_len = 5):
    #_/_/_/ load dataset _/_/_/ 
    mini_batch_loader = image_loader.ImageLoader(input_path)
 
    # load myfcn model
    model = MyFcn(N_ACTIONS)
    
    #_/_/_/ setup _/_/_/
    optimizer = chainer.optimizers.Adam(alpha=LEARNING_RATE)
    optimizer.setup(model)

    agent = PixelWiseA3C(model, optimizer, episode_len, DISCOUNT_FACTOR)
    
    chainer.serializers.load_npz(model_used_path, agent.model)
    agent.act_deterministically = True
    # agent.model.to_gpu()

    #_/_/_/ testing _/_/_/
    
    return test(mini_batch_loader, agent, image_result_path, episode_len)
    
     
 
if __name__ == '__main__':
    if len(sys.argv) != 5:
        sys.exit(1)

    input_path = sys.argv[1]
    output_path = sys.argv[2]
    model_path = sys.argv[3]
    episode_len = int(sys.argv[4])
    try:
        # fout = open('testlog.txt', "w")
        main(input_path, output_path, model_path, episode_len)
    except Exception as error:
        print(error.message)
