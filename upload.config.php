<?php
include 'admin_header.php';
include '../../mainfile.php';
require XOOPS_ROOT_PATH . '/modules/xadmintools/includes/upload.inc.php';

require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
    xoops_cp_header();
    include 'navigation.php';
        OpenTable();
?>


<?php function xoopsuploadshow()
{
    require XOOPS_ROOT_PATH . '/modules/xadmintools/includes/upload.inc.php';

    global $file_size_ind, $dir_store, $file_ext_allow, $file_list_allow, $file_del_allow; ?>
<html>
<head>
<title>Module Upload Configuration Settings</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td>
<form name="form1" method="post" action="upload.config.php">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="19%">File Size Limit</td>
            <td colspan="4"> <input name="file_size_ind" type="text" id="file_size_ind" value="<?php echo $file_size_ind; ?>">
              Bytes</td>
          </tr>
          <tr> 
            <td>Upload Directory:</td>
            <td colspan="4">&nbsp;</td>
          </tr>
          <tr> 
            <td><strong><font color="#FF0000"><?php echo XOOPS_ROOT_PATH . '/'; ?></font></strong></td>
            <td colspan="4"> <strong><font color="#FF0000"> 
              <input name="dir_store" type="text" id="dir_store" value="<?php echo $xpath; ?>">
              </font></strong></td>
          </tr>
          <tr> 
            <td>Extensions Allowed:<font color="#FF0000"> 
              <?php
    for ($i = 0, $iMax = count($file_ext_allow); $i < $iMax; $i++) {
        if (($i != count($file_ext_allow) - 1)) {
            $commas = ', ';
        } else {
            $commas = '';
        }

        [$key, $value] = each($file_ext_allow);

        echo $value . $commas;
    } ?>
              </font> </td>
            <td colspan="4"> <select name="extensions" id="extensions">
                <option value="'zip'" selected <?php if (!(strcmp('zip', (string)$value))) {
        echo 'SELECTED';
    } ?>>Zip</option>
                <option value="'gz'" <?php if (!(strcmp('gz', (string)$value))) {
        echo 'SELECTED';
    } ?>>Tar.gz</option>
                <option value="'zip','gz'" <?php if (!(strcmp('zip', 'gz', (string)$value))) {
        echo 'SELECTED';
    } ?>>Zip 
                / Tar.gz</option>
              </select> </td>
          </tr>
          <tr> 
            <td>Show File List</td>
            <td width="1%"> <input  <?php if (!(strcmp($file_list_allow, '1'))) {
        echo 'CHECKED';
    } ?> type="radio" name="filelist" value="1"></td>
            <td width="2%">Yes</td>
            <td width="1%"> <input  <?php if (!(strcmp($file_list_allow, '0'))) {
        echo 'CHECKED';
    } ?> type="radio" name="filelist" value="0"></td>
            <td width="77%">No</td>
          </tr>
          <tr> 
            <td>Allow Files To Be Deleted</td>
            <td><input   <?php if (!(strcmp($file_del_allow, '1'))) {
        echo 'CHECKED';
    } ?> type="radio" name="filedelete" value="1"></td>
            <td>Yes</td>
            <td><input   <?php if (!(strcmp($file_del_allow, '0'))) {
        echo 'CHECKED';
    } ?> type="radio" name="filedelete" value="0"></td>
            <td>No</td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td colspan="4">&nbsp;</td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td colspan="4">&nbsp;</td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td colspan="4">&nbsp;</td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td colspan="4">&nbsp;</td>
          </tr>
          <tr> 
            <td><input type="submit" name="Submit" value="Submit"> <input name="op" type="hidden" id="op" value="xoopsuploadConfigEdit"></td>
            <td colspan="4">&nbsp;</td>
          </tr>
        </table>
</form></td>
</tr>
</table>
</body>
</html>
<?php
        CloseTable();

    xoops_cp_footer();
}

