<?php
include 'admin_header.php';
include '../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

header('Content-type: text/xml');

echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
?>
<rdf:RDF xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:h="http://www.w3.org/1999/xhtml" xmlns:hr="http://www.w3.org/2000/08/w3c-synd/#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://purl.org/rss/1.0/">
    <channel rdf:about="<?php echo $xoopsConfig['sitename']; ?>">
    </channel>
<?php
$xoops_url = XOOPS_URL;

$sql = 'SELECT lid, title, logourl, date FROM ' . $xoopsDB->prefix('mydownloads_downloads') . ' ORDER BY lid';
$result = $xoopsDB->query($sql);
while (false !== ($row = $xoopsDB->fetchArray($result))) {
    echo '<title>';

    echo $row[title];

    echo('</title>
        <link>' . XOOPS_URL . '/modules/mydownloads/singlefile.php?lid=');

    echo $row[lid];

    echo('</link>');

    echo $row[date];
}

mysql_free_result($result);
?>
</rdf:RDF> 


