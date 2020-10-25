<?php
include 'admin_header.php';
include '../../mainfile.php';
global $xoopsUser;
if ($xoopsUser) {
    if ($xoopsUser->isAdmin()) {
        if ($xoopsUser) {
            if ($xoopsUser->isAdmin()) {
                include '/include/main.php';
            }
        }

        switch ($action) {
            case 'supprimer':
                xoops_cp_header();
                OpenTable($width = '98%');
                 if (!connecte($id)) {
                     header('Location:index.php');

                     exit;
                 }
                echo "<center>\n";
                if (is_dir('' . XOOPS_ROOT_PATH . "/$fic")) {
                    $mime = '' . _directory . '';
                } else {
                    $mime = '' . _file . '';
                }
                 echo '<font size="2">' . _really . " $mime <b>$fic</b> ?";
                echo '<br><br>';
                echo "<a href=\"index.php?action=supprimer_suite&rep=$rep&fic=$fic&id=$id&ordre=$ordre&sens=$sens\">" . _YES . "</a>&nbsp;&nbsp;&nbsp;\n";
                echo "<a href=\"index.php?rep=$rep&id=$id&ordre=$ordre&sens=$sens\">" . _NO . "</a>\n";
                echo '</font><br>';
                echo "</center>\n";
                CloseTable();
                xoops_cp_footer();
                break;
            case 'supprimer_suite':
                if (!connecte($id)) {
                    header('Location:index.php');

                    exit;
                }
                $messtmp = '<font size="2">';
                $a_effacer = '' . XOOPS_ROOT_PATH . "/$fic";
                 if (file_exists($a_effacer)) {
                     if (is_dir($a_effacer)) {
                         deldir($a_effacer);

                         $messtmp .= "'._Thedirectory.' <b>$fic</b> '._deleted.'";
                     } else {
                         unlink($a_effacer);

                         $messtmp .= "'._Thefile.' <b>$fic</b> '._deleted.'";
                     }
                 } else {
                     $messtmp .= '' . _removed . '';
                 }
                $messtmp .= "<br><br><a href=\"index.php?rep=$rep&id=$id&ordre=$ordre&sens=$sens\">" . _Goback . '</a>';
                $messtmp .= '</font>';
                redirect_header('unzip.php', 2, "The Module $fic was deleted from server!");
                exit;
                break;
        }
    } else {
        global $xoopsConfig;

        redirect_header(XOOPS_URL . '/', 3, _NOPERM);

        exit();
    }
}
