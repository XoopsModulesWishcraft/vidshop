<?php
	
	include 'header.php';
	include_once '../include/form.vidshop.php';
	include_once '../include/functions.php';
	include_once '../../../class/pagenav.php';
	
	$videoHandler =& xoops_getmodulehandler('video', 'vidshop');
	$videoCatHandler =& xoops_getmodulehandler('video_category', 'vidshop');
	
	$categorycount = $videoCatHandler->getCount(NULL); 
	$videocount = $videoHandler->getCount(NULL);
	
	$op = !empty($_REQUEST['op'])?strtolower($_REQUEST['op']):'default';
	$id = !empty($_REQUEST['id']) ?(int)($_REQUEST['id']):0;
	$itmppg = !empty($_REQUEST['itmppg']) ?(int)($_REQUEST['itmppg']):30;
	$start = !empty($_REQUEST['start']) ?(int)($_REQUEST['start']):0;
	
	xoops_cp_header();
	
	switch ($op) {
	case 'delete':
		$sql[0] = "DELETE FROM ".$GLOBALS['xoopsDB']->prefix('vidshop_video') . ' WHERE id = '.$id;
		if ($GLOBALS['xoopsDB']->queryF($sql[0]))
			redirect_header(XOOPS_URL.'/modules/vidshop/admin/admin.php?op=list', 4, sprintf(_VSP_AM_VIDEODELETE_SUCCESSFUL));
		else
			redirect_header(XOOPS_URL.'/modules/vidshop/admin/admin.php?op=list', 4, sprintf(_VSP_AM_VIDEODELETE_UNSUCCESSFUL, $video->getVar('name')));					
		exit(0);
	case 'cats':
		switch ($_REQUEST['fct'])
		{
		case "edit":
			echo function_exists("loadModuleAdminMenu") ? loadModuleAdminMenu(3) : "";
			echo formVideoCats($id);
			break;
		case "new":
			echo function_exists("loadModuleAdminMenu") ? loadModuleAdminMenu(3) : "";
			echo formVideoCats($id);
			break;		
		case "save":
			if (intval($_REQUEST['cid'])>0)
				saveEditVideoCategory(intval($_REQUEST['cid']));
			else
				saveNewVideoCategory(intval($_REQUEST['cid']));
		}	
		break;	
	case 'list':
		
		switch ($_REQUEST['fct']) {
		case "cats":
			echo function_exists("loadModuleAdminMenu") ? loadModuleAdminMenu(2) : "";
	
			$pgnav = new XoopsPageNav($categorycount, $itmppg, $start);
			$criteria = new Criteria('weight', '0', '>');
	
			$criteria->setOrder('weight');
			$criteria->setStart($start);
			$criteria->setLimit($itmppg);
			
			echo '<div style="clear:both; float:right;">' . $pgnav->renderNav() . '</div>';		
			echo formCatsList($videoCatHandler->getObjects($criteria, true), $videoCatHandler);
			break;
		default:
			echo function_exists("loadModuleAdminMenu") ? loadModuleAdminMenu(1) : "";
	
			$pgnav = new XoopsPageNav($videocount, $itmppg, $start);
			$criteria = new Criteria('uid', '0', '>');
	
			$criteria->setOrder('created');
			$criteria->setStart($start);
			$criteria->setLimit($itmppg);
			
			echo '<div style="clear:both; float:right;">' . $pgnav->renderNav() . '</div>';		
			echo formList($videoHandler->getObjects($criteria, true), $videoHandler);
			break;
		}		
		break;
	default:
	case 'edit':
		
		
		switch ($_REQUEST['fct']) {
		case "save":
			if ($_REQUEST['description_editor_current'] != $_REQUEST['description_editor'] ) {
				echo function_exists("loadModuleAdminMenu") ? loadModuleAdminMenu(1) : "";
				echo formVideo($id);
				xoops_cp_footer();
				exit(0);
			}

			saveEditVideo(intval($_REQUEST['id']));
			break;

		default:
			echo function_exists("loadModuleAdminMenu") ? loadModuleAdminMenu(1) : "";
			echo formVideo($id);
			break;

		}

		break;		
	case 'new':
		switch ($_REQUEST['fct']) {
		case "save":
			if ($_REQUEST['description_editor_current'] != $_REQUEST['description_editor'] ) {
				echo function_exists("loadModuleAdminMenu") ? loadModuleAdminMenu(4) : "";
				echo formVideo($id);
				xoops_cp_footer();
				exit(0);
			}

			saveNewVideo();
			break;

		default:
			echo function_exists("loadModuleAdminMenu") ? loadModuleAdminMenu(4) : "";
			echo formVideo($id);
			break;

		}
		break;		
	}
	
	xoops_cp_footer();
?>

