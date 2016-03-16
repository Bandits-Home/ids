<?php
@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
ob_implicit_flush(1);
#####################################
# User defined variables
#####################################
$api = "apikeyhere";
$url = "http://nagiosserver.com/";
$autoapply = 0; #auto apply changes when done
#####################################
# Grab variables that were passed
$hostname=$_POST['hostname'];
$address=$_POST['address'];
$cores=$_POST['cores'];
$customer=$_POST['customer'];
$abbr=$_POST['abbr'];

# Begin displaying results of processing new linux host
ob_start();
echo "<br><br>";
#
# Run the plugin update script
echo "Running plugin install script on $address.....: ";
ob_flush();
$output = `/usr/local/nagios/libexec/cdm-deploy.sh $address`;
echo "<font color=green><b>OK</b></font>"."<br>";
ob_flush();
#
# validate NRPE was installed
echo "Checking NRPE on $address.....: ";
$output2 = `/usr/local/nagios/libexec/check_nrpe -H $address`;
if (strpos($output2,'NRPE v') !== false) {
    echo "<font color=green><b>OK</b></font>"."<br>";
    $NRPE="OK";
} else {
    echo "<font color=red><b>FAILED - NRPE not working</b></font>"."<br>";
    $NRPE="FAILED";
    echo "Something went wrong, opening ticket.....:";
    $msg = "Please open a task for Nagios Team to find out why NRPE isn't working for $hostname on $address and to add to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
}
#
#Add to nagios
echo "Everything worked, adding to NagiosXI..... <br>";
ob_flush();
#
# New customer
$new = 0;
if ($customer == 'new') {
    $customer = $abbr;
    $new = 1;
}
$customerupper = strtoupper($customer);
$hosttemplate = $customer . "_generic_host";
$servicetemplate = $customer . "_generic-service-5";
$servicetemplate60 = $customer . "_generic-service-60";
$sg = $customer . "_system_checks";
$hg = $customerupper . "-Linux";
$lw3 = round($cores * 1.5 + 1, 1);
$lw2 = $lw3 + 5;
$lw1 = $lw2 + 5;
$lc3 = round($cores * 2 + 1, 1);
$lc2 = $lc3 + 5;
$lc1 = $lc2 + 5;
$cw1 = 70;
$cw2 = 40;
$cw3 = 60;
$cc1 = 90;
$cc2 = 60;
$cc3 = 70;
#
#
# New customer
if ($new == 1) {
	echo "Templates : ";
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $length = 5;
        $filename = $customer;
        for($i = 0; $i < $length; $i++)
        {
            $filename .= $chars[mt_rand(0, 36)];
        }
        $tmpfname = "/usr/local/nagios/etc/import/" . $filename . ".cfg";
        $handle = fopen($tmpfname, "w") or die("Unable to open file!");
        $cg = $customerupper . "_NAGIOS_ALL_CG";
        # 5 Minute Service Template
        fwrite($handle, "define service {\n");
        fwrite($handle, "name $servicetemplate\n");
        fwrite($handle, "service_description                      Generic $customerupper Service-5 Min\n");
        fwrite($handle, "is_volatile                              0\n");
        fwrite($handle, "max_check_attempts                       3\n");
        fwrite($handle, "check_interval                           5\n");
        fwrite($handle, "retry_interval                           2\n");
        fwrite($handle, "active_checks_enabled                    1\n");
        fwrite($handle, "passive_checks_enabled                   1\n");
        fwrite($handle, "check_period                             24x7\n");
        fwrite($handle, "parallelize_check                        1\n");
        fwrite($handle, "obsess_over_service                      1\n");
        fwrite($handle, "check_freshness                          0\n");
        fwrite($handle, "event_handler_enabled                    1\n");
        fwrite($handle, "flap_detection_enabled                   1\n");
        fwrite($handle, "process_perf_data                        1\n");
        fwrite($handle, "retain_status_information                1\n");
        fwrite($handle, "retain_nonstatus_information             1\n");
        fwrite($handle, "notification_interval                    0\n");
        fwrite($handle, "notification_period                      24x7\n");
        fwrite($handle, "notification_options                     w,c,u,r,\n");
        fwrite($handle, "notifications_enabled                    1\n");
        fwrite($handle, "register                                 0\n");
        fwrite($handle, "}\n");
        # 60 Minute Service Template
        fwrite($handle, "define service {\n");
        fwrite($handle, "name $servicetemplate60\n");
        fwrite($handle, "service_description                      Generic $customerupper Service-60 Min\n");
        fwrite($handle, "is_volatile                              0\n");
        fwrite($handle, "max_check_attempts                       3\n");
        fwrite($handle, "check_interval                           60\n");
        fwrite($handle, "retry_interval                           2\n");
        fwrite($handle, "active_checks_enabled                    1\n");
        fwrite($handle, "passive_checks_enabled                   1\n");
        fwrite($handle, "check_period                             24x7\n");
        fwrite($handle, "parallelize_check                        1\n");
        fwrite($handle, "obsess_over_service                      1\n");
        fwrite($handle, "check_freshness                          0\n");
        fwrite($handle, "event_handler_enabled                    1\n");
        fwrite($handle, "flap_detection_enabled                   1\n");
        fwrite($handle, "process_perf_data                        1\n");
        fwrite($handle, "retain_status_information                1\n");
        fwrite($handle, "retain_nonstatus_information             1\n");
        fwrite($handle, "notification_interval                    0\n");
        fwrite($handle, "notification_period                      24x7\n");
        fwrite($handle, "notification_options                     w,c,u,r,\n");
        fwrite($handle, "notifications_enabled                    1\n");
        fwrite($handle, "register                                 0\n");
        fwrite($handle, "}\n");
        # Host Template
        fwrite($handle, "define host {\n");
        fwrite($handle, "name                                     $hosttemplate\n");
        fwrite($handle, "check_command                            check_xi_host_ping!3000.0!80%!5000.0!100%!!!!\n");
        fwrite($handle, "max_check_attempts                       2\n");
        fwrite($handle, "check_interval                           5\n");
        fwrite($handle, "retry_interval                           1\n");
        fwrite($handle, "check_period                             xi_timeperiod_24x7\n");
        fwrite($handle, "event_handler_enabled                    1\n");
        fwrite($handle, "flap_detection_enabled                   1\n");
        fwrite($handle, "process_perf_data                        1\n");
        fwrite($handle, "retain_status_information                1\n");
        fwrite($handle, "retain_nonstatus_information             1\n");
        fwrite($handle, "contact_groups                           $cg\n");
        fwrite($handle, "notification_interval                    0\n");
        fwrite($handle, "notification_period                      xi_timeperiod_24x7\n");
        fwrite($handle, "notifications_enabled                    1\n");
        fwrite($handle, "register                                 0\n");
        fwrite($handle, "}\n");
        # Contact Group
        fwrite($handle, "define contactgroup {\n");
        fwrite($handle, "contactgroup_name                       $cg\n");
        fwrite($handle, "alias                                   $customerupper Global Nagios Contact Group\n");
        fwrite($handle, "members                                 sdnagios\n");
        fwrite($handle, "}\n");
	# servicegroup
	fwrite($handle, "define servicegroup {\n");
	fwrite($handle, "servicegroup_name                        $sg\n");
	fwrite($handle, "alias                                    $customerupper System Level Checks-CPU, Disk, Ram\n");
	fwrite($handle, "}\n");
        # hostgroup
        fwrite($handle, "define hostgroup {\n");
        fwrite($handle, "hostgroup_name                        $hg\n");
        fwrite($handle, "alias                                    $customerupper Linux Hosts\n");
        fwrite($handle, "}\n");
        # Close File
        fclose($handle);
	$command = $url . "nagiosxi/api/v1/system/importconfig?apikey=" . $api . "&pretty=1";
	$result=`curl -k -XGET "$command"`;
	if (strpos($result,'error') !== false) {
		echo "<font color=red><b>FAILED</b></font>"."<br>";
		echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
		$msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
		ob_flush();
		emailcms($msg);
	} else {
		echo "<font color=green><b>OK : $result</b></font>"."<br>";
		ob_flush();
	}
}
#
# Exit here for testing the "NEW" customer functions
#
#ob_end_flush();
#exit;
#
#
# Add the Host
#
###############################################
# Host
###############################################
echo "Host : ";
$command = $url . "nagiosxi/api/v1/config/host?apikey=" . $api . "&pretty=1";
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&address=$address&hostgroups=%2B$hg&use=$hosttemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
   echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
