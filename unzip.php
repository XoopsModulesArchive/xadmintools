<?php
/***************************************************************************
                             PHP Unzipper, v1.1
 ***************************************************************************
    file:                index.php
    functionality:       Provides a shell wrapper for Vincent Blavet's PclZip module.

                         This application is helpful when there is a need to upload a
                         many files with complicated directory structure to web server,
                         for example, forum systems (like phpBB) or other applications
                         (like phpMyAdmin) which consists of many files arranged in complicated
                         directory structure. All you need to do is to upload the archive file
                         and PHP Unzipper will take care of creating the correct directory layot
                         and file extraction. This program is especially helpful when you don't
                         have FTP access to webserver but generally it will be helpful in all cases
                         when there is a need to upload many small files to webserver.

    begin                14.07.2003
    last edited          26.08.2003
    copyright            (C) 2003 Rinalds Uzkalns
    email                dev@rinalds.com

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU Lesser General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

 ############################### XOOPS INCLUDES #########################################
include 'admin_header.php';
include '../../mainfile.php';

require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
include '/language/modules_admin.php';
 ############################### XOOPS INCLUDES #########################################

?>

<?php
function xoopszipdirlist()
{
    ############################### XOOPS HEADER #########################################

    xoops_cp_header();

    include 'navigation.php';

    OpenTable();

    ############################### XOOPS HEADER #########################################

    $docname = basename(getenv('script_name'));

    function fileext($file)
    {
        $p = pathinfo($file);

        return $p['extension'];
    }

    function convertsize($size)
    {
        $times = 0;

        $comma = '.';

        while ($size > 1024) {
            $times++;

            $size /= 1024;
        }

        $size2 = floor($size);

        $rest = $size - $size2;

        $rest *= 100;

        $decimal = floor($rest);

        $addsize = $decimal;

        if ($decimal < 10) {
            $addsize .= '0';
        }

        if (0 == $times) {
            $addsize = $size2;
        } else {
            $addsize = $size2 . $comma . mb_substr($addsize, 0, 2);
        }

        switch ($times) {
  case 0: $mega = ' bytes'; break;
  case 1: $mega = ' KB'; break;
  case 2: $mega = ' MB'; break;
  case 3: $mega = ' GB'; break;
  case 4: $mega = ' TB'; break; }

        $addsize .= $mega;

        return $addsize;
    }

    //$dir = $_GET['dir'];

    $dir = XOOPS_ROOT_PATH . '/modules/';

    $tempdir = XOOPS_ROOT_PATH . '/uploads/';

    $action = $_GET['action'];

    $adm_user = $_POST['adm_user'];

    $adm_pass = $_POST['adm_pass'];

    $adm_pass_conf = $_POST['adm_pass_conf'];

    /*      THE REAL STUFF BEGINS HERE     */

    include 'pclzip.lib.php';

    chdir($dir);

    $basedir = getcwd();

    $basedir = str_replace('\\', '/', $basedir);        //'
