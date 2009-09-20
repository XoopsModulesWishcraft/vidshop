<?php
	include ('../../../mainfile.php');

	global $xoopsDB;
	$file = basename((string)$_REQUEST['file']);

	$module_handler =& xoops_gethandler('module');
	$xoModule = $module_handler->getByDirname('vidshop');
	$config_handler =& xoops_gethandler('config');
	$xoConfigs = $config_handler->getConfigList($xoModule->getVar('mid'));
	
	if (is_object($GLOBALS['xoopsUser']))
		$session['uid'] = $GLOBALS['xoopsUser']->getVar('uid');	
	else
		$session['uid'] = 0;	
	$session['ip'] = $_SERVER['REMOTE_ADDR'];
	$session['addy'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	
	$criteria = new CriteriaCompo(new Criteria('ip', $session['ip']), 'AND');
	$criteria->add(new Criteria('addy', $session['addy']), 'AND');
	if ($session['uid']>0)
		$criteria->add(new CriteriaCompo(new Criteria('uid', $session['uid']), 'OR'), 'OR');
	if (isset($_COOKIE['vidshop']['key']))
		$criteria->add(new CriteriaCompo(new Criteria('`key`', $_COOKIE['vidshop']['key']), 'OR'), 'OR');
	$criteria->add(new Criteria('download', $file), 'AND');
	
	$downloadHandler =& xoops_getmodulehandler('video_downloads', 'vidshop');

	if ($downloadHandler->getCount($criteria)===0) {
		header("Location: ".XOOPS_URL);
		exit(0);
	}
	
	$download = $downloadHandler->getAll($criteria);
	if (is_array($download[0]))
		$download = $download[0];
	else {
		header("Location: ".XOOPS_URL);
		exit(0);
	}
	
	if ($download->getVar('downloads')>$xoConfigs['downloads']) {
		$downloadHandler->delete($download);
		header("Location: ".XOOPS_URL);
		exit(0);
	} else {
		$downloads = $download->getVar('downloads');
		$downloads++;
		$download->setVar('downloads', $downloads);
		@$downloadHandler->insert($download);
	}

	if (file_exists($xoConfigs['download_sources'].'/'.$download->getVar('download')))
	{
		header('Content-Disposition: attachment; filename="'.$download->getVar('download').'"');
		header("Content-Type: application/octet-stream");
		readfile($xoConfigs['download_sources'].'/'.$download->getVar('download'));
		exit;
	} else {
		redirect_header(XOOPS_URL.'/index.php', 3, 'File not found');
		exit(0);
	}
	
?>
