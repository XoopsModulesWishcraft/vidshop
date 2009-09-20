<?php
/**
 * Private message module
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code 
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         pm
 * @since           2.3.0
 * @author          Jan Pedersen
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: xoops_version.php 2022 2008-08-31 02:07:17Z phppp $
 */
 
/**
 * This is a temporary solution for merging XOOPS 2.0 and 2.2 series
 * A thorough solution will be available in XOOPS 3.0
 *
 */

$modversion = array();
$modversion['name'] = _VSP_MI_NAME;
$modversion['version'] = 1.52;
$modversion['description'] = _VSP_MI_DESC;
$modversion['author'] = "Simon Roberts (simon@chronolabs.org.au)";
$modversion['credits'] = "To my Friend Gary Arthy";
$modversion['license'] = "SDLC (Software Directive Licence Commercial)";
$modversion['image'] = "images/vidshop_slogo.png";
$modversion['dirname'] = "vidshop";
$modversion['status'] = "stable";

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/admin.php";
$modversion['adminmenu'] = "admin/menu.php";

// Mysql file
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

// Table
$modversion['tables'][1] = "vidshop_video";
$modversion['tables'][2] = "vidshop_video_category";
$modversion['tables'][3] = "vidshop_video_downloads";
$modversion['tables'][4] = "vidshop_video_cart";
$modversion['tables'][5] = "vidshop_video_cart_items";
$modversion['tables'][6] = "vidshop_transactions";
$modversion['tables'][7] = "vidshop_translog";

// Scripts to run upon installation or update
//$modversion['onInstall'] = "include/install.php";
//$modversion['onUpdate'] = "include/update.php";

$videoHandler =& xoops_getmodulehandler('video_category', 'vidshop');
$criteria = new CriteriaCompo(new Criteria('weight', '0', '>'));
$criteria->setOrder('weight');
$videoCats = $videoHandler->getObjects($criteria, true);

$ii++;
$modversion['sub'][$ii]['name'] = _VSP_SHOPPING_CART;
$modversion['sub'][$ii]['url'] = "cart.php";

$downloadsHandler =& xoops_getmodulehandler('video_downloads', 'vidshop');
$criteria = new CriteriaCompo(new Criteria('ip', $session['ip']), 'AND');
$criteria->add(new Criteria('addy', $session['addy']), 'AND');
if ($session['uid']>0)
	$criteria->add(new CriteriaCompo(new Criteria('uid', $session['uid']), 'OR'), 'OR');
if (isset($_COOKIE['vidshop']['key']))
	$criteria->add(new CriteriaCompo(Criteria('key', $_COOKIE['vidshop']['key']), 'OR'), 'OR');		
if ($downloadsHandler->getCount($criteria)>0) {
	$ii++;
	$modversion['sub'][$ii]['name'] = _VSP_SHOPPING_DOWNLOADS;
	$modversion['sub'][$ii]['url'] = "downloads.php";
}

if (is_array($videoCats))
foreach ($videoCats as $id => $videocat) {
	$ii++;
	$modversion['sub'][$ii]['name'] = $videocat->getVar('name');
	$modversion['sub'][$ii]['url'] = "index.php?op=cat&cid=".$id;
}

// Templates
$modversion['templates'] = array();
$modversion['templates'][1]['file'] = 'vidshop_index.html';
$modversion['templates'][1]['description'] = '';
$modversion['templates'][2]['file'] = 'vidshop_categories.html';
$modversion['templates'][2]['description'] = '';
$modversion['templates'][3]['file'] = 'vidshop_info.html';
$modversion['templates'][3]['description'] = '';
$modversion['templates'][4]['file'] = 'vidshop_category.html';
$modversion['templates'][4]['description'] = '';
$modversion['templates'][5]['file'] = 'vidshop_vids.html';
$modversion['templates'][5]['description'] = '';
$modversion['templates'][6]['file'] = 'vidshop_checkout.html';
$modversion['templates'][6]['description'] = '';
$modversion['templates'][7]['file'] = 'vidshop_cart.html';
$modversion['templates'][7]['description'] = '';
$modversion['templates'][8]['file'] = 'vidshop_download.html';
$modversion['templates'][8]['description'] = '';

// Comments
$modversion['hasComments'] = 1;
$modversion['comments']['itemName'] = 'id';
$modversion['comments']['pageName'] = 'index.php';

// Comment callback functions
$modversion['comments']['callbackFile'] = 'include/comment_functions.php';
$modversion['comments']['callback']['approve'] = 'vidshop_com_approve';
$modversion['comments']['callback']['update'] = 'vidshop_com_update';

// Menu
$modversion['hasMain'] = 1;

$modversion['config'] = array();
$modversion['config'][]=array(
	'name' => 'download_sources',
	'title' => '_VSP_MI_FILE_SOURCES',
	'description' => '_VSP_MI_FILE_SOURCES_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => XOOPS_VAR_PATH.'/downloads');

$modversion['config'][]=array(
	'name' => 'download_spot',
	'title' => '_VSP_MI_FILE_SPOT',
	'description' => '_VSP_MI_FILE_SPOT_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => XOOPS_URL.'/modules/vidshop/bin');
	
$modversion['config'][]=array(
	'name' => 'downloads',
	'title' => '_VSP_MI_DOWNLOADS',
	'description' => '_VSP_MI_DOWNLOADS_DESC',
	'formtype' => 'select',
	'valuetype' => 'int',
	'options' => array(2 => 2, 4 => 4, 6 => 6, 10 => 10, 12 => 12, 15 => 15, 17 => 17, 20 => 20, 30 => 30, 35 => 35, 40 => 40, 60 => 60, 80 => 80, 100 => 100),
	'default' => 6);


$modversion['config'][]=array(
	'name' => 'daysonpayment',
	'title' => '_VSP_MI_DAYSONPAYMENT',
	'description' => '_VSP_MI_DAYSONPAYMENT_DESC',
	'formtype' => 'select',
	'valuetype' => 'int',
	'options' => array(2 => 2, 4 => 4, 6 => 6, 10 => 10, 12 => 12, 15 => 15, 17 => 17, 20 => 20, 30 => 30, 35 => 35, 40 => 40, 60 => 60, 80 => 80, 100 => 100),
	'default' => 6);

$modversion['config'][]=array(
	'name' => 'paypalEmail',
	'title' => '_VSP_MI_PAYPAL_EMAIL',
	'description' => '_VSP_MI_PAYPAL_EMAIL_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => 'earplugz@hotmail.com');

$modversion['config'][]=array(
	'name' => 'paypalitemSprintf',
	'title' => '_VSP_MI_PAYPAL_ITEM',
	'description' => '_VSP_MI_PAYPAL_ITEM_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => 'Purchase of %s video titles from Bee Unlimited');
	
$modversion['config'][]=array(
	'name' => 'paypalCurrency',
	'title' => '_VSP_MI_PAYPAL_CURRENCY',
	'description' => '_VSP_MI_PAYPAL_CURRENCY_DESC',
	'formtype' => 'select',
	'valuetype' => 'text',
	'options' => array('GBP' => 'GBP', 'AUD' => 'AUD', 'USD' => 'USD', 'HKD' => 'HKD'),
	'default' => 'GBP');

?>