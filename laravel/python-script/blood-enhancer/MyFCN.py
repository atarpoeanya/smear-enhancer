import chainer
from chainer import Variable
import chainer.links as L
import chainer.functions as F
import numpy as np
import math
import cv2
import chainerrl
from chainerrl.agents import a3c

class MyFcn_trained(chainer.Chain, a3c.A3CModel):
 
    def __init__(self):
        
        super(MyFcn_trained, self).__init__(
            conv1=L.Convolution2D( 3, 64, 3, stride=1, pad=1, nobias=False, initialW=None, initial_bias=None),
            diconv2=DilatedConvBlock(2, None, None),
            diconv3=DilatedConvBlock(3, None, None),
            diconv4=DilatedConvBlock(4, None, None),
            diconv5=DilatedConvBlock(3, None, None),
            diconv6=DilatedConvBlock(2, None, None),
            conv7=L.Convolution2D( 64, 3, 3, stride=1, pad=1, nobias=False, initialW=None, initial_bias=None),
        )

class DilatedConvBlock(chainer.Chain):

    def __init__(self, d_factor, weight, bias):
        super(DilatedConvBlock, self).__init__(
            diconv=L.DilatedConvolution2D( in_channels=64, out_channels=64, ksize=3, stride=1, pad=d_factor, dilate=d_factor, nobias=False, initialW=weight, initial_bias=bias),
            #bn=L.BatchNormalization(64)
        )

        self.train = True

    def __call__(self, x):
        h = F.relu(self.diconv(x))
        #h = F.relu(self.bn(self.diconv(x)))
        return h


class MyFcn(chainer.Chain, a3c.A3CModel):
 
    def __init__(self, n_actions):
        w = chainer.initializers.HeNormal()
        net = MyFcn_trained()
        chainer.serializers.load_npz("D:/ma repo/smear-enhancer/laravel/python-script/blood-enhancer/model/200model.npz", net)
        super(MyFcn, self).__init__(
            conv1=L.Convolution2D( 3, 64, 3, stride=1, pad=1, nobias=False, initialW=net.conv1.W.data, initial_bias=net.conv1.b.data),
             diconv2=DilatedConvBlock(2, net.diconv2.diconv.W.data, net.diconv2.diconv.b.data),
            diconv3=DilatedConvBlock(3, net.diconv3.diconv.W.data, net.diconv3.diconv.b.data),
            diconv4=DilatedConvBlock(4, net.diconv4.diconv.W.data, net.diconv4.diconv.b.data),
            
            diconv5_pi=DilatedConvBlock(3, net.diconv5.diconv.W.data, net.diconv5.diconv.b.data),
            diconv6_pi=DilatedConvBlock(2, net.diconv6.diconv.W.data, net.diconv6.diconv.b.data),
            
            conv7_pi=chainerrl.policies.SoftmaxPolicy(L.Convolution2D( 64, n_actions, 3, stride=1, pad=1, nobias=False, initialW=w)),

            diconv5_V=DilatedConvBlock(3, net.diconv5.diconv.W.data, net.diconv5.diconv.b.data),
            diconv6_V=DilatedConvBlock(2, net.diconv6.diconv.W.data, net.diconv6.diconv.b.data),
            conv7_V=L.Convolution2D( 64, 1, 3, stride=1, pad=1, nobias=False, initialW=w),
        )
        self.train = True
 
    def pi_and_v(self, x):
         
        h = F.relu(self.conv1(x))
        h = self.diconv2(h)
        h = self.diconv3(h)
        h = self.diconv4(h)
        h_pi = self.diconv5_pi(h)
        h_pi = self.diconv6_pi(h_pi)

        pout = self.conv7_pi(h_pi)
        
        h_V = self.diconv5_V(h)
        h_V = self.diconv6_V(h_V)
        vout = self.conv7_V(h_V)
       
        return pout, vout
