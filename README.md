直接运行build.sh根据app/ImagesPath.lua生成对应的md5码输出到app/ImagesMd5.lua文件

⚠注意：保证res里面的资源，就是ImagesPath.lua里面用到的资源。
⚠注意：res里面用到了广告资源，（这些广告资源可能不会打在apk里面，通常直接通过http下载），但是生成md5码的时候，需要手动把所有广告资源，放置在res对应的位置里面。