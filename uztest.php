<?php
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
echo 'This is a test';
