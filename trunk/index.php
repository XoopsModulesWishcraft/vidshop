<?php

	include( '../../mainfile.php' );
	
	$myts =& MyTextSanitizer::getInstance();
	
	$videoCatHandler =& xoops_getmodulehandler('video_category', 'vidshop');
	$videoHandler =& xoops_getmodulehandler('video', 'vidshop');
	
	$op = !empty($_REQUEST['op']) ? strtolower($_REQUEST['op']) : 'new';
	$id = !empty($_REQUEST['id']) ? (int)($_REQUEST['id']) : 0;
	$cid = !empty($_REQUEST['cid']) ? (int)($_REQUEST['cid']) : 0;
	
	$module_handler =& xoops_gethandler('module');
	$xoModule = $module_handler->getByDirname('vidshop');
	$config_handler =& xoops_gethandler('config');
	$xoConfigs = $config_handler->getConfigList($xoModule->getVar('mid'));		

	switch ( $op )	{		
	case 'cat':
		$xoopsOption['template_main'] = "vidshop_vids.html";
		include XOOPS_ROOT_PATH . '/header.php';
			
		$vidcat = $videoCatHandler->get($cid);
		$xoopsTpl->assign('xoops_pagetitle', $vidcat->getVar('name'));
		$xoopsTpl->assign('category', $vidcat->getVar('name'));
		$xoopsTpl->assign('cid', $cid);
		
		$criteria = new Criteria('cid', $cid);
		$criteria->setOrder('RAND()');
		$videos = $videoHandler->getObjects($criteria);	
		if (is_array($videos)) 
			foreach($videos as $key => $object) {
				$videos = array();
				$videos = $object->getValues();
				$videos['description'] = $myts->displayTarea($videos['description'], 1, 1, 1, 1, 1);
				$xoopsTpl->append('videos', $videos);
			}
		$criteria = new Criteria('cid', $cid);
		$criteria->setOrder('RAND()');
		$videos = $videoHandler->getObjects($criteria);	
		if (is_array($videos)) {
			$video = array();
			$video = $videos[0]->getValues();
			$video['description'] = $myts->displayTarea($video['description'], 1, 1, 1, 1, 1);
			$xoopsTpl->assign('video', $video);
			
			include_once XOOPS_ROOT_PATH."/modules/tag/include/tagbar.php";
			$xoopsTpl->assign('tagbar', tagBar($videos[0]->getVar('id'), $videos[0]->getVar('cid')));
	
			$_GET['id'] = $videos[0]->getVar('id');
			include_once XOOPS_ROOT_PATH . "/include/comment_view.php";
		}		
		include XOOPS_ROOT_PATH . '/footer.php';
		exit(0);
	
		break;
	case 'view':
		$xoopsOption['template_main'] = "vidshop_info.html";
		include XOOPS_ROOT_PATH . '/header.php';
		
		$video = $videoHandler->get($id);	
		if (is_object($video)){
			$videoarr = array();
			$videoarr = $video->getValues();
			$videoarr['description'] = $myts->displayTarea($videoarr['description'], 1, 1, 1, 1, 1);
			$xoopsTpl->assign('video', $videoarr);
			$xoopsTpl->assign('xoops_pagetitle', $video->getVar('name'));
			
			include_once XOOPS_ROOT_PATH."/modules/tag/include/tagbar.php";
			$xoopsTpl->assign('tagbar', tagBar($video->getVar('id'), $video->getVar('cid')));
			$_GET['id'] = $video->getVar('id');
			include_once XOOPS_ROOT_PATH . "/include/comment_view.php";
		}
		include XOOPS_ROOT_PATH . '/footer.php';
		exit(0);
		break;
	default:
		$xoopsOption['template_main'] = "vidshop_index.html";
		include XOOPS_ROOT_PATH . '/header.php';
		
		$criteria = new Criteria('weight', '0', '>');
		$criteria->setOrder('weight');
		$categories = $videoCatHandler->getObjects($criteria, true);
		if (is_array($categories))
			foreach($categories as $key => $object)
				$xoopsTpl->append('categories', $object->getValues());
		
		$criteria = new Criteria('uid', '0', '>');
		$criteria->setOrder('RAND()');
		$videos = $videoHandler->getObjects($criteria);	
		if (is_array($videos)) {
			$video = array();
			$video = $videos[0]->getValues();
			$video['description'] = $myts->displayTarea($video['description'], 1, 1, 1, 1, 1);
			$xoopsTpl->assign('video', $video);		
			include_once XOOPS_ROOT_PATH."/modules/tag/include/tagbar.php";
			$xoopsTpl->assign('tagbar', tagBar($videos[0]->getVar('id'), $videos[0]->getVar('cid')));
	
			$_GET['id'] = $videos[0]->getVar('id');
			include_once XOOPS_ROOT_PATH . "/include/comment_view.php";
		}			
		include XOOPS_ROOT_PATH . '/footer.php';
		exit(0);
		break;
	}
?>