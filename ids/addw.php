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
$autoapply = 0; # automatic apply when done
#####################################
# Grab variables that were passed
$hostname=$_POST['hostname'];
$address=$_POST['address'];
$customer=$_POST['customer'];
$abbr=$_POST['abbr'];
#
# Begin displaying results of processing new linux host
ob_start();
echo "<br><br>";
#
# Verify Agent
echo "Verifying Nagios Agent on $address.....: ";
ob_flush();
$output2 = `/usr/local/nagios/libexec/check_nrpe -H $address`;
sleep(1); #pretend its doing something
#
# validate NSCLIENT was installed
if (strpos($output2,'seem to be doing') !== false) {
    echo "<font color=green><b>OK</b></font>"."<br>";
    $NRPE="OK";
} else {
    echo "<font color=red><b>FAILED</b></font>"."<br>";
    $NRPE="FAILED - Agent not working";
    echo "Something went wrong, opening ticket.....:";
    $msg = "Please open a task for Nagios Team to find out why NSClient isn't working for Windows host $hostname on $address and to add to Nagios.  Thank you!";
    ob_flush();
    emailcms($msg);
}
#
#Add to nagios
echo "Everything worked, adding to NagiosXI.....<br>";
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
$sg = $customer . "_system_checks";
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
# Add the Host
#
###############################################
# Host
###############################################
$result = array();
# Add Host
$command = $url . "nagiosxi/api/v1/config/host?apikey=" . $api . "&pretty=1";
$result[]=`curl -XPOST "$command" -d "host_name=$hostname&address=$address&use=$hosttemplate&force=1"`;
#
#Add Services / %2C = , and %21 = ! and %25 = % and %2F = /
#
# CPU
$command = $url . "nagiosxi/api/v1/config/service?apikey=" . $api . "&pretty=1";
$result[]=`curl -XPOST "$command" -d "host_name=$hostname&service_description=CPU&check_command=check_nrpe%21alias_cpu_ex%21-a%2070%2090&servicegroups=$sg&use=$servicetemplate&force=1"`;
# Disk
$result[]=`curl -XPOST "$command" -d "host_name=$hostname&service_description=Disk&check_command=check_nrpe%21alias_disk_ex%21-a%2015%2010&servicegroups=$sg&use=$servicetemplate&force=1"`;
# Memory
$result[]=`curl -XPOST "$command" -d "host_name=$hostname&service_description=Memory&check_command=check_nrpe%21alias_mem_ex%21-a%2090%2095&servicegroups=$sg&use=$servicetemplate&force=1"`;
#
# Test for any failures
foreach ($result as $temp){
    #echo $temp;
    if (strpos($temp,'error') !== false) {
        echo "<font color=red><b>FAILED</b></font>"."<br>";
        echo "Something went wrong adding to NagiosXI, emailing CMS CMS to open a task for Nagios.....:";
        $msg = "Please open a task for Nagios Team to check why Windows host $hostname on $address failed to get added to Nagios.  Thank you!";
        ob_flush();
        emailcms($msg);
    }
}
echo "<font color=green><b>OK</b></font>"."<br>";
# Auto Apply changes
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
