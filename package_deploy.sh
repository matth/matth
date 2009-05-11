#!/bin/bash

if [ $# -ne 2 ]; then
	echo "Usage: `basename $0` [incoming_dir] [tmp_dir]";
	echo
	echo "Untars any *.tar.gz files it finds in incoming_dir to tmp_dir, looks for deploy.sh in root of tar and runs it"
	echo
else 
	INCOMING_DIR=`dirname $1`/`basename $1`
	TMP_DIR=`dirname $2`/`basename $2`

	for f in $( ls $INCOMING_DIR/*.tar.gz 2> /dev/null ); do
		mv $f $TMP_DIR
		cd $TMP_DIR
		tar -xvzf `basename $f`
		cd -
		source $TMP_DIR/deploy.sh
		rm -rdf $TMP_DIR/*
	done	
fi