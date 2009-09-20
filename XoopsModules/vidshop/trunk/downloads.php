<?php

	include( '../../mainfile.php' );
	include( '/include/form.vidshop.php' );
	
	$module_handler =& xoops_gethandler('module');
	$xoModule = $module_handler->getByDirname('vidshop');
	$config_handler =& xoops_gethandler('config');
	$xoConfigs = $config_handler->getConfigList($xoModule->getVar('mid'));		

	$xoopsOption['template_main'] = "vidshop_download.html";
	include XOOPS_ROOT_PATH . '/header.php';
	$form['downloads'] = formDownloads($_COOKIE['vidshop']['key']);
	$xoopsTpl->assign('form', $form);
	include XOOPS_ROOT_PATH . '/footer.php';
		
?>