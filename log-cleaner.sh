#!/bin/bash

#
#	Cleans log files. Archives current log file to *.<yesterday> and clears it.
#	Logs and reports older than a certain amount of days are discarded.
#
#	Put this script in cron to be run everyday just after midnight
#

DAYS=7										# The no of days the reps/logs will be preserved
YESTERDAY=`php /data/company-exporter/current/yesterday.php`

HOME=/data
LOGDIR=$HOME/shared/log
LOG=$LOGDIR/company-export.log
REPORT=$LOGDIR/company-export.report
LOGMAINTREP=$LOGDIR/log_maintenance_report

# echo $YESTERDAY
# echo $LOG
# echo $REPORT

echo `date` "Cleaning $LOG" >> $LOGMAINTREP

if [ -f $LOG.$YESTERDAY ]
then
	  echo `date` "File $LOG.$YESTERDAY already exists - skipped cleaning this time" >> $LOGMAINTREP
		  exit
		fi

cp -p $LOG $LOG.$YESTERDAY
cp -p $REPORT $REPORT.$YESTERDAY
> $LOG
> $REPORT
# ls -l $LOG*

# Remove older log archives

find /data/company-exporter/shared/log/export* -type f -mtime +$DAYS -exec rm {} \;

