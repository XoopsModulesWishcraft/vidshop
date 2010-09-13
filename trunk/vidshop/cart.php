<?php

	include( '../../mainfile.php' );
	include( 'include/form.vidshop.php' );

	$cartHandler =& xoops_getmodulehandler('video_cart', 'vidshop');

	$op = !empty($_REQUEST['op']) ? strtolower($_REQUEST['op']) : 'new';
	
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
	}
	
	switch($op) {
	default:
		$xoopsOption['template_main'] = "vidshop_cart.html";
		include XOOPS_ROOT_PATH . '/header.php';
		$form['cart'] = formCart($cart->getVar('key'));
		$xoopsTpl->assign('form', $form);
		include XOOPS_ROOT_PATH . '/footer.php';
		break;
	case "checkout":
		$xoopsOption['template_main'] = "vidshop_checkout.html";
		include XOOPS_ROOT_PATH . '/header.php';
		$form['items'] = formCart($cart->getVar('key'));
		$form['checkout'] = formCheckout();
		$xoopsTpl->assign('form', $form);
		include XOOPS_ROOT_PATH . '/footer.php';
		break;
	}
		
?>