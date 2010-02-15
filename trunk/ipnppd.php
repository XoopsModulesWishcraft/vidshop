<?php
/************************************************************************/
/* Donations - Paypal financial management module for Xoops 2           */
/* Copyright (c) 2004 by Xoops2 Donations Module Dev Team               */
/* http://dev.xoops.org/modules/xfmod/project/?group_id=1060            */
/*                                                                      */
/************************************************************************/
/*                                                                      */
/* Based on NukeTreasury for PHP-Nuke - by Dave Lawrence AKA Thrash     */
/* NukeTreasury - Financial management for PHP-Nuke                     */
/* Copyright (c) 2004 by Dave Lawrence AKA Thrash                       */
/*                       thrash@fragnastika.com                         */
/*                       thrashn8r@hotmail.com                          */
/*                                                                      */
/************************************************************************/
/*                                                                      */
/* This program is free software; you can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/*                                                                      */
/* This program is distributed in the hope that it will be useful, but  */
/* WITHOUT ANY WARRANTY; without even the implied warranty of           */
/* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU     */
/* General Public License for more details.                             */
/*                                                                      */
/* You should have received a copy of the GNU General Public License    */
/* along with this program; if not, write to the Free Software          */
/* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307  */
/* USA                                                                  */
/************************************************************************/

include "../../mainfile.php";

$module_handler =& xoops_gethandler('module');
$xoModule = $module_handler->getByDirname('vidshop');
$config_handler =& xoops_gethandler('config');
$xoConfigs = $config_handler->getConfigList($xoModule->getVar('mid'));

include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

$ERR = 0;
$log = "";
$loglvl = 0;
define('_ERR', 1);
define('_INF', 2);

if( isset($_GET['dbg']) )
        $dbg = 1;
else
        $dbg = 0;

if( $dbg )
{
        dprt(_DM_DEBUGACTIVE, _INF);
        echo _DM_DEBUGHEADER;
        $receiver_email = $xoConfigs['paypalEmail'];
}

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {

        $value = urlencode(stripslashes($value));
        $req .= "&$key=$value";

}

// post back to PayPal system to validate
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

// For Debug Purposes ONLY
//$fp = fsockopen('www.eliteweaver.co.uk', 80, $errno, $errstr, 30);

dprt(_DM_OPENCONN, _INF);

if (!$fp) {
        // HTTP ERROR
        dprt(_DM_CONNFAIL, _ERR);
        die('Failed to post back.');
}

dprt("OK!", _INF);

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$txn_type = $_POST['txn_type'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];


fputs ($fp, $header . $req);

// Perform PayPal email account verification
if( !$dbg && strcasecmp( $_POST['business'], $xoConfigs['paypalEmail']) != 0)
{
        dprt(sprintf(_DM_RCVINVALID,$receiver_email), _ERR) ;
        $ERR = 1;
}

$insertSQL = "";
// Look for duplicate txn_id's
if( $txn_id )
{
        $sql = "SELECT * FROM ".$GLOBALS['xoopsDB']->prefix("vidshop_transactions")." WHERE txn_id = '$txn_id'";
        $Recordset1 = $GLOBALS['xoopsDB']->query($sql);
        $row_Recordset1 = $GLOBALS['xoopsDB']->fetchArray($Recordset1);
        $NumDups = $GLOBALS['xoopsDB']->getRowsNum($Recordset1);
}

