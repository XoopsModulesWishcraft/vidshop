<?php
	
	include( '../../mainfile.php' );
	
	$id = (isset($_REQUEST['id']))?intval($_REQUEST['id']):0;
	$cid = (isset($_REQUEST['cid']))?intval($_REQUEST['cid']):0;
	$op = (isset($_REQUEST['op']))?($_REQUEST['op']):'addtocart';
	$uri = (isset($_REQUEST['uri']))?($_REQUEST['uri']):XOOPS_URL."/modules/vidshop/?id=$id&cid=$cid&op=view";
	
	$videoHandler =& xoops_getmodulehandler('video', 'vidshop');
	$cartHandler =& xoops_getmodulehandler('video_cart', 'vidshop');
	$cartItemsHandler =& xoops_getmodulehandler('video_cart_items', 'vidshop');
	
	if (is_object($GLOBALS['xoopsUser']))
		$session['uid'] = $GLOBALS['xoopsUser']->getVar('uid');	
	else
		$session['uid'] = 0;	
	$session['ip'] = $_SERVER['REMOTE_ADDR'];
	$session['addy'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			
	if (isset($_COOKIE['vidshop']['key']))
		$criteria = new Criteria('`key`', $_COOKIE['vidshop']['key']);
	else {
		$criteria = new CriteriaCompo(new Criteria('ip', $session['ip']), 'AND');
		$criteria->add(new Criteria('addy', $session['addy']), 'AND');
		if ($session['uid']>0)
			$criteria->add(new CriteriaCompo(new Criteria('uid', $session['uid']), 'OR'), 'OR');
	}
	
	$cart = $cartHandler->getObjects($criteria);
	
	if (isset($cart[0])) {
		$cart = $cart[0];
		$_COOKIE['vidshop']['key'] = $cart->getVar('key');
	} else {
		$cart = $cartHandler->create();
		$cart->setVar('uid', $session['uid']);
		$cart->setVar('ip', $session['ip']);
		$cart->setVar('addy', $session['addy']);
		$cart->setVar('created', time());
		$cart->setVar('key', $cartHandler->GenerateKey());
		$_COOKIE['vidshop']['key'] = $cart->getVar('key');
		@$cartHandler->insert($cart);
		$criteria = new Criteria('`key`', $cart->getVar('key'));
		$cart = $cartHandler->getObjects($criteria);
		if (isset($cart[0])) {
			$cart = $cart[0];
			$_COOKIE['vidshop']['key'] = $cart->getVar('key');
		}
	}
	
	switch($op) {
	default:
	case 'addtocart':
		if ($cid>0)
			$criteria = new criteria('cid', $cid);
		else
			$criteria = new criteria('id', $id);
			
		$videos = $videoHandler->getObjects($criteria, true);
		foreach($videos as $key => $object) {
			$criteria = new CriteriaCompo(new Criteria('hid', $cart->getVar('id')));
			$criteria->add(new Criteria('vid', $key));		
			if ($cartItemsHandler->getCount($criteria)==0) {
				$item = $cartItemsHandler->create();
				$item->setVar('hid', $cart->getVar('id'));
				$item->setVar('vid', $key);
				$cartItemsHandler->insert($item, true);
				$ii++;
			}
		}
		$items = $cart->getVar('items');
		$items = $items + $ii;
		$cart->setVar('items', $items);
		@$cartHandler->insert($cart);
		redirect_header($uri, 3, sprintf(_VSP_SCOOP_ITEMSADDED, $ii, $items));
		exit(0);
		break;
	case 'removefromcart':
		if ($cid>0)
			$criteria = new criteria('cid', $cid);
		else
			$criteria = new criteria('id', $id);
			
		$videos = $videoHandler->getObjects($criteria, true);
		foreach($videos as $key => $object) {
			$criteria = new CriteriaCompo(new Criteria('hid', $cart->getVar('id')));
			$criteria->add(new Criteria('vid', $key));
			if ($cartItemsHandler->getCount($criteria)>0) {
				$sql = "DELETE FROM ".$GLOBALS['xoopsDB']->prefix('vidshop_video_cart_items')." WHERE hid = '".$cart->getVar('id')."' AND vid = '$key'";
				@$GLOBALS['xoopsDB']->queryF($sql);
				$ii++;
			}
		}
		$items = $cart->getVar('items');
		$items = $items - $ii;
		$cart->setVar('items', $items);
		@$cartHandler->insert($cart);
		redirect_header($uri, 3, sprintf(_VSP_SCOOP_ITEMSREMOVED, $ii, $items));
		exit(0);
		break;
	}
	
?>