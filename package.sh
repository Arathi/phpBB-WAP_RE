#!/bin/bash

VERSION='REU' #版本标识
SRC=`pwd` #获取当前目录
cd ..
PDATE=`date +%y%m%d%H` #获取打包时间
TARGET='phpBBwap.'$VERSION'.'$PDATE
DEST=`pwd`'/'$TARGET

#准备待打包文件
rm -rf $DEST
cp -r $SRC $DEST
cd $DEST
rm -rf phpBBwap.zip .git .settings .buildpath .gitignore .project package.bat package.sh README.txt phpBBwap.tar.gz

cd ..
tar czvf $TARGET'.tar.gz' $TARGET
