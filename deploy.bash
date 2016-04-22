#!/bin/bash
source source.bash
python misery.py --name 2016
#python misery.py --name live-20150813 --live
./ftp.bash --dir $REMOTE_DIR/output --host $REMOTE_HOST
curl -X PURGE http://extras.denverpost.com/app/misery-index/
curl -X PURGE http://extras.denverpost.com/app/misery-index/output/2016.json
curl -X PURGE http://extras.denverpost.com/app/misery-index/output/scores.json
#./ftp.bash --dir $REMOTE_DIR --source_dir www --host $REMOTE_HOST
#sleep 20
#python misery.py --name live-20150813 --live
#./ftp.bash --dir $REMOTE_DIR/output --host $REMOTE_HOST