############################################################################ DIRECTORY LIST ####################################################################
if (is_dir($basedir)) { //show directory list
$parent = dirname($basedir);

    $cur = $basedir;

    while ('/' == mb_substr($cur, 0, 1)) {
        $cur = mb_substr($cur, 1, mb_strlen($cur));

        $path .= '/';
    }

    $p_out = $path;

    while (mb_strlen($cur) > 0) {
        $k = mb_strpos($cur, '/');

        if (!mb_strpos($cur, '/')) {
            $k = mb_strlen($cur);
        }

        $s = mb_substr($cur, 0, $k);

        $cur = mb_substr($cur, $k + 1, mb_strlen($cur));

        $path .= $s . '/';

        $p_out .= "<a href='?dir=$path'>$s</a>/";
    }

    echo '<br><left><div><b>Current Module dir: <font color=FF0000>' . $path . '</font></b></div>';

    echo '<br><div class=filedirtitle>Current Installable Packages</div>';

    //echo "<b><left><a href='?dir=$parent'>Parent directory</a></b></center><br>\n";

    $glob = [];

    $c = 0;

    if ($dh = opendir(getcwd())) {
        while (false !== ($file = readdir($dh))) {
            if ('..' != $file && '.' != $file) {
                $glob[$c++] = $file;
            }
        }

        closedir($dh);
    }

    foreach ($glob as $filename) {
        if (is_dir($filename)) {
            //echo "&nbsp;&nbsp;/<a href='?dir=$basedir/$filename'>$filename</a><br>\n";
    //echo "&nbsp;&nbsp;$filename<br>\n";
        }
    }

    echo '</div><div class=filelist>';

    $filez = $glob;

    reset($filez);

    if (count($filez) > 0) {
        foreach ($filez as $filename) {
            if ('zip' == mb_strtolower(fileext($filename))) {
                if (is_file($filename)) {
                    echo "<table width=50% height=\"10\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">
  <tr> 
  <td width=\"50%\" align=\"left\" valign=\"top\"><b>$filename</b></td>
    <td width=\"3%\" align=\"left\"><form name=\"form1\" method=\"post\" action=\"unzip.php\">
        <input type=\"submit\" name=\"Submit\" value=\"View\">
        <input name=\"dir\" type=\"hidden\" id=\"dir\" value=\"$basedir\">
        <input name=\"op\" type=\"hidden\" id=\"op\" value=\"view\">
        <input name=\"unzip\" type=\"hidden\" id=\"unzip\" value=\"$filename\">
        <input name=\"action\" type=\"hidden\" id=\"action\" value=\"view\">
      </form></td>
    <td width=\"3%\" align=\"left\"><form name=\"form2\" method=\"post\" action=\"unzip.php\">
        <input type=\"submit\" name=\"Submit2\" value=\"Unzip\">
        <input name=\"dir\" type=\"hidden\" id=\"dir\" value=\"$basedir\">
        <input name=\"op\" type=\"hidden\" id=\"op\" value=\"unzip\">
        <input name=\"unzip\" type=\"hidden\" id=\"unzip\" value=\"$filename\">
        <input name=\"action\" type=\"hidden\" id=\"action\" value=\"unzip\">
      </form></td>
	 <td width=\"3%\" align=\"left\"><form name=\"form3\" method=\"post\" action=\"unzip.php\">
        <input type=\"submit\" name=\"Submit2\" value=\"Delete File\">
        <input name=\"dir\" type=\"hidden\" id=\"dir\" value=\"$basedir\">
        <input name=\"op\" type=\"hidden\" id=\"op\" value=\"deletefile\">
        <input name=\"unzip\" type=\"hidden\" id=\"unzip\" value=\"$filename\">
        <input name=\"action\" type=\"hidden\" id=\"action\" value=\"deletefile\">
      </form></td>
  </tr>
</table>
";
                }
            } elseif ('rar' == mb_strtolower(fileext($filename))) {
                if (is_file($filename)) {
                    echo "<table width=50% border=\"0\" cellspacing=\"0\" cellpadding=\"2\">
  <tr> 
  <td width=\"30%\" align=\"left\" valign=\"top\"><b>$filename</b></td>
    <td width=\"3%\" align=\"left\"><form name=\"form1\" method=\"post\" action=\"unzip.php\">
        <input type=\"submit\" name=\"Submit\" value=\"View\">
        <input name=\"dir\" type=\"hidden\" id=\"dir\" value=\"$basedir\">
        <input name=\"op\" type=\"hidden\" id=\"op\" value=\"view\">
        <input name=\"unzip\" type=\"hidden\" id=\"unzip\" value=\"$filename\">
        <input name=\"action\" type=\"hidden\" id=\"action\" value=\"view\">
      </form></td>
    <td width=\"3%\" align=\"left\"><form name=\"form2\" method=\"post\" action=\"unzip.php\">
        <input type=\"submit\" name=\"Submit2\" value=\"Coming Soon\">
        <input name=\"dir\" type=\"hidden\" id=\"dir\" value=\"$basedir\">
        <input name=\"op\" type=\"hidden\" id=\"op\" value=\"rar\">
        <input name=\"unzip\" type=\"hidden\" id=\"unzip\" value=\"$filename\">
        <input name=\"action\" type=\"hidden\" id=\"action\" value=\"extractrar\">
      </form></td>
  </tr>
</table>";
                }
            } elseif ('gz' == mb_strtolower(fileext($filename))) {
                if (is_file($filename)) {
                    echo "<table width=50% border=\"0\" cellspacing=\"0\" cellpadding=\"2\">
  <tr> 
  <td width=\"30%\" align=\"left\" valign=\"top\"><b>$filename</b></td>
    <td width=\"3%\" align=\"left\"><form name=\"form1\" method=\"post\" action=\"unzip.php\">
        <input type=\"submit\" name=\"Submit\" value=\"View\">
        <input name=\"dir\" type=\"hidden\" id=\"dir\" value=\"$basedir\">
        <input name=\"op\" type=\"hidden\" id=\"op\" value=\"view\">
        <input name=\"unzip\" type=\"hidden\" id=\"unzip\" value=\"$filename\">
        <input name=\"action\" type=\"hidden\" id=\"action\" value=\"view\">
      </form></td>
    <td width=\"3%\" align=\"left\"><form name=\"form2\" method=\"post\" action=\"unzip.php\">
        <input type=\"submit\" name=\"Submit2\" value=\"Untar\">
        <input name=\"dir\" type=\"hidden\" id=\"dir\" value=\"$basedir\">
        <input name=\"op\" type=\"hidden\" id=\"op\" value=\"untar\">
        <input name=\"unzip\" type=\"hidden\" id=\"unzip\" value=\"$filename\">
        <input name=\"action\" type=\"hidden\" id=\"action\" value=\"untar\">
      </form></td>
	 <td width=\"3%\" align=\"left\"><form name=\"form3\" method=\"post\" action=\"unzip.php\">
        <input type=\"submit\" name=\"Submit2\" value=\"Delete File\">
        <input name=\"dir\" type=\"hidden\" id=\"dir\" value=\"$basedir\">
        <input name=\"op\" type=\"hidden\" id=\"op\" value=\"deletefile\">
        <input name=\"unzip\" type=\"hidden\" id=\"unzip\" value=\"$filename\">
        <input name=\"action\" type=\"hidden\" id=\"action\" value=\"deletefile\">
      </form></td>
  </tr>
</table>";
                }
            }
        }
    }

    echo '</div></div><br>';
}

    ########################################################################## END SHOW DIRECTORY LIST ##################################################################################

    ########################################################################## START UNZIP FUNCTION #####################################################################################

    $unzip = $_POST['unzip'];

    if (is_file($unzip)) {       //unzipping...
        $zip = new PclZip($unzip);

        if (0 == ($list = $zip->listContent())) {
            die('Error : ' . $zip->errorInfo(true));
        }

        /*
        File 0 / [stored_filename] = config
        File 0 / [size] = 0
        File 0 / [compressed_size] = 0
        File 0 / [mtime] = 1027023152
        File 0 / [comment] =
        File 0 / [folder] = 1
        File 0 / [index] = 0
        File 0 / [status] = ok
        */

        ######################## START CALC STATS #############################################

        for ($i = 0, $iMax = count($list); $i < $iMax; $i++) {
            if ('1' == $list[$i][folder]) {
                $fold++;

                $dirs[$fold] = $list[$i][stored_filename];

                if ('unzip' == $_POST[action]) {
                    $dirname = $list[$i][stored_filename];

                    $dirname = mb_substr($dirname, 0, -1);

                    mkdir($basedir . '/' . $dirname);
                }

                chmod($basedir . '/' . $dirname, 0777);
            } else {
                $fil++;
            }

            $tot_comp += $list[$i][compressed_size];

            $tot_uncomp += $list[$i][size];
        }

        ########################## END CALC STATS ##############################################

        ########################## START UNZIP #################################################

        echo '<div class=unzip>' . ('unzip' == $_POST[action] ? 'Unzipping' : 'Viewing') . " file <b>$unzip</b><br>\n";

        echo "$fil files and $fold directories in archive<br>\n";

        echo 'Compressed archive size: ' . convertsize($tot_comp) . "<br>\n";

        echo 'Uncompressed archive size: ' . convertsize($tot_uncomp) . "<br>\n";

        if ('unzip' == $_POST[action]) {
            echo '<br><b>Starting to decompress...</b><br>';

            $zip->extract('');

            //echo "Archive sucessfuly extracted!<br>\n";

            @unlink($dir . "/$unzip");

            echo "<font color=FF0000 size=3><br>>>> $unzip Deleted! </font>";

            redirect_header('unzip.php', 2, "$unzip successfully extracted, and was deleted from server!");

            xoops_cp_footer();
        }

        ########################## END UNZIP ###################################################

        ##################################################  VIEW FUNCTION ###########################################################

        if ('view' == $_POST[action]) {
            echo '<br>';

            for ($i = 0, $iMax = count($list); $i < $iMax; $i++) {
                if (1 == $list[$i][folder]) {
                    echo '<b>Folder: ' . $list[$i][stored_filename] . '</b><br>';
                } else {
                    echo $list[$i][stored_filename] . ' (' . convertsize($list[$i][size]) . ')<br>';
                }
            }

            ################################################## END VIEW FUNCTION #######################################################
        }

        ########################################################################################### END UNZIP FUNCTION ###################################################################
    }

    ############################################ FILE UPLOAD SCRIPT START ################################################################3

    require_once XOOPS_ROOT_PATH . '/modules/xadmintools/includes/upload.inc.php'; ?>
