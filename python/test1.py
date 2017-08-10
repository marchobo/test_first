# -*- coding: utf-8 -*-

import sys

if __name__ == '__main__':
    print 'Pythonスクリプト動作確認'
    print 'parameter1 is ' + sys.argv[1]
    print 'parameter2 is ' + sys.argv[2]
    print 'parameter1 + parameter2 = ' + str(int(sys.argv[1]) + int(sys.argv[2]))