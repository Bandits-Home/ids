#!/bin/sh
# This is a sample shell script showing how you can submit the SEND_CUSTOM_SVC_NOTIFICATION command
# to Nagios.  Adjust variables to fit your environment as necessary.

now=`date +%s`
commandfile='/usr/local/nagios/var/rw/nagios.cmd'

/usr/bin/printf "[%lu] PROCESS_SERVICE_CHECK_RESULT;localhost;Host Automation;2;$1\n" $now > $commandfile
sleep 3
/usr/bin/printf "[%lu] PROCESS_SERVICE_CHECK_RESULT;localhost;Host Automation;0;OK - Resetting\n" $now > $commandfile