<?php
if (@phpversion() < '4.1.0') {
        $_FILE = $HTTP_POST_FILES;

        $_GET = $_GET;

        $_POST = $_POST;
    }

    clearstatcache();

    error_reporting(E_ALL & ~E_NOTICE);

    $fum_vers = '1.3'; # do not edit this line, the script will not work!!!

    $fum_info_full = "File Upload Manager v$fum_vers";

    function authDo($auth_userToCheck, $auth_passToCheck)
    {
        global $auth_usern, $auth_passw;

        $auth_encodedPass = md5($auth_passw);

        if ($auth_userToCheck == $auth_usern && $auth_passToCheck == $auth_encodedPass) {
            $auth_check = true;
        } else {
            $auth_check = false;
        }

        return $auth_check;
    }

    if (isset($logout)) {
        setcookie('fum_user', '', time() - 3600);

        setcookie('fum_pass', '', time() - 3600);
    }

    if (isset($login)) {
        $auth_password_en = md5($auth_formPass);

        $auth_username_en = $auth_formUser;

        if (authDo($auth_username_en, $auth_password_en)) {
            setcookie('fum_user', $auth_username_en, time() + 3600);

            setcookie('fum_pass', $auth_password_en, time() + 3600);

            $auth_msg = '<b>Authentication successful!</b> The cookies have been set.<br><br>' .
    $auth_msg . "Your password (MD5 encrypted) is: $auth_password_en";
        } else {
            $auth_msg = '<b>Authentication error!</b>';
        }
    }

    if (('dl' == $_GET[act]) && $_GET[file]) {
        if (1 != $auth_ReqPass || (1 == $auth_ReqPass && isset($fum_user) && !isset($logout))) {
            if (1 != $auth_ReqPass || (1 == $auth_ReqPass && authDo($fum_user, $fum_pass))) {
                $value_de = base64_decode($_GET[file], true);

                $dl_full = $dir_store . '/' . $value_de;

                $dl_name = $value_de;

                if (!file_exists($dl_full)) {
                    //echo"ERROR: Cannot download file, it does not exist.<br>»<a href=\"$_SERVER[PHP_SELF]\">back</a>";

                    redirect_header('unzip.php', 1, 'ERROR: Cannot download file, it does not exist.');
                }

                header('Content-Type: application/octet-stream');

                header("Content-Disposition: attachment; filename=$dl_name");

                header('Content-Length: ' . filesize($dl_full));

                header('Accept-Ranges: bytes');

                header('Pragma: no-cache');

                header('Expires: 0');

                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

                header('Content-transfer-encoding: binary');

                @readfile($dl_full);

                exit();
            }
        }
    }

    function getlast($toget)
    {
        $pos = mb_strrpos($toget, '.');

        $lastext = mb_substr($toget, $pos + 1);

        return $lastext;
    }

    function replace($o)
    {
        $o = str_replace('/', '', $o);

        $o = str_replace('\\', '', $o);

        $o = str_replace(':', '', $o);

        $o = str_replace('*', '', $o);

        $o = str_replace('?', '', $o);

        $o = str_replace('<', '', $o);

        $o = str_replace('>', '', $o);

        $o = str_replace('"', '', $o);

        $o = str_replace('|', '', $o);

        return $o;
    } ?>
