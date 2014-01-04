#!/bin/bash

VERSION='REU' #版本标识
SRC=`pwd` #获取当前目录
cd ..
PDATE=`date +%y%m%d%H` #获取打包时间
TARGET='phpBBwap.'$VERSION'.'$PDATE
DEST=`pwd`'/'$TARGET

#准备待打包文件
rm -rf $DEST
cp -vr $SRC $DEST
cd $DEST
rm -vrf .git .settings .buildpath .gitignore .project package.sh README.txt docs

cd ..
7z a -tzip $TARGET'.zip' $TARGET
#tar czvf $TARGET'.tar.gz' $TARGET
