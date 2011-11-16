#!/bin/bash

#
#	Company profile export script
#

STAMP=`date +%Y-%m-%d_%H.%M.%S`
HOME=/data
SITE=$HOME/rent/current
RUNDIR=$HOME/company-exporter/current
LOGDIR=$HOME/shared/log
SCRIPT=$RUNDIR/company-exporter.sh
LOG=$LOGDIR/company-export.log
REPORT=$LOGDIR/company-export.report
IMPORT_ENVIRONMENT=$1
LOCK_FILE=/tmp/exporter.lock
RUN_FILE=/tmp/exporter.run

OUT_FILE=$LOGDIR/company-export.$STAMP.json

START=`date +%s`

#
#	Start the actual export script, results to $OUT_FILE
#

php $RUNDIR/company-export.php $LOG $REPORT > $OUT_FILE
cd $SITE/script

./start-company-import-$IMPORT_ENVIRONMENT.sh $OUT_FILE $LOG

#
# Use 'at' to schedule the nex execution. Use the 'c' queue instead of the
# default 'a' queue
#

END=`date +%s`
DURATION=`expr $END - $START`
STARTAT=`expr 3600 - $DURATION`     # Minimum interval is 1 hour
STARTAT=`expr $STARTAT / 60`      	# Seconds -> minutes
STARTAT=`expr $STARTAT + 1`     		# Add 1 minute for rounding
[ $STARTAT -lt 0 ] && STARTAT=1     # Minimum is 1 min, cant be < 0

at now + "$STARTAT" minutes -q c 2>/dev/null <<EOC
	$SCRIPT auto >$RUNDIR/exporter.out
EOC