<!-- <?=$fum_info_full?> -->

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo ($title) ?: ('Xoops Module Upload Manager'); ?></title>
<link rel="stylesheet" href="<?=$dir_img?>/<?=$style?>.css" type="text/css">
<?php
    if (1 == $auth_ReqPass) {
        if (isset($login) || isset($logout)) {
            echo("<meta http-equiv='refresh' content='2;url=$_SERVER[PHP_SELF]'>");
        }
    } ?>
</head>
<body bgcolor="#F7F7F7">
<div align="left"><br>
  <br>
  <?php
    if (1 != $auth_ReqPass || (1 == $auth_ReqPass && isset($fum_user) && !isset($logout))) {
        if (1 != $auth_ReqPass || (1 == $auth_ReqPass && authDo($fum_user, $fum_pass))) {
            ?>
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr> 
      <td><font size="3"><b>Xoops Module Upload Manager</b></font>&nbsp;<font style="text-decoration: bold; font-size: 9px;">v
        <?=$fum_vers?>
        </font>&nbsp; 
        <?php
    #--Please do not remove my link/copyright as it is unfair and a breach of the license--#
    echo'Original Script by: <a href="http://www.mtnpeak.net" style="text-decoration: none; color: #C0C0C0; font-size: 9px; cursor: default";>&copy; thepeak</a>';

            echo '<br> Module by talunceford (Widowmaker) <a href=http://www.tswn.com style=text-decoration: none; color: #C0C0C0; font-size: 9px; cursor: default;> The Spider Web Network</a>'; ?>
      </td>
    </tr>
  </table>
  <?php
    if (!eregi('777', decoct(fileperms($dir_store)))) {
        redirect_header('unzip.php', 1, 'ERROR: cannot access the upload store file directory. please chmod the \"$dir_store\" directory with value 0777 (xrw-xrw-xrw)!');
    } else {
        if (!$_FILES[fileupload]) {
            ?>
  <table width="100%" cellspacing="0" cellpadding="0" border="0" class="table_decoration" style="padding-top:5px;padding-left=5px;padding-bottom:5px;padding-right:5px">
    <form method="post" enctype="multipart/form-data">
      <tr> 
        <td>file:</td>
        <td><input type="file" name="fileupload" class="textfield" size="30"></td>
      </tr>
      <tr> 
        <td>rename to:</td>
        <td><input type="text" name="rename" class="textfield" size="46"></td>
      </tr>
      <tr> 
        <td>file types allowed:</td>
        <td> 
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
        </td>
      </tr>
      <tr> 
        <td>file size limit:</td>
        <td> <b>
          <?php
            if ($file_size_ind >= 1048576) {
                $file_size_ind_rnd = round(($file_size_ind / 1024000), 3) . ' MB';
            } elseif ($file_size_ind >= 1024) {
                $file_size_ind_rnd = round(($file_size_ind / 1024), 2) . ' KB';
            } elseif ($file_size_ind >= 0) {
                $file_size_ind_rnd = $file_size_ind . ' bytes';
            } else {
                $file_size_ind_rnd = '0 bytes';
            }

            echo (string)$file_size_ind_rnd; ?>
          </b> </td>
      </tr>
      <tr> 
        <td colspan="2"><input type="submit" value="upload" class="button">
          &nbsp;
          <input type="reset" value="clear" class="button"></td>
      </tr>
    </form>
  </table>
  <?php
  ?>
  <?php
        if ((!$_GET[act] || !$_GET[file]) && 'delall' != $_GET[act]) {
            $opendir = @opendir($dir_store);

            while ($readdir = @readdir($opendir)) {
                if ('.' != $readdir && '..' != $readdir && 'index.html' != $readdir) {
                    $filearr[] = $readdir;
                }

                $sort = [];

                for ($i = 1, $iMax = count($filearr); $i <= $iMax; $i++) {
                    $key = count($filearr) - $i;

                    $file = $filearr[$key];

                    $sort[$i] = $file;
                }

                asort($sort);
            } ?>
  <br>
  
  <?php

  ?>
  <br>
  <?php
            if (1 == $file_list_allow && (count($filearr) >= 1)) {
                ?>
  <?php
  //require_once XOOPS_ROOT_PATH."/download_backend.php";
require_once XOOPS_ROOT_PATH . '/modules/xadmintools/include/modulelist.php';

                require_once XOOPS_ROOT_PATH . '/modules/xadmintools/include/templatelist.php';

                CloseTable();

                xoops_cp_footer(); ?>
  <?php
            }
        } elseif (('view' == $_GET[act]) && $_GET[file]) {
            $value_de = base64_decode($_GET[file], true);

            echo"<script language=\"javascript\">\nViewPopup = window.open(\"$dir_store/$value_de\", \"fum_viewfile\", \"toolbar=no,status=no,menubar=no,scrollbars=yes,resizable=yes,location=no,width=640,height=480\")\nViewPopup.document.bgColor=\"#F7F7F7\"\nViewPopup.document.close()\n</script>";

            echo"<br><img src=\"$dir_img/info.gif\" width=\"15\" height=\"15\">&nbsp;<b><font size=\"2\">file opened!</font></b><br>»<a href=\"$_SERVER[PHP_SELF]\">back</a><br><br><br>If the file did not display, you must <b>disable</b> your popup manager, or enable javascript in your browser.";
        } elseif (('del' == $_GET[act]) && $_GET[file]) {
            $value_de = base64_decode($_GET[file], true);

            @unlink($dir_store . "/$value_de");

            //echo"<br><img src=\"$dir_img/info.gif\" width=\"15\" height=\"15\">&nbsp;<b><font size=\"2\">file has been deleted!</font></b><br>»<a href=\"$_SERVER[PHP_SELF]\">back</a>";

            redirect_header('unzip.php', 1, 'File has been deleted!');
        }

            if ('delall' == $_GET[act]) {
                $handle = opendir($dir_store);

                while ($file = readdir($handle)) {
                    if (('.' != $file) && ('..' != $file)) {
                        @unlink($dir_store . '/' . $file);
                    }
                }

                closedir($handle);

                xoops_cp_footer();

                //echo"<br><img src=\"$dir_img/info.gif\" width=\"15\" height=\"15\">&nbsp;<b><font size=\"2\">all files have been deleted!</font></b><br>»<a href=\"$_SERVER[PHP_SELF]\">back</a>";

                redirect_header('unzip.php', 1, 'All Files Have Been Deleted!');
            }
        } else {
            echo'<br><br>';

            $uploadpath = $dir_store . '/';

            $source = $_FILES[fileupload][tmp_name];

            $fileupload_name = $_FILES[fileupload][name];

            $weight = $_FILES[fileupload][size];

            for ($i = 0, $iMax = count($file_ext_allow); $i < $iMax; $i++) {
                if (getlast($fileupload_name) != $file_ext_allow[$i]) {
                    $test .= '~~';
                }
            }

            $exp = explode('~~', $test);

            if (count($exp) == (count($file_ext_allow) + 1)) {
                //echo"<br><img src=\"$dir_img/error.gif\" width=\"15\" height=\"15\">&nbsp;<b><font size=\"2\">ERROR: your file type is not allowed (".getlast($fileupload_name).")</font>, or you didn't specify a file to upload.</b><br>»<a href=\"$_SERVER[PHP_SELF]\">back</a>";

                redirect_header('unzip.php', 1, "ERROR: your file type is not allowed or you didn't specify a file to upload.");
            } else {
                if ($weight > $file_size_ind) {
                    echo"<br><img src=\"$dir_img/error.gif\" width=\"15\" height=\"15\">&nbsp;<b><font size=\"2\">ERROR: please get the file size less than " . $file_size_ind . ' BYTES  (' . round(($file_size_ind / 1024), 2) . " KB)</font></b><br>»<a href=\"$_SERVER[PHP_SELF]\">back</a>";
                } else {
                    foreach ($_FILES[fileupload] as $key => $value) {
                        echo"<font color=\"#3399FF\">$key</font> : $value <br>";
                    }

                    echo '<br>';

                    $dest = '';

                    if (('none' != $source) && ('' != $source)) {
                        $dest = $uploadpath . $fileupload_name;

                        if ('' != $dest) {
                            if (file_exists($uploadpath . $fileupload_name)) {
                                //echo"<br><img src=\"$dir_img/error.gif\" width=\"15\" height=\"15\">&nbsp;<b><font size=\"2\">ERROR: that file has already been uploaded before, please choose another file</font></b><br>»<a href=\"$_SERVER[PHP_SELF]\">back</a>";

                                redirect_header('unzip.php', 1, 'ERROR: that file has already been uploaded before, please choose another file');
                            } else {
                                if (copy($source, $dest)) {
                                    if ($_POST[rename]) {
                                        $_POST[rename] = replace($_POST[rename]);

                                        $exfile = explode('.', $fileupload_name);

                                        if (@rename("$dir_store/$fileupload_name", "$dir_store/$_POST[rename]." . getlast($fileupload_name))) {
                                            echo"<br><img src=\"$dir_img/info.gif\" width=\"15\" height=\"15\">&nbsp;<b><font size=\"2\">file has been renamed to $_POST[rename]." . getlast($fileupload_name) . '!</font></b></font><br>';
                                        }
                                    }

                                    //echo"<br><img src=\"$dir_img/info.gif\" width=\"15\" height=\"15\">&nbsp;<b><font size=\"2\">file has been uploaded!</font></b><br>»<a href=\"$_SERVER[PHP_SELF]\">back</a>";
                                } else {
                                    //echo"<br><img src=\"$dir_img/error.gif\" width=\"15\" height=\"15\">&nbsp;<b><font size=\"2\">ERROR: cannot upload, please chmod the dir to 777</font></b><br>»<a href=\"$_SERVER[PHP_SELF]\">back</a>";

                                    redirect_header('unzip.php', 1, 'ERROR: cannot upload, please chmod the dir to 777');
                                }
                            }
                        }
                    }
                }
            }
        }
    }

            redirect_header('unzip.php', 1, 'File has been uploaded!');

        #/# end of main script, start authentication code IF user not logged in IF $auth_ReqPass is enabled
        } else {
            echo("<p><img src=\"$dir_img/error.gif\" width=\"15\" height=\"15\">&nbsp;Authentication error</p>" .
"<p><a href='$_SERVER[PHP_SELF]?logout=1'>Delete cookies and login again<a></p>");
        }
    } else {
        if (!isset($login) || isset($relogin)) {
            ?>
  &nbsp;</p>
<p><font size="3"><b><i><?php echo ($title) ?: ('File Upload Manager'); ?></i>
  - Authentication</b></font><br>
  <br>
</p></div>
<table class="table_auth"><tr><td><center>
Please enter the username and password to enter the restricted area.<br>
You must have cookies enabled in your browser to continue.
</center></td></tr></table>
<form action="<?=$_SERVER[PHP_SELF]?>?login=1" method="POST"><p>
Username: <input type="text" name="auth_formUser" size="20"><br>
Password: <input type="password" name="auth_formPass" size="20">
<p><input type="submit" name="submit" class="button" value="Log-In"></p>
</form></center>
<?php
        } elseif (isset($login)) {
            echo("<p>$auth_msg</p>" . "<p>You'll be redirected in 2 seconds!</p>");
        }
    } ?>
