#!/bin/bash
source source.bash
python misery.py --name 2016
#python misery.py --name live-20150813 --live
./ftp.bash --dir $REMOTE_DIR/output --host $REMOTE_HOST
#./ftp.bash --dir $REMOTE_DIR --source_dir www --host $REMOTE_HOST
#sleep 20
#python misery.py --name live-20150813 --live
#./ftp.bash --dir $REMOTE_DIR/output --host $REMOTE_HOST