function xoopsuploadConfigEdit()
{
    global $_POST;

    $xfile_size_ind = $_POST['file_size_ind'];

    $xdir_store = $_POST['dir_store'];

    $x_extensions = stripslashes($_POST['extensions']);

    $xfile_list = $_POST['filelist'];

    $xfile_delete = $_POST['filedelete'];

    $filename = XOOPS_ROOT_PATH . '/modules/xadmintools/includes/upload.inc.php';

    $xextensions = stripslashes($x_extensions);

    $file = fopen($filename, 'wb');

    $content .= '';

    $content .= "<?php\n";

    $content .= "\n";

    $content .= "#---------------------------------------------#\n";

    $content .= "# file upload manager 1.3\n";

    $content .= "# 13.10.2003\n";

    $content .= "# \n";

    $content .= "# written by thepeak (adam johnson)\n";

    $content .= "# copyright (c) 2003 thepeak of mtnpeak.net\n";

    $content .= "#\n";

    $content .= "# www: http://webdev.mtnpeak.net\n";

    $content .= "# www: http://www.xd3v.com\n";

    $content .= "# email: thepeak@gmx.net\n";

    $content .= "#\n";

    $content .= "# A simple, powerful tool to upload and manage \n";

    $content .= "# files using your web browser.\n";

    $content .= "# \n";

    $content .= "# This program is free software; you can redistribute \n";

    $content .= "# it and/or modify it under the terms of the GNU General \n";

    $content .= "# Public License as published by the Free Software \n";

    $content .= "# Foundation; either version 2 of the License, or \n";

    $content .= "# (at your option) any later version, as long as the \n";

    $content .= "# copyright info and links stay intact. You may not sell\n";

    $content .= "# this program under any circumstance without written \n";

    $content .= "# permission from the author. Full license is in the \n";

    $content .= "# included ZIP/GZ package this script was downloaded in.\n";

    $content .= "#\n";

    $content .= "# *please send me feedback - and enjoy!\n";

    $content .= "#---------------------------------------------#\n";

    $content .= "################## configurations ####################\n";

    $content .= "\$xpath = \"$xdir_store\";\n";

    $content .= "# header & title of this file\n";

    $content .= "\$title = \"File Upload Manager\";\n";

    $content .= "# individual file size limit - in bytes (102400 bytes = 100KB)\n";

    $content .= "\$file_size_ind = \"$xfile_size_ind\";\n";

    $content .= "# the upload store directory (chmod 777)\n";

    $content .= "\$dir_store= XOOPS_ROOT_PATH.\"/$xdir_store\";\n";

    $content .= "# the images directory\n";

    $content .= "\$dir_img= \"img\";\n";

    $content .= "# the style-sheet file to use (located in the \"img\" directory, excluding .css)\n";

    $content .= "\$style = \"style-def\";\n";

    $content .= "# the file type extensions allowed to be uploaded\n";

    $content .= "\$file_ext_allow = array($xextensions);\n";

    $content .= "# option to display the file list\n";

    $content .= "# to enable/disable, enter '1' to ENABLE or '0' to DISABLE (without quotes)\n";

    $content .= "\$file_list_allow = $xfile_list;\n";

    $content .= "# option to allow file deletion\n";

    $content .= "# to enable/disable, enter \"1\" to ENABLE or '0' to DISABLE (without quotes)\n";

    $content .= "\$file_del_allow = $xfile_delete;\n";

    $content .= "# option to password-protect this script [-part1]\n";

    $content .= "# to enable/disable, enter \"1\" to ENABLE or \"0\" to DISABLE (without quotes)\n";

    $content .= "\$auth_ReqPass = 0;\n";

    $content .= "# option to password-protect this script [-part2]\n";

    $content .= "# if \"$auth_ReqPass\" is enabled you must set the username and password\n";

    $content .= "\$auth_usern = \"username\";\n";

    $content .= "\$auth_passw = \"password\";\n";

    $content .= "################ end of configurations ###############\n";

    $content .= "?>\n";

    fwrite($file, $content);

    fclose($file);

    redirect_header('index.php', 1, 'Xoops Upload configurations Successfully Edited');
}
?>


<?php
switch ($op) {
        default:
            xoopsuploadshow();
            break;
        case 'xoopsuploadConfigEdit':
            if (xoopsfwrite()) {
                xoopsuploadConfigEdit();
            }
            break;
}
?>