</body>
</html>
<!-- Copyright (c) 2003 thepeak. Get your own copy of this free PHP script from www.mtnpeak.net -->
<?php
}
function xoopsmoduleupload()
{
    global $HTTP_POST_FILES;

    error_reporting(E_ALL);

    require_once XOOPS_ROOT_PATH . '/modules/system/upload.php';

    //require __DIR__ . '/upload.php';

    echo '<pre>';

    //print_r($HTTP_POST_FILES);

    $upload = new http_upload('es');

    $file = $upload->getFiles('userfile');

    if (PEAR::isError($file)) {
        die($file->getMessage());
    }

    if ($file->isValid()) {
        $file->setName('uniq');

        $dest_dir = XOOPS_ROOT_PATH . '/modules/';

        $dest_name = $file->moveTo($dest_dir);

        if (PEAR::isError($dest_name)) {
            die($dest_name->getMessage());
        }

        $real = $file->getProp('real');

        //echo "Uploaded $real as $dest_name in $dest_dir\n";

        echo '</pre>';

        xoops_cp_footer();

        redirect_header('unzip.php', 1, 'Module Successfully Uploaded <br> Saved as: $real <br> Full path: #dest_dir');
    } elseif ($file->isMissing()) {
        echo "No file selected\n";
    } elseif ($file->isError()) {
        //echo $file->errorMsg() . "\n";

        xoops_cp_footer();

        redirect_header('unzip.php', 1, $file->errorMsg());
    }

    //print_r($file->getProp());
}

