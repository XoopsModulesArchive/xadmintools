<?php

/*
 *      file:///C|/DOCUME%7E1/TALUNC%7E1/LOCALS%7E1/Temp/Rar$DI00.406/rssparse.php
 *
 *      Copyright (C) 2000 Jeremey Barrett
 *      j@nwow.org
 *      http://nwow.org
 *
 *      Version 0.4
 *
 *      This library is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU Lesser General Public License as published by
 *      the Free Software Foundation; either version 2.1 of the License, or
 *      (at your option) any later version.
 *
 *      This library is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU Lesser General Public License for more details.
 *
 *      You should have received a copy of the GNU Lesser General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation,Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 *
 *      file:///C|/DOCUME%7E1/TALUNC%7E1/LOCALS%7E1/Temp/Rar$DI00.406/rssparse.php is a small PHP script for parsing RDF/RSS XML data. It has been
 *      tested with a number of popular web news and information sites such as
 *      slashdot.org, lwn.net, and freshmeat.net. This is not meant to be exhaustive
 *      but merely to provide the basic necessities. It will grow in capabilities
 *      with time, I'm sure.
 *
 *      This is code I wrote for Nerds WithOut Wires, http://nwow.org.
 *
 *
 *      USAGE:
 *      In your PHP script, simply do something akin to this:
 *
 *      include "file:///C|/DOCUME%7E1/TALUNC%7E1/LOCALS%7E1/Temp/Rar$DI00.406/rssparse.php";
 *      $fp = fopen("file", "r");
 *      $rss = rssparse($fp);
 *
 *      if (!$rss) {
 *          ERROR;
 *      }
 *
 *      while (list(,$item) = each($rss->items)) {
 *          printf("Title: %s\n", $item["title"]);
 *          printf("Link: %s\n", $item["link"]);
 *          printf("Description: %s\n", $item["desc"]);
 *      }
 *
 *      printf("Channel Title: %s\n", $rss->title);
 *      printf("Channel Description: %s\n", $rss->desc);
 *      printf("Channel Link: %s\n", $rss->link);
 *
 *      printf("Image Title: %s\n", $rss->image["title"]);
 *      printf("Image URL: %s\n", $rss->image["url"]);
 *      printf("Image Description: %s\n", $rss->image["desc"]);
 *      printf("Image Link: %s\n", $rss->image["link"]);
 *
 *
 *      CHANGES:
 *      0.4 - rssparse.php now supports the channel image tag and correctly supports
 *            RSS-style channels.
 *
 *
 *      BUGS:
 *      Width and height tags in image not supported, some other tags not supported
 *      yet.
 *
 *
 *      IMPORTANT NOTE:
 *      This requires PHP's XML routines. You must configure PHP with --with-xml.
 */

function _rssparse_start_elem($parser, $elem, $attrs)
{
    global $_rss;

    if ('CHANNEL' == $elem) {
        $_rss->depth++;

        $_rss->state[$_rss->depth] = 'channel';

        $_rss->tmptitle[$_rss->depth] = '';

        $_rss->tmplink[$_rss->depth] = '';

        $_rss->tmpdesc[$_rss->depth] = '';
    } elseif ('IMAGE' == $elem) {
        $_rss->depth++;

        $_rss->state[$_rss->depth] = 'image';

        $_rss->tmptitle[$_rss->depth] = '';

        $_rss->tmplink[$_rss->depth] = '';

        $_rss->tmpdesc[$_rss->depth] = '';

        $_rss->tmpurl[$_rss->depth] = '';
    } elseif ('ITEM' == $elem) {
        $_rss->depth++;

        $_rss->state[$_rss->depth] = 'item';

        $_rss->tmptitle[$_rss->depth] = '';

        $_rss->tmplink[$_rss->depth] = '';

        $_rss->tmpdesc[$_rss->depth] = '';
    } elseif ('TITLE' == $elem) {
        $_rss->depth++;

        $_rss->state[$_rss->depth] = 'title';
    } elseif ('LINK' == $elem) {
        $_rss->depth++;

        $_rss->state[$_rss->depth] = 'link';
    } elseif ('DESCRIPTION' == $elem) {
        $_rss->depth++;

        $_rss->state[$_rss->depth] = 'desc';
    } elseif ('URL' == $elem) {
        $_rss->depth++;

        $_rss->state[$_rss->depth] = 'url';
    }
}

