<?php
//
// ITC Deploy Server
//

require_once(dirname(__FILE__) . '/../../common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and do prereq/auth checks
grab_request_vars();
check_prereqs();
check_authentication(false);

$title = gettext("Nagios XI - ITC Deploy New Host");

do_page_start(array("page_title" => $title), true);
?>

<style type='text/css'>
select {
    font-family: 'Courier New';
}
</style>
<?php

ids_show_main();

function ids_show_main()
{
    global $request;
    global $lstr;
    #do_page_start(array("page_title" => $lstr['ITCDeployServer']), true);
    $showall = true;
    ?>
    <h1>Add new host</h1>
    <b><a href="https://put.link.here">SOP For Using This Page</a></b><p>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#tabs").tabs();
        });
    </script>

    <div id="tabs">
    <ul>
        <li><a href="#linux-tab"><?php echo gettext("Linux"); ?></a></li>
        <li><a href="#windows-tab"><?php echo gettext("Windows"); ?></a></li>
    </ul>

<!-- Linux Tab Start -->
    <div id='linux-tab'>
<!--    <div class="infotable_title" style="clear: left; margin: 0 0 10px 0;"><?php echo "THIS PAGE UNDER CONSTRUCTION!"; ?></div> -->
    <div style="clear: left; margin: 0 0 10px 0;">
        <?php echo get_nagios_session_protector(); ?>
	<table class="table table-condensed table-no-border table-auto-width table-padded">
        <tr><form name="myformlinux" action="addl.php" method="post">
                <tr>
                    <td class="vt">
                        <label for="hostname">Host Name</label>
                    </td>
                    <td>
                        <input type="text" size="40" name="hostname" id="hostname" required placeholder="Hostname(Not FQDN)..." class="textfield form-control">
                        <div class="subtext">The hostname of the server.  Do not include the FQDN</div>
                    </td>
                </tr>
                <tr>
                    <td class="vt">
                        <label for="address">IP Address</label>
                    </td>
                    <td>
                        <input type="text" size="40" name="address" id="address" required placeholder="IP Address..." class="textfield form-control">
                        <div class="subtext">The IP Address of the server.</div>
                    </td>
                </tr>
                <tr>
                    <td class="vt">
                        <label for="cores"># of CPU Cores</label>
                    </td>
                    <td>
                        <input type="text" size="10" name="cores" id="cores" required placeholder="# of Cores..." class="textfield form-control">
                        <div class="subtext">The number of CPU Cores.</div>
                    </td>
                </tr>
		<tr>
                    <td class="vt">
                        <label for="customer">Customer</label>
                    </td>
                <td>
	                <select name="customer" id="customer" style="display:inline-block;width:auto;min-width:150px;font-family:Courier New;" size=1 required>
                                <option value disabled selected> -- select an option -- </option>
                                <?php $options = ids_get_hosttemplate_option_list();echo $options;?>
                                <option value="new">New Customer - Not Listed </option></select>
                                <div class="subtext">The customer nagios abbreviation.</div>
		</td>
		</tr>
                <tr>
                    <td></td><td>
                        <input type="text" size="20" name="abbr" id="abbr" placeholder="Abbreviation if new..." class="textfield form-control">
                        <div class="subtext">The abbreviation if "New" selected above!</div>
                    </td>
                </tr>
		<tr><td><input type="submit" value="Submit"></td></tr>
	</form></tr>
        </table>
    </div>
    </div>
<!-- Linux Tab End -->

<!-- Windows Tab Start -->
    <div id='windows-tab'>
<!--    <div class="infotable_title" style="clear: left; margin: 0 0 10px 0;"><?php echo "THIS PAGE UNDER CONSTRUCTION!"; ?></div> -->
    <div style="clear: left; margin: 0 0 10px 0;">
        <table class="table table-condensed table-no-border table-auto-width table-padded">
        <tr><form name="myformwindows" action="addw.php" method="post">
                <tr>
                    <td class="vt">
                        <label for="hostname">Host Name</label>
                    </td>
                    <td>
                        <input type="text" size="40" name="hostname" id="hostname" required placeholder="Hostname(Not FQDN)..." class="textfield form-control">
                        <div class="subtext">The hostname of the server.  Do not include the FQDN</div>
                    </td>
                </tr>
                <tr>
                    <td class="vt">
                        <label for="address">IP Address</label>
                    </td>
                    <td>
                        <input type="text" size="40" name="address" id="address" required placeholder="IP Address..." class="textfield form-control">
                        <div class="subtext">The IP Address of the server.</div>
                    </td>
                </tr>
                <tr>
                    <td class="vt">
                        <label for="customer">Customer</label>
                    </td>
                <td>
                        <select name="customer" id="customer" style="display:inline-block;width:auto;min-width:150px;font-family:Courier New;" size=1 required>
                                <option value disabled selected> -- select an option -- </option>
                                <?php $options = ids_get_hosttemplate_option_list();echo $options;?>
                                <option value="new">New Customer - Not Listed </option></select>
                                <div class="subtext">The customer nagios abbreviation.</div>
                </td>
                </tr>
		<tr>
		    <td></td><td>
			<input type="text" size="20" name="abbr" id="abbr" placeholder="Abbreviation if new..." class="textfield form-control">
                        <div class="subtext">The abbreviation if "New" selected above!</div>
		    </td>
		</tr>
                <tr><td><input type="submit" value="Submit"></td></tr>
        </form></tr>
        </table>
    </div>
    </div>
<!-- Windows Tab End -->

<?php { ?>
</div> <?php }
do_page_end(true);
}
//
// end function ids_show_main()
