<?php
// ------------------------------------------------------------------------- //
//                XOOPS - PHP Content Management System                      //
//                       <https://www.xoops.org>                             //
// ------------------------------------------------------------------------- //
// Based on:								     //
// myPHPNUKE Web Portal System - http://myphpnuke.com/	  		     //
// PHP-NUKE Web Portal System - http://phpnuke.org/	  		     //
// Thatware - http://thatware.org/					     //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------- //
//  Xoops Mainfile Administration                                                               //
//  by Tim Lunceford (Widowmaker)                                                         //
//  The Spider Web Network                                                    //
//  http://www.tswn.com                                               //
//                                                       //
// ------------------------------------------------------------------------- //

include 'admin_header.php';
include '../../mainfile.php';

require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';

$myts = MyTextSanitizer::getInstance();
$eh = new ErrorHandler();

function xoopsmainfileconfig()
{
    global $xoopsDB, $xxmysqldb_name, $xxmysqldb_host, $xxmysqldb_password, $xxmysqldb_uname, $login_id, $date_started;

    xoops_cp_header();

    include 'navigation.php';

    OpenTable();

    if (is_writable(XOOPS_ROOT_PATH . '/mainfile.php')) {
        xoops_error(sprintf(_WARNINWRITEABLE, XOOPS_ROOT_PATH . '/mainfile.php'));

        echo '<br>';
    }

    /*if ( is_writable(XOOPS_ROOT_PATH."/mainfile_test.php" ) ) {
        xoops_error(sprintf(_WARNINWRITEABLE,XOOPS_ROOT_PATH.'/mainfile_test.php'));
        echo "<br>";
    }*/

    echo '<h4>Xoops Mainfile.php Configuration Settings</h4><br>'; ?>
<?php
$file = '../../mainfile.php';

    if (empty($save)) {
        $fp = fopen($file, r);

        $content = fread($fp, filesize($file));

        $content = preg_replace('<', '&#60;', $content);

        $content = preg_replace('>', '&#62;', $content);

        fclose($fp); ?>
	<?php
    if (is_writable(XOOPS_ROOT_PATH . '/mainfile.php')) {
        ?>
	<style type="text/css">	
	input.Radio.special {
	background: #2F5376;
	font: 11px verdana, arial, helvetica, sans-serif;
	border: 0px solid #000000;
}
textarea.wide { border: #000000 1px solid; width: 800px; font: 11px verdana, arial, helvetica, sans-serif;}
hr.special { height: 3px; border: 1px ridge #E18A00; width: 99%;}
</style>
<form name="form2" method="post" action="mainfile.config.php">
  <table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr> 
      <td> <table width="75%" border="0" class="bg3" cellspacing="0" cellpadding="3">
          <tr> 
            <td width="20%"> <input name="xoops_root_path" type="text" id="xoops_root_path" value="<?php echo XOOPS_ROOT_PATH ?>"></td>
            <td width="80%">Xoops Root Path</td>
          </tr>
          <tr> 
            <td> <input name="xoops_url" type="text" id="xoops_url" value="<?php echo XOOPS_URL ?>"></td>
            <td>Xoops Url</td>
          </tr>
          <tr> 
            <td> 
              <input name="xoops_db_pass" type="password" id="xoops_db_pass" value="<?php echo XOOPS_DB_PASS ?>">
            </td>
            <td>DB Pass</td>
          </tr>
          <tr> 
            <td> <input name="xoops_db_user" type="text" id="xoops_db_user" value="<?php echo XOOPS_DB_USER ?>"></td>
            <td>DB Username</td>
          </tr>
          <tr> 
            <td> <input name="xoops_db_name" type="text" id="xoops_db_name" value="<?php echo XOOPS_DB_NAME ?>"></td>
            <td>DB Name</td>
          </tr>
          <tr>
            <td><font size="2">
              <input type="hidden" name="op" value="xoopspphloggerConfigEditWrite">
              </font></td>
            <td><input type="submit" name="Submit2" value="Save Config"></td>
          </tr>
        </table>
        <font size="2">&nbsp; </font></td>
    </tr>
  </table>
</form>
<p>
  <?php
    } else {
        ?>
</p>
<table width="99%" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#666666" class="bg1">
  <tr><td> 
	<p>&nbsp;</p>
<table width="97%" border="1" class="bg3" align="center" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
        
<tr>
	
<td> 
    <form method="post" action="mainfile.config.php">
	<font size="2">
<input type="hidden" name="op" value="confirm">
	
</font><font size="3">
<center>
<font size="2">Use this form to edit your mainfile.php file.
	Make sure to edit the lines that pertain only <br>
to your database hostname, database name, database username, and database password.
</font>
</center>
<font size="2"></p>
</font>    </font>
<center>
                <font size="2">
</font>
<table width="90%" border="1" cellspacing="0" cellpadding="0">
<tr>
<td>
Is <?php echo $file ?> writeable?  
<?php if (is_writable(XOOPS_ROOT_PATH . '/mainfile.php')) {?>
<font color="#00FF00" size="3"><strong>YES</strong></font>
<?php } else { ?>
<font color="#FF0000" size="4" face="Arial, Helvetica, sans-serif"><strong>NO</strong></font>
<div align="left"></div></td>
<?php } ?>

</tr>
</table>
<font size="2">
<br>
 
    
<input name="save" type="submit" value="Make Writeable" class="button">
&nbsp;
                </font>
</center>
</form></td>
</tr>
</table>
<p>&nbsp;</p></td>
</tr>
</table>
</td>
</tr>
<?php
    } ?>

<?php
    }

    if (is_writable(XOOPS_ROOT_PATH . '/mainfile.php')) {
        ?>

<?php

        CloseTable();

        xoops_cp_footer();
    }
}
?>
<?php
function confirm()
{
    xoops_cp_header();

    xoops_confirm(['op' => 'xoopsmainfilemakewriteable', 'id_art' => $id_art, 'ok' => 1], 'mainfile.config.php', 'Are you sure you want to make this file writable?');

    xoops_cp_footer();
}

        function confirmwrite()
        {
            xoops_cp_header();

            global $_POST;

            $xoops_root_path = $_POST['xoops_root_path'];

            $xoops_url = $_POST['xoops_url'];

            $xoops_db_pass = $_POST['xoops_db_pass'];

            $xoops_db_user = $_POST['xoops_db_user'];

            $xoops_db_name = $_POST['xoops_db_name'];

            xoops_confirm(['op' => 'xoopspphloggerConfigEditWrite', 'id_art' => $id_art, 'ok' => 1], 'mainfile.config.php', 'Are you sure you of your settings?  Please confirm below.');

            echo '<br><table width="100%" border="1" align="center" class="bg2" cellpadding="0" cellspacing="0">';

            echo '<tr>';

            echo '<td>';

            echo  '<ul><li><font color=#FFFFFF size=2>Xoops URL: <b>';

            echo XOOPS_URL;

            echo '</b></li></ul></font><br>';

            echo '<ul><li><font color=#FFFFFF size=2>Xoops Root Path: <b>';

            echo XOOPS_ROOT_PATH;

            echo '</b></li></ul></font><br>';

            echo '<ul><li><font color=#FFFFFF size=2>Xoops DB Name: <b>';

            echo XOOPS_DB_NAME;

            echo '</b></li></ul></font><br>';

            echo '<ul><li><font color=#FFFFFF size=2>Xoops DB Password: <b>';

            echo XOOPS_DB_PASS;

            echo '</b></li></ul></font><br>';

            echo '<ul><li><font color=#FFFFFF size=2>Xoops DB Username: <b>';

            echo XOOPS_DB_USER;

            echo '</b></li></ul></font><br>';

            echo '</td>';

            echo '</tr>';

            echo '</table>';

            xoops_cp_footer();
        }

        function confirmchmodwin32()
        {
            xoops_cp_header();

            xoops_confirm(['op' => 'win32readonly', 'id_art' => $id_art, 'ok' => 1], 'mainfile.config.php', 'Are you sure you want this file to be chmodded  775 for a Windows System?');

            xoops_cp_footer();
        }

        function confirmchmodunix()
        {
            xoops_cp_header();

            xoops_confirm(['op' => 'unixreadonly', 'id_art' => $id_art, 'ok' => 1], 'mainfile.config.php', 'Are you sure you want this file to be chmodded  777 for a Unix System?');

            xoops_cp_footer();
        }

?>

<?php

function xoopsmainfileconfigwin32()
{
    global $xoopsDB, $xxmysqldb_name, $xxmysqldb_host, $xxmysqldb_password, $xxmysqldb_uname, $login_id, $date_started;

    xoops_cp_header();

    OpenTable();

    if (is_writable(XOOPS_ROOT_PATH . '/mainfile.php')) {
        xoops_error(sprintf(_WARNINWRITEABLE, XOOPS_ROOT_PATH . '/mainfile.php'));

        echo '</br>';

        echo '<table width="100%" border="1" align="center" cellspacing="0" bordercolor="#FFFFFF">';

        echo '<form action="mainfile.config.php" method="post">';

        echo '<tr>';

        echo '<td class="bg3" width="40%"><font size="2">Make Configuration Files Read Only</td>';

        echo '<td class="bg3" width="60%">';

        echo '<input type="hidden" name="op" value="win32readonly">';

        $config = 'mainfile.php';

        echo "<input class=\"button\" type=\"submit\" value=\"Make $config Read Only\">";

        echo '</td>';

        echo '</tr>';

        echo '</form>';

        echo '</table>';

        echo '<p>&nbsp;</p>';

        CloseTable();

        xoops_cp_footer();
    } else {
        redirect_header('mainfile.config.php', 1, 'Mainfile.php is not Writeable!');
    }
}

function xoopsmainfileconfigunix()
{
    global $xoopsDB, $xxmysqldb_name, $xxmysqldb_host, $xxmysqldb_password, $xxmysqldb_uname, $login_id, $date_started;

    xoops_cp_header();

    OpenTable();

    if (is_writable(XOOPS_ROOT_PATH . '/mainfile.php')) {
        xoops_error(sprintf(_WARNINWRITEABLE, XOOPS_ROOT_PATH . '/mainfile.php'));

        echo '</br>';

        echo '<table width="100%" border="1" align="center" cellspacing="0" bordercolor="#FFFFFF">';

        echo '<form action="mainfile.config.php" method="post">';

        echo '<tr>';

        echo '<td class="bg3" width="40%"><font size="2">Make Configuration Files Read Only</td>';

        echo '<td class="bg3" width="60%">';

        echo '<input type="hidden" name="op" value="unixreadonly">';

        $config = 'mainfile.php';

        echo "<input class=\"button\" type=\"submit\" value=\"Make $config Read Only\">";

        echo '</td>';

        echo '</tr>';

        echo '</form>';

        echo '</table>';

        echo '<p>&nbsp;</p>';
    } else {
        redirect_header('mainfile.config.php', 1, 'Mainfile.php is not Writeable!');
    }
}

function xoopschmodselect()
{
    xoops_cp_header();

    OpenTable();

    echo'
	<style type=text/css>	
	input.Radio.special {
	background: #2F5376;
	font: 11px verdana, arial, helvetica, sans-serif;
	border: 0px solid #000000;
}
textarea.wide { border: #000000 1px solid; width: 800px; font: 11px verdana, arial, helvetica, sans-serif;}
hr.special { height: 3px; border: 1px ridge #E18A00; width: 99%;}
</style>
<table width=98% border=0 align=center cellpadding=0 cellspacing=0>
<tr>
<td>
<p>&nbsp;</p><form name=form1 method=post action=mainfile.config.php>
<table width=97% border=0 align=center cellpadding=0 cellspacing=0 class=bg3>
<tr>
<td colspan=3><strong>Choose Your Operating System</strong></td>
</tr>
</tr>
<tr>
<td>&nbsp;</td>
<td colspan=2>&nbsp;</td>
</tr>
<tr>
<td width=9%><div align=right><strong>Windows</strong></div></td>
<td width=2%><input type=radio name=op class=special value=confirmchmodwin32 border=0></td>
<td width=89%><hr class=special idth=99% size=1></td>
</tr>
<tr>
<td><div align=right><strong>Linux / Unix</strong></div></td>
<td><input type=radio name=op class=special border=0 value=confirmchmodunix></td>
<td><hr class=special size=1></td>
</tr>
<tr>
<td>&nbsp;</td>
<td colspan=2><input type=submit name=Submit value=Next -&gt;></td>
</tr>
</table>
<p>&nbsp;</p></form>

</td>
</tr>
</table>
';

    CloseTable();

    xoops_cp_footer();
}

function win32readonly()
{
    chmod(XOOPS_ROOT_PATH . '/mainfile.php', 775);

    chmod(XOOPS_ROOT_PATH . '/mainfile.php', 775);

    redirect_header('mainfile.config.php', 1, 'Properties Successfully Changed');
}

function unixreadonly()
{
    chmod(XOOPS_ROOT_PATH . '/mainfile.php', 777);

    chmod(XOOPS_ROOT_PATH . '/mainfile.php', 777);

    redirect_header('mainfile.config.php', 1, 'Properties Successfully Changed');
}

function xoopsmainfileConfigEdit()
{
    global $_POST;

    $content = $_POST['content'];

    chmod(XOOPS_ROOT_PATH . '/mainfile.php', 666);

    $filename = XOOPS_ROOT_PATH . '/mainfile.php';

    $content = preg_replace('&#60', '<', $content);

    $content = preg_replace('&#62', '>', $content);

    $content = stripslashes($content);

    $fp = fopen($filename, w);

    fwrite($fp, $content);

    fclose($fp);

    redirect_header('mainfile.config.php', 1, 'Xoops Mainfile.php Successfully Modified!');
}

function xoopsmainfilemakewriteable()
{
    chmod(XOOPS_ROOT_PATH . '/mainfile.php', 666);

    redirect_header('mainfile.config.php', 1, 'Xoops Mainfile.php made Writeable!');
}

function xoopspphloggerConfigEditWrite()
{
    global $_POST;

    chmod(XOOPS_ROOT_PATH . '/mainfile.php', 666);

    $xoops_root_path = $_POST['xoops_root_path'];

    $xoops_url = $_POST['xoops_url'];

    $xoops_db_pass = $_POST['xoops_db_pass'];

    $xoops_db_user = $_POST['xoops_db_user'];

    $xoops_db_name = $_POST['xoops_db_name'];

    $filename = XOOPS_ROOT_PATH . '/mainfile.php';

    $file = fopen($filename, 'wb');

    $content .= '';

    $content .= "<?php\n";

    $content .= "\n";

    $content .= "// $Id: mainfile.dist.php,v 1.5 2003/02/12 11:36:33 okazu Exp $\n";

    $content .= "//  ------------------------------------------------------------------------ //\n";

    $content .= "//                XOOPS - PHP Content Management System                      //\n";

    $content .= "//                    Copyright (c) 2000 XOOPS.org                           //\n";

    $content .= "//                       <https://www.xoops.org>                             //\n";

    $content .= "//  ------------------------------------------------------------------------ //\n";

    $content .= "//  This program is free software; you can redistribute it and/or modify     //\n";

    $content .= "//  it under the terms of the GNU General Public License as published by     //\n";

    $content .= "//  the Free Software Foundation; either version 2 of the License, or        //\n";

    $content .= "//  (at your option) any later version.                                      //\n";

    $content .= "//                                                                           //\n";

    $content .= "//  You may not change or alter any portion of this comment or credits       //\n";

    $content .= "//  of supporting developers from this source code or any supporting         //\n";

    $content .= "//  source code which is considered copyrighted (c) material of the          //\n";

    $content .= "//  original comment or credit authors.                                      //\n";

    $content .= "//                                                                           //\n";

    $content .= "//  This program is distributed in the hope that it will be useful,          //\n";

    $content .= "//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //\n";

    $content .= "//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //\n";

    $content .= "//  GNU General Public License for more details.                             //\n";

    $content .= "//                                                                           //\n";

    $content .= "//  You should have received a copy of the GNU General Public License        //\n";

    $content .= "//  along with this program; if not, write to the Free Software              //\n";

    $content .= "//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //\n";

    $content .= "//  ------------------------------------------------------------------------ //\n";

    $content .= "\n";

    $content .= "if ( !defined(\"XOOPS_MAINFILE_INCLUDED\") ) {\n";

    $content .= "define(\"XOOPS_MAINFILE_INCLUDED\",1);\n";

    $content .= "\n";

    $content .= "// XOOPS Physical Path\n";

    $content .= "// Physical path to your main XOOPS directory WITHOUT trailing slash\n";

    $content .= "define('XOOPS_ROOT_PATH', '$xoops_root_path');\n";

    $content .= "\n";

    $content .= "// XOOPS Virtual Path (URL)\n";

    $content .= "// Virtual path to your main XOOPS directory WITHOUT trailing slash\n";

    $content .= "define('XOOPS_URL', '$xoops_url');\n";

    $content .= "\n";

    $content .= "// Database\n";

    $content .= "// Choose the database to be used\n";

    $content .= "define('XOOPS_DB_TYPE', 'mysql');\n";

    $content .= "\n";

    $content .= "// Table Prefix\n";

    $content .= "// This prefix will be added to all new tables created to avoid name conflict in the database. If you are unsure, just use the default 'xoops'.\n";

    $content .= "define('XOOPS_DB_PREFIX', 'xoops');\n";

    $content .= "\n";

    $content .= "// Database Hostname\n";

    $content .= "// Hostname of the database server. If you are unsure, 'localhost' works in most cases.\n";

    $content .= "define('XOOPS_DB_HOST', 'localhost');\n";

    $content .= "\n";

    $content .= "// Database Username\n";

    $content .= "// Your database user account on the host\n";

    $content .= "define('XOOPS_DB_USER', '$xoops_db_user');\n";

    $content .= "\n";

    $content .= "// Database Password\n";

    $content .= "// Password for your database user account\n";

    $content .= "define('XOOPS_DB_PASS', '$xoops_db_pass');\n";

    $content .= "\n";

    $content .= "// Database Name\n";

    $content .= "// The name of database on the host. The installer will attempt to create the database if not exist\n";

    $content .= "define('XOOPS_DB_NAME', '$xoops_db_name');\n";

    $content .= "\n";

    $content .= "// Use persistent connection? (Yes=1 No=0)\n";

    $content .= "// Default is 'Yes'. Choose 'Yes' if you are unsure.\n";

    $content .= "define('XOOPS_DB_PCONNECT', 0);\n";

    $content .= "\n";

    $content .= "define('XOOPS_GROUP_ADMIN', '1');\n";

    $content .= "define('XOOPS_GROUP_USERS', '2');\n";

    $content .= "define('XOOPS_GROUP_ANONYMOUS', '3');\n";

    $content .= "\n";

    $content .= "if (!isset(\$xoopsOption['nocommon'])) {\n";

    $content .= "require XOOPS_ROOT_PATH.\"/include/common.php\";\n";

    $content .= "}\n";

    $content .= "}\n";

    $content .= "?>\n";

    fwrite($file, $content);

    fclose($file);

    //chmod(XOOPS_ROOT_PATH."/mainfile_test.php",775);

    redirect_header('mainfile.config.php?op=xoopschmodselect', 1, 'Xoops Mainfile.php Successfully Edited');
}

switch ($op) {
        default:
            xoopsmainfileconfig();
            break;
        case 'xoopsmainfileConfigEdit':
            if (xoopsfwrite()) {
                xoopsmainfileConfigEdit();
            }
            break;
            case 'win32readonly':
            win32readonly();
            break;
            case 'unixreadonly':
            unixreadonly();
            break;
            case 'xoopsmainfileconfigwin32':
            xoopsmainfileconfigwin32();
            break;
            case 'xoopsmainfileconfigunix':
            xoopsmainfileconfigunix();
            break;
            case 'xoopsmainfilemakewriteable':
            xoopsmainfilemakewriteable();
            break;
            case 'confirm':
            confirm();
            break;
            case 'confirmchmodwin32':
            confirmchmodwin32();
            break;
            case 'confirmchmodunix':
            confirmchmodunix();
            break;
            case 'xoopspphloggerConfigEditWrite':
            xoopspphloggerConfigEditWrite();
            break;
            case 'xoopschmodselect':
            xoopschmodselect();
            break;
            case 'confirmwrite':
            confirmwrite();
            break;
}
?>
