# -*- coding: utf-8 -*-

from pyPdf import PdfFileWriter, PdfFileReader
import os

print(os.getcwd())

output = PdfFileWriter()

input1 = PdfFileReader(file("021423301611111111.pdf", "rb"))
num1 = input1.getNumPages()    # ページ数を記録

input2 = PdfFileReader(file("test.pdf", "rb"))
num2 = input2.getNumPages() # ページ数を記録

for i in xrange(num1): # 1ページずつ出力する
    output.addPage(input1.getPage(i))

for i in xrange(num2):
    output.addPage(input2.getPage(i))

outputStream = file("pdf/mergedfile.pdf", "wb") # 出力ファイル名
output.write(outputStream)
outputStream.close()

print(os.getcwd())