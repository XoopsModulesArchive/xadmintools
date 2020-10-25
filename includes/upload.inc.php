<?php

#---------------------------------------------#
# file upload manager 1.3
# 13.10.2003
#
# written by thepeak (adam johnson)
# copyright (c) 2003 thepeak of mtnpeak.net
#
# www: http://webdev.mtnpeak.net
# www: http://www.xd3v.com
# email: thepeak@gmx.net
#
# A simple, powerful tool to upload and manage
# files using your web browser.
#
# This program is free software; you can redistribute
# it and/or modify it under the terms of the GNU General
# Public License as published by the Free Software
# Foundation; either version 2 of the License, or
# (at your option) any later version, as long as the
# copyright info and links stay intact. You may not sell
# this program under any circumstance without written
# permission from the author. Full license is in the
# included ZIP/GZ package this script was downloaded in.
#
# *please send me feedback - and enjoy!
#---------------------------------------------#
################## configurations ####################
$xpath = 'modules';
# header & title of this file
$title = 'File Upload Manager';
# individual file size limit - in bytes (102400 bytes = 100KB)
$file_size_ind = '10240000';
# the upload store directory (chmod 777)
$dir_store = XOOPS_ROOT_PATH . '/modules';
# the images directory
$dir_img = 'img';
# the style-sheet file to use (located in the "img" directory, excluding .css)
$style = 'style-def';
# the file type extensions allowed to be uploaded
$file_ext_allow = ['zip', 'gz'];
# option to display the file list
# to enable/disable, enter '1' to ENABLE or '0' to DISABLE (without quotes)
$file_list_allow = 1;
# option to allow file deletion
# to enable/disable, enter "1" to ENABLE or '0' to DISABLE (without quotes)
$file_del_allow = 1;
# option to password-protect this script [-part1]
# to enable/disable, enter "1" to ENABLE or "0" to DISABLE (without quotes)
$auth_ReqPass = 0;
# option to password-protect this script [-part2]
# if "" is enabled you must set the username and password
$auth_usern = 'username';
$auth_passw = 'password';
################ end of configurations ###############
