<?php

/*##############################################

RSS parser written for bMachine feed.
This script can be used to read ANY kind of RSS feeds from anywhere!


*--------------------------------------------*
Written by Kailash Nadh,
http://bnsoft.net , kailash@bnsoft.net

Author of bMachine, http://boastology.com

I wrote this script on the 1st day I studied
XML by running through the documentation at www.php.net ;)
*--------------------------------------------*

WARNING!: This script is Heavily commented! :)


This is how the script works.
> Reads the XML file
> Parses it into an array using xml_parse_into_struct()
> Converts that array into a more sensible, easily usable array
    [ See the structure.txt file to get an idea of the array structure ]
> Finally, Display the data in anyway you want!

This script demonstrates the use of simple logic
to do Complex XML/RSS parsing functions
This script can be easily developed into a powerful application.

##############################################*/

$url = 'http://stigmataband.tswn.com/modules/xadmintools/downloadrss.php';
    // Enter the url to an RSS feed

$data = @fread(fopen((string)$url, 'rb'), 10000) || die("Cant open $url!");
    // Get the file contents

$myar = getXmlData($data);
    // What ever it may be, the argument to this
    // function should be the XML document's content

// Now $myar holds the fully parsed XML contents.
// It may have a number of tags, like <TITLE> , <LINK> , <AUTHOR> , <SOMETHING> ... and so on
// With a simple loop, you can extract all the tags and their values neatly

// Remember, $i<= should be lesser than or equal to the number of values
// a tag has. If you give count($myar), it will only take the number of tags.
// See structure.txt for better understanding.

for ($i = 0, $iMax = count($myar[title]); $i <= $iMax; $i++) {
    // Here, we want to read the TITLE, DESCRIPTION, and LINK of the RSS feed.

    // You can read the values of any tag like this.

    // $myar[TITLE][$i], $myar[SOMETHING][$i], $myar[SOMETAG][$i].... and so on..

    // Here it goes

    $title = $myar[title][$i];

    $text = $myar[description][$i];

    $link = $myar[link][$i];

    if ($title) {
        echo <<<EOF
<a href="$link"><font size="2" face="Verdana" color="blue">$title</font></a><br>
<font size="2" face="Verdana">$text</font><br><br>
<hr width="50%" size="1" align="left">
EOF;
    }
}

//#####################################################################

// This is the function. It returns the array of the parsed XML data

function getXmlData($xml_doc)
{
    $n = 0; 		 // Counter used for arraying the XML data
$ar = []; // The main array for storing parsed xml using xml_parse_into_struct()

// Parse the XML document

    $parser = xml_parser_create();

    xml_parse_into_struct($parser, $xml_doc, $vals, $index) || die(xml_error_string(xml_get_error_code($parser)));

    xml_parser_free($parser);

    $ttags = []; // Temporary arry for storing tag names

    // The main part. This is MY CREATION

    // and this piece of code makes this script simple :)

    // This is the "MAGIC LOOP" !! :)

    for ($n = 0; $n <= count($vals) - 1; $n++) {
        if (trim($vals[$n][value])) {
            $ar[$vals[$n][tag]][count($ar[$vals[$n][tag]])] = $vals[$n][value];

            $ttags[$vals[$n][tag]] = $vals[$n][tag];
        }
    }

    // Array for storing all the tag names

    // This array will hold all the Tag names found in the XML document

    // eg: ("TITLE","LINK","AUTHOR","DOMAIN")..

    // Use this if you need it.

    $tags = [];

    // Extract and save the tag names to the array

    foreach ($ttags as $tagi) {
        $tags[] = $tagi;
    }

    return $ar;
}