while (!$dbg && !$ERR && !feof($fp)) 
{
        $res = fgets ($fp, 1024);
        if (strcmp ($res, "VERIFIED") == 0)
        {
                dprt(_DM_VERIFIED, _INF);
                // Ok, PayPal has told us we have a valid IPN here

                // Check for a reversal for a refund
                if( strcmp($payment_status, "Refunded") == 0)
                {
                        // Verify the reversal
                        dprt(_DM_REFUND, _INF);
                        if( ($NumDups == 0) || strcmp($row_Recordset1['payment_status'], "Completed") || 
                                (strcmp($row_Recordset1['txn_type'], "web_accept") != 0 && strcmp($row_Recordset1['txn_type'], "send_money") != 0) )
                        {
                                // This is an error.  A reversal implies a pre-existing completed transaction
                                dprt(_DM_TRANSMISSING, _ERR);
                                foreach( $_POST as $key => $val )
                                {
                                        dprt("$key => $val", _ERR);
                                }
                                break;
                        }
                        if( $NumDups != 1 )
                        {
                                dprt(_DM_MULTITXNS, _ERR);
                                foreach( $_POST as $key => $val )
                                {
                                        dprt("$key => $val", _ERR);
                                }
                                break;
                        }
                        
                        // We flip the sign of these amount so refunds can be handled correctly
                        $mc_gross = -$_POST['mc_gross'];
                        $mc_fee = -$_POST['mc_fee'];
                        $insertSQL = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("vidshop_transactions")." (`txn_id`,`business`,`item_name`, `item_number`, `quantity`, `invoice`, `custom`, `memo`, `tax`, `option_name1`, `option_selection1`, `option_name2`, `option_selection2`, `payment_status`, `payment_date`, `txn_type`, `mc_gross`, `mc_fee`, `mc_currency`, `settle_amount`, `exchange_rate`, `first_name`, `last_name`, `address_street`, `address_city`, `address_state`, `address_zip`, `address_country`, `address_status`, `payer_email`, `payer_status`) VALUES ('".$_POST['txn_id']."','".$_POST['business']."','".$_POST['item_name']."','".$_POST['item_number']."','".$_POST['quantity']."','".$_POST['invoice']."','".$_POST['custom']."','".$_POST['memo']."','".$_POST['tax']."','".$_POST['option_name1']."','".$_POST['option_selection1']."','".$_POST['option_name2']."','".$_POST['option_selection2']."','".$_POST['payment_status']."','".strftime('%Y-%m-%d %H:%M:%S',strtotime($_POST['payment_date']))."','".$_POST['txn_type']."','$mc_gross','$mc_fee','".$_POST['mc_currency']."','".$_POST['settle_amount']."','".$_POST['exchange_rate']."','".$_POST['first_name']."','".$_POST['last_name']."','".$_POST['address_street']."','".$_POST['address_city']."','".$_POST['address_state']."','".$_POST['address_zip']."','".$_POST['address_country']."','".$_POST['address_status']."','".$_POST['payer_email']."','".$_POST['payer_status']."')";

                        // We're cleared to add this record
                        dprt($insertSQL, _INF);
                        $Result1 = $GLOBALS['xoopsDB']->queryF($insertSQL);
                        dprt("SQL result = " . $Result1, _INF);
                        break;
                } else // Look for anormal payment
                if( (strcmp($payment_status, "Completed") == 0) && ((strcmp($txn_type, "web_accept")== 0) || (strcmp($txn_type, "send_money")== 0)) )
                {
                        dprt("Normal transaction", _INF);
                        if( $lp ) fputs($lp, $payer_email . " " . $payment_status . " " . $_POST['payment_date'] . "\n");

                        // Check for a duplicate txn_id
                        if( $NumDups != 0 )
                        {
                                dprt(_DM_DUPLICATETXN, _ERR);
                                foreach( $_POST as $key => $val )
                                {
                                        dprt("$key => $val", _ERR);
                                }
                                break;
                        }
                        
                        $insertSQL = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("vidshop_transactions")." (`txn_id`,`business`,`item_name`, `item_number`, `quantity`, `invoice`, `custom`, `memo`, `tax`, `option_name1`, `option_selection1`, `option_name2`, `option_selection2`, `payment_status`, `payment_date`, `txn_type`, `mc_gross`, `mc_fee`, `mc_currency`, `settle_amount`, `exchange_rate`, `first_name`, `last_name`, `address_street`, `address_city`, `address_state`, `address_zip`, `address_country`, `address_status`, `payer_email`, `payer_status`) VALUES ('".$_POST['txn_id']."', '".$_POST['business']."', '".$_POST['item_name']."', '".$_POST['item_number']."', '".$_POST['quantity']."', '".$_POST['invoice']."', '".$_POST['custom']."', '".$_POST['memo']."', '".$_POST['tax']."', '".$_POST['option_name1']."', '".$_POST['option_selection1']."', '".$_POST['option_name2']."', '".$_POST['option_selection2']."', '".$_POST['payment_status']."', '".strftime('%Y-%m-%d %H:%M:%S', strtotime($_POST['payment_date']))."', '".$_POST['txn_type']."', '".$_POST['mc_gross']."', '".$_POST['mc_fee']."', '".$_POST['mc_currency']."', '".$_POST['settle_amount']."', '".$_POST['exchange_rate']."', '".$_POST['first_name']."', '".$_POST['last_name']."', '".$_POST['address_street']."', '".$_POST['address_city']."', '".$_POST['address_state']."', '".$_POST['address_zip']."', '".$_POST['address_country']."', '".$_POST['address_status']."', '".$_POST['payer_email']."', '".$_POST['payer_status']."')";

                        // We're cleared to add this record
                        dprt($insertSQL, _INF);
                        $Result1 = $GLOBALS['xoopsDB']->queryF($insertSQL);
                        dprt("SQL result = " . $Result1, _INF);

						list($id, $uid, $created, $ip, $addy, $key, $items) = $GLOBALS['xoopsDB']->fetchRow($GLOBALS['xoopsDB']->queryF('SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('vidshop_video_cart'). ' WHERE key = "'.$_POST['custom'].'"'));
						if ($id>0) {
							$cartItemsHandler =& xoops_getmodulehandler('video_cart_items', 'vidshop');
							$videoHandler =& xoops_getmodulehandler('video', 'vidshop');
							$downloadsHandler =& xoops_getmodulehandler('video_downloads', 'vidshop');
							$items = $cartItemsHandler->getObject(new Criteria('hid', $id));
							foreach($items as $item) {
								$video = $videoHandler->get($item->getVar('vid'));
								$download = $downloadsHandler->create();
								$download->setVar('vid', $item->getVar('vid'));
								$download->setVar('uid', $uid);
								$download->setVar('download', $video->getVar('download'));
								$download->setVar('ip', $ip);
								$download->setVar('addy', $addy);
								$download->setVar('key', $key);
								$download->setVar('created', time());
								@$downloadsHandler->insert($download);
								$sqlg = "DELETE FROM ".$GLOBALS['xoopsDB']->prefix('vidshop_video_cart_items')." WHERE hid = '".$item->getVar('hid')."' AND vid = '".$item->getVar('vid')."'";
								@$GLOBALS['xoopsDB']->queryF($sqlg);
							}
						
						} 
                        break;
                } else // We're not interested in this transaction, so we're done
                {
                        dprt(_DM_NOTINTERESTED, _ERR);
                        foreach( $_POST as $key => $val )
                        {
                                dprt("$key => $val", _ERR);
                        }
                        break;
                }
        }
        elseif (strcmp ($res, "INVALID") == 0) 
        {
                // log for manual investigation
                dprt(_DM_INVALIDIPN, _ERR);
                foreach( $_POST as $key => $val )
                {
                        dprt("$key => $val", _ERR);
                }
                break;
        }
	
}

if( $dbg )
{
        $sql = "SELECT * FROM ".$GLOBALS['xoopsDB']->prefix("vidshop_transactions")." LIMIT 10";
        echo "Executing test query...";
        $Result1 = $GLOBALS['xoopsDB']->query($sql);
        if($Result1)
                echo _DM_DEBUGPASS."<br>";
        else
                echo "<b>"._DM_DEBUGFAIL."</b><br>";
        echo sprintf(_DM_RCVEMAIL,$xoConfigs['paypalEmail']);
}

if( $log )
{
        dprt("<br>"._DM_LOGBEGIN."<br>\n", _INF);
        // Insert the log entry
        $sql = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("vidshop_translog")." VALUES (NULL,'" . strftime('%Y-%m-%d %H:%M:%S',mktime()) . "', '"
                 . strftime('%Y-%m-%d %H:%M:%S',strtotime($_POST['payment_date'])) . "','" . addslashes($log) . "')";
        $Result1 = $GLOBALS['xoopsDB']->queryF($sql);

        // Clear out old log entries
        $sql = "SELECT id as lowid FROM ".$GLOBALS['xoopsDB']->prefix("donations_translog")." ORDER BY id DESC LIMIT " . 30;
        $Result1 = $GLOBALS['xoopsDB']->query($sql);
        while(list($lowid) = $GLOBALS['xoopsDB']->fetchRow($Result1))
        {
                $sql =  "DELETE FROM ".$GLOBALS['xoopsDB']->prefix("vidshop_translog")." WHERE id < '" . $lowid . "'";
                $Result1 = $GLOBALS['xoopsDB']->queryF($sql);
        }
}

fclose ($fp);
if( $lp ) fputs($lp,"Exiting\n");
if( $lp ) fclose ($lp);

if( $dbg)
{
        echo "<hr>";
        echo _DM_IFNOERROR."<br>";
}
        
function dprt($str, $clvl)
{
        global $dbg, $xoopsDB, $lp, $log, $loglvl;

        if( $lp ) 
                fputs($lp, $str . "\n");
        if( $dbg ) 
                echo $str . "<br>";
        if( $clvl <= $loglvl )
                $log .= $str . "\n";
}

?>
