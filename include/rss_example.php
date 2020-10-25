<?php
require 'xmlwriterclass.php';
    require 'rss_writer_class.php';
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
