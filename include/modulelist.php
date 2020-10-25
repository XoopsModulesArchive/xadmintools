<?php
#####################################################################################  MODULE LIST ######################################################################
  require XOOPS_ROOT_PATH . '/modules/xadmintools/language/module_admin.php';

    $moduleHandler = xoops_getHandler('module');
    $installed_mods = $moduleHandler->getObjects();
    $listed_mods = [];
    $count = 0;
    foreach ($installed_mods as $module) {
        if (0 == $count % 2) {
            $class = 'even';
        } else {
            $class = 'odd';
        }

        $count++;

        $listed_mods[] = $module->getVar('dirname');
    }
    echo "<br>
	<table width='100%' border='0' class='outer' cellpadding='4' cellspacing='1'>
	<tr><td><b>Installable Modules</b></td></tr>
	<tr align='center'><th>" . _MD_AM_MODULE . '</th><th>' . _MD_AM_VERSION . '</th><th>' . _MD_AM_ACTION . '</th></tr>
	';
    $modules_dir = XOOPS_ROOT_PATH . '/modules';
    $handle = opendir($modules_dir);
    $count = 0;
    while ($file = readdir($handle)) {
        clearstatcache();

        $file = trim($file);

        if ('' != $file && 'cvs' != mb_strtolower($file) && !preg_match('/^[.]{1,2}$/', $file) && is_dir($modules_dir . '/' . $file)) {
            if (!in_array($file, $listed_mods, true)) {
                $module = $moduleHandler->create();

                $module->loadInfo($file);

                if (0 == $count % 2) {
                    $class = 'even';
                } else {
                    $class = 'odd';
                }

                echo '<tr class="' . $class . '" align="center" valign="middle">
				<td align="center" valign="bottom"><img src="' . XOOPS_URL . '/modules/' . $module->getInfo('dirname') . '/' . $module->getInfo('image') . '" alt="' . htmlspecialchars($module->getInfo('name'), ENT_QUOTES | ENT_HTML5) . '" border="0"></td>
				<td align="center">' . round($module->getInfo('version'), 2) . '</td>
				<td>
				<a href="' . XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin&amp;op=install&amp;module=' . $module->getInfo('dirname') . '"><img src="' . XOOPS_URL . '/modules/system/images/install.gif" alt="' . _MD_AM_INSTALL . '"></a>';

                echo '&nbsp;<a href="' . XOOPS_URL . '/modules/xadmintools/unzip.php?op=confirmdelete&rep=&fic=modules/' . $module->getInfo('dirname') . '&id=&ordre=&sens=0"><img src="' . XOOPS_URL . '/modules/xadmintools/images/delete.gif" alt="' . _MD_AM_DELETE . '"></a>';

                echo "&nbsp;<a href='javascript:openWithSelfMain(\"" . XOOPS_URL . '/modules/system/admin.php?fct=version&amp;mid=' . $module->getInfo('dirname') . "\",\"Info\",300,230);'>";

                echo '<img src="' . XOOPS_URL . '/modules/system/images/info.gif" alt="' . _INFO . '"></a></td></tr>
				';

                unset($module);

                $count++;
            }
        }
    }
        require '/include/xmlwriterclass.php';
    require '/include/rss_writer_class.php';
    /*
     *  First create an object of the class.
     */
    $rss_writer_object = new rss_writer_class();

    /*
     *  Choose the version of specification that the generated RSS document should conform.
     */
    $rss_writer_object->specification = '1.0';

    /*
     *  Specify the URL where the RSS document will be made available.
     */
    $rss_writer_object->about = XOOPS_URL . '/modules/xadmintools/channels.xml';

    /*
     *  Specify the URL of an optional XSL stylesheet.
     *  This lets the document be rendered automatically in XML capable browsers
     *  such as Internet Explore 5, Mozilla 5/Netscape 6 or better.
     */
    $rss_writer_object->stylesheet = XOOPS_URL . '/modules/xadmintools/rss/rss2html.xsl';

    /*
     *  When generating RSS version 1.0, you may declare additional
     *  namespaces that enable the use of more property tags defined
     *  by extension modules of the RSS specification.
     */
    $rss_writer_object->rssnamespaces['dc'] = 'http://purl.org/dc/elements/1.1/';

    /*
     *  Define the properties of the channel.
     */
    $properties = [];
    $properties['description'] = $xoopsConfig['sitename'];
    $properties['link'] = $xoopsConfig['siteurl'];
    $properties['title'] = $xoopsConfig['sitename'];
    $properties['dc:date'] = '2002-05-06T00:00:00Z';
    $rss_writer_object->addchannel($properties);

    /*
     *  If your channel has a logo, before adding any channel items, specify the logo details this way.
     */
    $properties = [];
    $properties['url'] = 'http://www.phpclasses.org/graphics/logo.gif';
    $properties['link'] = 'http://www.phpclasses.org/';
    $properties['title'] = 'PHP Classes repository logo';
    $properties['description'] = 'Repository of components and other resources for PHP developers';
    $rss_writer_object->addimage($properties);

    /*
     *  Then add your channel items one by one.
     */
$sql = 'SELECT lid, title, logourl, date FROM ' . $xoopsDB->prefix('mydownloads_downloads') . ' ORDER BY lid';
$result = $xoopsDB->query($sql);
    $xoops_url = XOOPS_URL;
while (false !== ($row = $xoopsDB->fetchArray($result))) {
    $properties = [];

    $properties['description'] = $row[title];

    $properties['link'] = XOOPS_URL . '/modules/mydownloads/singlefile.php?lid=';

    $properties['title'] = $row[text];

    $properties['dc:date'] = $row[date];
}
$rss_writer_object->additem($properties);
    /*
     *  If your channel has a search page, after adding the channel items, specify a search form details this way.
     */
    $properties = [];

    /*
     *  The name property if the name of the text input form field on which the user will enter the search word.
     */
    $properties['name'] = 'words';
    $properties['link'] = 'http://www.phpclasses.org/search.html?go_search=1';
    $properties['title'] = 'Search for:';
    $properties['description'] = 'Search in the PHP Classes repository';
    $rss_writer_object->addtextinput($properties);

    /*
     *  When you are done with the definition of the channel items, generate RSS document.
     */
    if ($rss_writer_object->writerss($output)) {
        /*
         *  If the document was generated successfully, you may not output it.
         */

        header('Content-Type: text/xml; charset="' . $rss_writer_object->outputencoding . '"');

        header('Content-Length: ' . (string)mb_strlen($output));

        echo $output;
    } else {
        /*
         *  If there was an error, output it as well.
         */

        header('Content-Type: text/plain');

        echo('Error: ' . $rss_writer_object->error);
    }
    echo '</table></table>'; ?>
	<table>
	<tr>
	<td>      
	<?php

    ########################################################  MODULE LIST ###################################################################
    ?>
</td></tr></table>
