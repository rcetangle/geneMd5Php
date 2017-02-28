#!/usr/bin/env sh
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$DIR"
PROJECT_DIR="$DIR/../"
SRC_DIR="$DIR/../app/ImagesPath.lua"
OUTPUT_DIR="$DIR/../app/ImagesMd5.lua"
"$DIR"/gene_md5.sh -i "$SRC_DIR" -o "$OUTPUT_DIR" -d "$PROJECT_DIR" -n "resMd5"