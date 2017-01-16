#!/bin/bash
source source.bash
YEAR=2016
python misery.py --name $YEAR
#python misery.py --name live-20150813 --live
./ftp.bash --dir $REMOTE_DIR/output --host $REMOTE_HOST
curl -X PURGE http://extras.denverpost.com/app/misery-index/
curl -X PURGE http://extras.denverpost.com/app/misery-index/output/$YEAR.json
curl -X PURGE http://extras.denverpost.com/app/misery-index/output/scores.json
