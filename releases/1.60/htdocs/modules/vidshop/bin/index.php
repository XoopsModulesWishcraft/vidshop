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
	
	$criteria_a = new CriteriaCompo(new Criteria('ip', $session['ip']), 'AND');
	$criteria_a->add(new Criteria('addy', $session['addy']), 'AND');
	if ($session['uid']>0)
		$criteria_a->add(new CriteriaCompo(new Criteria('uid', $session['uid']), 'OR'), 'OR');
	if (isset($_COOKIE['vidshop']['key']))
		$criteria_a->add(new CriteriaCompo(new Criteria('`key`', $_COOKIE['vidshop']['key']), 'OR'), 'OR');
	$criteria_b = new Criteria('download', $file);
	$criteria = new CriteriaCompo($criteria_a, 'AND');
	$criteria->add($criteria_b, 'AND');
	$downloadHandler =& xoops_getmodulehandler('video_downloads', 'vidshop');

	if ($downloadHandler->getCount($criteria)==0) {
		header("Location: ".XOOPS_URL);
		exit(0);
	}
	
	$download = $downloadHandler->getObjects($criteria, false);
	if (isset($download[0]))
		$download = $download[0];
	else {
		header("Location: ".XOOPS_URL);
		exit(0);
	}
	
	if (intval($download->getVar('downloads'))>intval($xoConfigs['downloads'])) {
		$downloadHandler->delete($download, true);
		redirect_header('../index.php', 3, _VSP_MAXIMUM_DOWNLOADS);
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
		redirect_header(XOOPS_URL.'/index.php', 3, _VSP_FILENOTFOUND);
		exit(0);
	}
	
?>