#
#
#Add Services / %2C = , and %21 = ! and %25 = % and %2F = /
#
###############################################
# Load
###############################################
echo "Load : ";
ob_flush();
$command = $url . "nagiosxi/api/v1/config/service?apikey=" . $api . "&pretty=1";
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Load&check_command=check_by_nrpe_load%21$lw1%2C$lw2%2C$lw3%21$lc1%2C$lc2%2C$lc3%21%21%21%21%21%21&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# CPU
###############################################
echo "CPU : ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=CPU&check_command=check_by_nrpe_cpu%21$cw1%2C$cw2%2C$cw3%21$cc1%2C$cc2%2C$cc3&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# VNC
###############################################
echo "VNC : ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=VNC%20Running&check_command=check_nrpe%21check_vnc&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Secure Log
###############################################
echo "/var/log/secure : ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Log%20File%20-%20%2fvar%2flog%2fsecure&check_command=check_by_nrpe_logfile%21%2Fusr%2flocal%2Fnagios%2flibexec%2fsecure_log.cfg&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Disk - APPLCSF
###############################################
echo "Disk - APPLCSF: ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Disk%20-%20APPLCSF&check_command=check_by_nrpe_disk_applcsf%2120%25%2110%25&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Disk - APPL_TOP and COMMON_TOP and Binaries
###############################################
echo "Disk - APPL_TOP and COMMON_TOP and Binaries: ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Disk%20-%20APPL_TOP%20and%20COMMON_TOP%20and%20Binaries&check_command=check_by_nrpe_disk_appltop%2110%25%218%25&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Disk - Archive Log
###############################################
echo "Disk - Archive Log: ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Disk%20-%20Archive%20Log&check_command=check_by_nrpe_disk_archive%2130%25%2120%25&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Disk - Backup
###############################################
echo "Disk - Backup: ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Disk%20-%20Backup&check_command=check_by_nrpe_disk_backup%218%25%212%25&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Disk - Data
###############################################
echo "Disk - Data: ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Disk%20-%20Data&check_command=check_by_nrpe_disk_data%2110%25%218%25&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Disk - Hosting
###############################################
echo "Disk - Hosting: ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Disk%20-%20Hosting&check_command=check_by_nrpe_disk_hosting%2110%25%218%25&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Disk - Miscellaneous
###############################################
echo "Disk - Miscellaneous: ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Disk%20-%20Miscellaneous&check_command=check_by_nrpe_disk_misc%2110%25%218%25&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Disk - Patch
###############################################
echo "Disk - Patch: ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Disk%20-%20Patch&check_command=check_by_nrpe_disk_patch%2110%25%218%25&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Disk - Redo Log
###############################################
echo "Disk - Redo Log: ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Disk%20-%20Redo%20Log&check_command=check_by_nrpe_disk_redo%2115%25%2110%25&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Disk - Software
###############################################
echo "Disk - Software: ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Disk%20-%20Software&check_command=check_by_nrpe_disk_software%2110%25%218%25&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Disk - Tempspace
###############################################
echo "Disk - Tempspace: ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Disk%20-%20Tempspace&check_command=check_by_nrpe_disk_temp%2115%25%2110%25&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Cron
###############################################
echo "Cron : ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Cron%20Scheduler&check_command=check_by_nrpe_init_service%21crond&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# /var/log/messages
###############################################
echo "/var/log/messages : ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Log%20File&check_command=check_by_nrpe_log%21%2Fvar%2flog%2Fmessages%21Error%21most_recent&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Memory
###############################################
echo "Memory : ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Memory&check_command=check_by_nrpe_memory%2110%215&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# NFS
###############################################
echo "NFS : ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=NFS&check_command=check_by_nrpe_nfs&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Readonly FS
###############################################
echo "Read Only FS : ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Read-Only%20File%20System&check_command=check_by_nrpe_rofs&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Swap
###############################################
echo "Swap : ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Swap&check_command=check_by_nrpe_swap%2130%2120&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Total Processes
###############################################
echo "Total Procs : ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Total%20Processes&check_command=check_by_nrpe_procs%21900%211200&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
###############################################
# Zombie Processes
###############################################
echo "Zombie Procs : ";
ob_flush();
$result=`curl -k -XPOST "$command" -d "host_name=$hostname&service_description=Zombie%20Processes&check_command=check_by_nrpe_procs%215%2110%21-s%20Z&servicegroups=$sg&use=$servicetemplate&force=1"`;
if (strpos($result,'error') !== false) {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    echo "Something went wrong adding to NagiosXI, emailing CMS to open a task for Nagios.....:";
    $msg = "Please open a task for Nagios Team to check why $hostname on $address failed to get added to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
} else {
    echo "<font color=green><b>OK : $result</b></font>"."<br>";
}
#
#
# Apply Changes
if ($autoapply == 1) {
	$command = $url . "nagiosxi/api/v1/system/applyconfig?apikey=" . $api . "&pretty=1";
	$apply = `curl -XGET "$command"`;
	echo "Everything worked and changes applied...";
	ob_end_flush();
	exit;
}
echo "Everything worked, informing SD and Nagios team...";
$msg = "Please open a low priority task for monitoring team: $hostname has been added to NagiosXI";
ob_end_flush();
emailcms($msg);

FUNCTION emailcms(&$message){
    # email sd and nagios admins
    `/usr/local/nagios/libexec/hanotification.sh "$message"`;
    echo "<font color=green><b>OK</b></font>"."<br>";
    ob_end_flush();
    exit;
}
?>
