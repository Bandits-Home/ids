<?php
#####################################
# User defined variables
#####################################
$api = "apikeyhere";
$url = "http://nagiosserver.com/";
#####################################
# Apply Changes
$command = $url . "nagiosxi/api/v1/system/applyconfig?apikey=" . $api . "&pretty=1";
`curl -XGET "$command"`;
echo "Config Applied";
?>