function _rssparse_end_elem($parser, $elem)
{
    global $_rss;

    if ('CHANNEL' == $elem) {
        $_rss->set_channel($_rss->tmptitle[$_rss->depth], $_rss->tmplink[$_rss->depth], $_rss->tmpdesc[$_rss->depth]);

        $_rss->depth--;
    } elseif ('IMAGE' == $elem) {
        $_rss->set_image($_rss->tmptitle[$_rss->depth], $_rss->tmplink[$_rss->depth], $_rss->tmpdesc[$_rss->depth], $_rss->tmpurl[$_rss->depth]);

        $_rss->depth--;
    } elseif ('ITEM' == $elem) {
        $_rss->add_item($_rss->tmptitle[$_rss->depth], $_rss->tmplink[$_rss->depth], $_rss->tmpdesc[$_rss->depth]);

        $_rss->depth--;
    } elseif ('TITLE' == $elem) {
        $_rss->depth--;
    } elseif ('LINK' == $elem) {
        $_rss->depth--;
    } elseif ('DESCRIPTION' == $elem) {
        $_rss->depth--;
    } elseif ('URL' == $elem) {
        $_rss->depth--;
    }
}

function _rssparse_elem_data($parser, $data)
{
    global $_rss;

    if ('title' == $_rss->state[$_rss->depth]) {
        $_rss->tmptitle[($_rss->depth - 1)] .= $data;
    } elseif ('link' == $_rss->state[$_rss->depth]) {
        $_rss->tmplink[($_rss->depth - 1)] .= $data;
    } elseif ('desc' == $_rss->state[$_rss->depth]) {
        $_rss->tmpdesc[($_rss->depth - 1)] .= $data;
    } elseif ('url' == $_rss->state[$_rss->depth]) {
        $_rss->tmpurl[($_rss->depth - 1)] .= $data;
    }
}

class rssparser
{
    public $title;

    public $link;

    public $desc;

    public $items = [];

    public $nitems;

    public $image = [];

    public $state = [];

    public $tmptitle = [];

    public $tmplink = [];

    public $tmpdesc = [];

    public $tmpurl = [];

    public $depth;

    public function __construct()
    {
        $this->nitems = 0;

        $this->depth = 0;
    }

    public function set_channel($in_title, $in_link, $in_desc)
    {
        $this->title = $in_title;

        $this->link = $in_link;

        $this->desc = $in_desc;
    }

    public function set_image($in_title, $in_link, $in_desc, $in_url)
    {
        $this->image['title'] = $in_title;

        $this->image['link'] = $in_link;

        $this->image['desc'] = $in_desc;

        $this->image['url'] = $in_url;
    }

    public function add_item($in_title, $in_link, $in_desc)
    {
        $this->items[$this->nitems]['title'] = $in_title;

        $this->items[$this->nitems]['link'] = $in_link;

        $this->items[$this->nitems]['desc'] = $in_desc;

        $this->nitems++;
    }

    public function parse($fp)
    {
        $xml_parser = xml_parser_create();

        xml_set_elementHandler($xml_parser, '_rssparse_start_elem', '_rssparse_end_elem');

        xml_set_character_dataHandler($xml_parser, '_rssparse_elem_data');

        while ($data = fread($fp, 4096)) {
            if (!xml_parse($xml_parser, $data, feof($fp))) {
                return 1;
            }
        }

        xml_parser_free($xml_parser);

        return 0;
    }
}

function rssparse($fp)
{
    global $_rss;

    $_rss = new rssparser();

    if ($_rss->parse($fp)) {
        return 0;
    }

    return $_rss;
}