/*function deletefolder($dir) {
global $_POST;
global $value_view, $dir;
$value_view = $_POST['module'];
$dir = $_POST['dir'];
}*/

//print_r($_POST);
//echo $value_view;
//$path = XOOPS_ROOT_PATH."/modules/$value_view/";
//passthru ("del $path* /q");
//rmdir(XOOPS_ROOT_DIR."/modules/$value_view/");
################################################################################################ CONFIRM MODULE DELETION ########################################################
function confirmdelete()
{
    xoops_cp_header();

    global $_GET;

    global $fic, $rep, $id, $ordre, $sens;

    $fic = $_GET['fic'];

    $rep = $_GET['rep'];

    $id = $_GET['id'];

    $ordre = $_GET['ordre'];

    $sens = $_GET['sens'];

    xoops_confirm(['op' => 'deletefolder', 'action' => 'supprimer_suite', 'rep' => $rep, 'fic' => $fic, 'id' => $id, 'ordre' => $ordre, 'sens' => 0, 'ok' => 1], 'delete.php', "Are you sure you want the module <font color=FF0000>\"$fic\"</font> to be deleted?<br>This is a perminant action and cannot be undone.");

    /*echo "$fic<br>";
    echo "$rep<br>";
    echo "$id<br>";
    echo "$ordre<br>";
    echo "$sens<br>";*/

    xoops_cp_footer();
}
################################################################################################### CONFIRM MODULE DELETION ###################################################

