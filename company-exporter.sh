#!/bin/bash

#
#	Company profile export script
#

STAMP=`date +%Y-%m-%d_%H.%M.%S`
HOME=/home/rent
SITE=$HOME/site
RUNDIR=$HOME/company-exporter
LOGDIR=$RUNDIR/log
SCRIPT=$RUNDIR/company-exporter.sh
LOG=$LOGDIR/company-export.$STAMP.log
REPORT=$LOGDIR/company-export.$STAMP.report

OUT_FILE=$RUNDIR/company-export.$STAMP.json

START=`date +%s`

cd $HOME

[ -s $LOG ] && mv $LOG $LOG.bak

#
#	Start the actual export script, results to $OUT_FULE
#

php company-exporter/company-export.php $LOG $REPORT > $OUT_FILE
cp -p $OUT_FILE $OUT_FILE.bak

# at now + "$STARTAT" minutes  2>/dev/null <<EOC
#  $SCRIPT auto >$RUNDIR/exporter.out
# EOC

