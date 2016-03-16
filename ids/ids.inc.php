<?php
//
//

// include the helper file
require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

// respect the name
$ids_component_name = "ids";

// run the initialization function
ids_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function ids_component_init()
{
    global $ids_component_name;

    //boolean to check for latest version
    $versionok = ids_component_checkversion();

    //component description
    $desc = gettext("This component allows advanced users to add new hosts.");

    if (!$versionok)
        $desc = "<b>" . gettext("Error: This component requires Nagios XI 2009R1.2B or later.") . "</b>";

    //all components require a few arguments to be initialized correctly.
    $args = array(

        // need a name
        COMPONENT_NAME => $ids_component_name,
        COMPONENT_VERSION => '1.0',
        COMPONENT_DATE => '10/06/2015',

        // informative information
        COMPONENT_AUTHOR => "IT Convergence",
        COMPONENT_DESCRIPTION => $desc,
        COMPONENT_TITLE => "ITC Deploy New Host",
    );

    // Register this component with XI
    register_component($ids_component_name, $args);

    // Register the addmenu function
    if ($versionok) {
        register_callback(CALLBACK_MENUS_INITIALIZED, 'ids_component_addmenu');
    }
}


///////////////////////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////

function ids_component_checkversion()
{

    if (!function_exists('get_product_release'))
        return false;
    //requires greater than 2009R1.2
    if (get_product_release() < 114)
        return false;

    return true;
}

function ids_component_addmenu($arg = null)
{
    global $autodiscovery_component_name;
    global $ids_component_name;
    //retrieve the URL for this component
    $urlbase = get_component_url_base($ids_component_name);
    //figure out where I'm going on the menu
    $mi = find_menu_item(MENU_CONFIGURE, "menu-configure-section-wizards", "id");
    if ($mi == null) //bail if I didn't find the above menu item
        return;

    $order = grab_array_var($mi, "order", ""); //extract this variable from the $mi array
    if ($order == "")
        return;

    $neworder = $order + 1; //determine my menu order

    //add this to the main home menu
    add_menu_item(MENU_CONFIGURE, array(
        "type" => "link",
        "title" => gettext("ITC Deploy New Host"),
        "id" => "menu-configure-ids",
        "order" => $neworder,
        "opts" => array(
            //this is the page the menu will actually point to.
            //all of my actual component workings will happen on this script
            "href" => $urlbase . "/ids.php"
        ),
        "function" => "is_advanced_user"
    ));

}

FUNCTION ids_get_hosttemplate_option_list(){
    $option_list = '';
    $hosttemplates = exec_sql_query(DB_NAGIOSQL, "SELECT `template_name` FROM nagiosql.tbl_hosttemplate ORDER BY `template_name`;", true);
    foreach($hosttemplates as $data){
        $data =(array)$data;
        print_r($data);
          $htlist[] = $data['template_name'];
    }
    natcasesort($htlist);
    foreach($htlist as $temp){
	$end = substr($temp, -12);
        $nhtest = substr($temp, -15);
	if ( $end == 'generic_host' && substr( $temp, 0, 8 ) !== 'xiwizard' && $nhtest !== 'nh_generic_host' ){
		$arr = explode("_", $temp, 2);
		$cust = $arr[0];
	        $option_list .='<option value="'.$cust.'">'.$cust.'</option>';
	}
    }
    return $option_list;
}