/*
####################################################### UNZIP FUNCTION ##############################################################
function unzip(){  //FUNCTION TO BE SEPARATED OUT FROM INITIAL CODE FROM ABOVE




}
####################################################### END UNZIP FUNCTION ##########################################################
*/

####################################################### UNTAR FUNCTION #############################################################
function untar()
{  //THIS FUNCTION TO BE ADDED AT A LATER DATE
    global $_POST;

    global $dir, $unzip, $action;

    $dir = $_POST['dir'];

    $contents = $_POST['unzip'];

    $action = $_POST['action'];

    // Include the Tarlib class

    require XOOPS_ROOT_PATH . '/modules/xadmintools/class/tar.php';

    $data = $dir . "/$contents";

    //$tar_object = new Archive_Tar('$data');

    //$tar_object->extractModify('$dir');

    echo (string)$data;

    //@unlink($dir."/$filename");

    echo "<b><font color=FF0000 size=3><br>>>> $data Deleted! </font></b>";

    redirect_header('unzip.php', 10, "$data successfully extracted, and was deleted from the server!");
}
######################################################### END UNTAR FUNCTION ###############################################################

############# DELETE FILE, NO UNZIP ##################
function deletefile()
{
    global $_POST;

    global $dir, $unzip, $action;

    $dir = $_POST['dir'];

    $contents = $_POST['unzip'];

    $action = $_POST['action'];

    @unlink($dir . "/$contents");

    redirect_header('unzip.php', 1, "$contents successfully deleted from the server!");
}

