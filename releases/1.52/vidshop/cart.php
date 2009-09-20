<?php

	include( '../../mainfile.php' );
	include( 'include/form.vidshop.php' );
	
	$op = !empty($_REQUEST['op']) ? strtolower($_REQUEST['op']) : 'new';
	
	switch($op) {
	default:
		$xoopsOption['template_main'] = "vidshop_cart.html";
		include XOOPS_ROOT_PATH . '/header.php';
		$form['cart'] = formCart($_COOKIE['vidshop']['key']);
		$xoopsTpl->assign('form', $form);
		include XOOPS_ROOT_PATH . '/footer.php';
		break;
	case "checkout":
		$xoopsOption['template_main'] = "vidshop_checkout.html";
		include XOOPS_ROOT_PATH . '/header.php';
		$form['items'] = formCart($_COOKIE['vidshop']['key']);
		$form['checkout'] = formCheckout();
		$xoopsTpl->assign('form', $form);
		include XOOPS_ROOT_PATH . '/footer.php';
		break;
	}
		
?>