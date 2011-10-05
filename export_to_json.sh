#
#	Exporter script
#

HOME=~/property-importer/property-exporter
LOG=$HOME/export.log
OUTPUT=$HOME/export.json

cd $HOME

[ -s $LOG ] && mv $LOG $LOG.bak
php ./export.php $* > $OUTPUT
cp -p $OUTPUT $OUTPUT.bak
