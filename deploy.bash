#!/bin/bash
source source.bash
python misery.py
./ftp.bash --dir $REMOTE_DIR/output --host $REMOTE_HOST
./ftp.bash --dir $REMOTE_DIR --source_dir www --host $REMOTE_HOST
