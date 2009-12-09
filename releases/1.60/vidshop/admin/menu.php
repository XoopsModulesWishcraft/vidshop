<?php
if (!defined('XOOPS_ROOT_PATH')) { exit(); }

$adminmenu = array();

$adminmenu[]= array("link"    	=> "admin/admin.php",
                    "title"    	=> _VSP_AM_MAININDEX,
					"icon"		=> "images/vidshopmain.png");
$adminmenu[]= array("link"    	=> "admin/admin.php?op=list",
                    "title"    	=> _VSP_AM_VIDSHOPLIST,
					"icon"		=> "images/videolist.png");
$adminmenu[]= array("link"    	=> "admin/admin.php?op=list&fct=cats",
                    "title"    	=> _VSP_AM_VIDSHOPCATSLIST,
					"icon"		=> "images/videocatslist.png");
$adminmenu[]= array("link"    	=> "admin/admin.php?op=cats&fct=new",
                    "title"    	=> _VSP_AM_VIDSHOPCATSNEW,
					"icon"		=> "images/videocatsnew.png");
$adminmenu[]= array("link"    	=> "admin/admin.php?op=new",
                    "title"    	=> _VSP_AM_VIDSHOPNEWITEM,
					"icon"		=> "images/videocnew.png");

?>