############# DELETE FILE, NO UNZIP END ##############

######################################################### TEMP UNTAR RESPONSE #############################################################
/*function untar(){
global $_POST;
$unzip = $_POST['unzip'];
$action = $_POST['action'];
redirect_header("unzip.php",5,"We are sorry, but $unzip cannot be extracted <br> because the function, $action has not been implemented yet.");
}*/
######################################################### END TEMP UNTAR RESPONSE #########################################################
function supprimer_suite()
{
    global $_POST;

    global $fic, $rep, $id, $ordre, $sens;

    $fic = $_POST['fic'];

    $rep = $_POST['rep'];

    $id = $_POST['id'];

    $ordre = $_POST['ordre'];

    $sens = $_POST['sens'];

    echo "$fic<br>";

    echo "$rep<br>";

    echo "$id<br>";

    echo "$ordre<br>";

    echo "$sens<br>";

    redirect_header('unzip.php', 2, "The Module $fic was not deleted from server, because this is a test!");
}
?>


<?php
switch ($op) {
        default:
            xoopszipdirlist();
            break;
            case 'xoopsmoduleupload':
            xoopsmoduleupload();
            break;
            case 'xoopsprint':
            xoopsprint();
            break;
            case 'delete':
            deletefile();
            break;
            case 'deletefolder':
            supprimer_suite();
            break;
            /*case "unzip":
            unzip();
            break;*/

            case 'confirmdelete':
            confirmdelete();
            break;
            case 'untar':
            untar();
            break;
            case 'deletefile':
            deletefile();
            break;
}
?>
