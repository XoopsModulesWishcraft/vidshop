<?php

	include $GLOBALS['xoops']->path('/class/uploader.php');
	
	function saveEditVideo($video_id)
	{
		$videoHandler =& xoops_getmodulehandler('video', 'vidshop');
		$video = $videoHandler->get( $video_id );
				
		$video->setVar('cid', $_POST["cid"]);
		$video->setVar('name', $_POST["name"]);
		$video->setVar('description', $_POST["description"]);
		if (is_object($GLOBALS['xoopsUser']))
			$video->setVar('uid', $GLOBALS['xoopsUser']->getVar('uid'));
		$video->setVar('created', time());			
		$video->setVar('video_tags', $_POST["video_tags"]);
		$video->setVar('embedded', $_POST["embedded"]);
		$video->setVar('price', $_POST["price"]);
		$video->setVar('download', $_POST["file"]);
		
		if(isset($_POST['xoops_upload_file'])) {
			$fldname = $_FILES[$_POST['xoops_upload_file'][0]];
			$fldname = (get_magic_quotes_gpc()) ? stripslashes($fldname['name']) : $fldname['name'];
			if(xoops_trim($fldname!='')) {
				$destname = md5(time()).'_'.$fldname;
				/**
				* You can attach files to your news, actually : Web pictures (png, gif, jpeg), zip, pdf, gtar, tar, pdf
				*/
				$permittedtypes=array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png' );
				$uploader = new XoopsMediaUploader( XOOPS_UPLOAD_PATH, $permittedtypes, 1024*1024*3);
				$uploader->setTargetFileName($destname);
				if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
					if ($uploader->upload()) {
						$video->setVar('image', str_replace(XOOPS_ROOT_PATH, '', $destname));
					} else {
						echo _AM_UPLOAD_ERROR. ' ' . $uploader->getErrors();
					}
				} else {
					echo $uploader->getErrors();
				}
			}
		}
		if ($videoHandler->insert($video)) {
			$video_id = $video_id;
			
			if (file_exists(XOOPS_ROOT_PATH.'/modules/tag/class/tag.php')) {
				$tag_handler = xoops_getmodulehandler('tag', 'tag');
				$tag_handler->updateByItem($_REQUEST["video_tags"], $video_id, $GLOBALS['xoopsModule']->getVar("dirname"), intval($_REQUEST["cid"]));
			}
								
			if (!strpos($_SERVER['REQUEST_URI'], '/vidshop/admin/'))
				redirect_header('admin.php?op=edit&id='.$video_id, 8, sprintf(_VSP_RH_VIDSHOP_EDITED, $video->getVar('name')));
			else
				redirect_header('admin.php?op=list', 8, sprintf(_VSP_RH_VIDSHOP_EDITED, $video->getVar('name')));
			exit(0);	
		} else {
			redirect_header('admin.php', 4, sprintf(_VSP_RH_VIDSHOP_NOCREATION, $video->getVar('name'), implode('<br/>',$video->getErrors())));
			exit(0);		
		}
	}
	
	function saveNewVideo() {

		$videoHandler =& xoops_getmodulehandler('video', 'vidshop');
		$video = $videoHandler->create();
		
		$video->setVar('cid', $_POST["cid"]);
		$video->setVar('name', $_POST["name"]);
		$video->setVar('description', $_POST["description"]);
		if (is_object($GLOBALS['xoopsUser']))
			$video->setVar('uid', $GLOBALS['xoopsUser']->getVar('uid'));
		$video->setVar('created', time());			
		$video->setVar('video_tags', $_POST["video_tags"]);
		$video->setVar('embedded', $_POST["embedded"]);
		$video->setVar('price', $_POST["price"]);
		$video->setVar('download', $_POST["file"]);
		
		if(isset($_POST['xoops_upload_file'])) {
			$fldname = $_FILES[$_POST['xoops_upload_file'][0]];
			$fldname = (get_magic_quotes_gpc()) ? stripslashes($fldname['name']) : $fldname['name'];
			if(xoops_trim($fldname!='')) {
				$destname = md5(time()).'_'.$fldname;
				/**
				* You can attach files to your news, actually : Web pictures (png, gif, jpeg), zip, pdf, gtar, tar, pdf
				*/
				$permittedtypes=array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png' );
				$uploader = new XoopsMediaUploader( XOOPS_UPLOAD_PATH, $permittedtypes, 1024*1024*3);
				$uploader->setTargetFileName($destname);
				if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
					if ($uploader->upload()) {
						$video->setVar('image', str_replace(XOOPS_ROOT_PATH, '', $destname));
					} else {
						echo _AM_UPLOAD_ERROR. ' ' . $uploader->getErrors();
					}
				} else {
					echo $uploader->getErrors();
				}
			}
		}
		if ($videoHandler->insert($video)) {
			$video_id = $GLOBALS['xoopsDB']->getInsertId();
			
			if (file_exists(XOOPS_ROOT_PATH.'/modules/tag/class/tag.php')) {
				$tag_handler = xoops_getmodulehandler('tag', 'tag');
				$tag_handler->updateByItem($_REQUEST["video_tags"], $video_id, $GLOBALS['xoopsModule']->getVar("dirname"), intval($_REQUEST["cid"]));
			}
								
			if (!strpos($_SERVER['REQUEST_URI'], '/vidshop/admin/'))
				redirect_header('admin.php?id='.$video_id, 8, sprintf(_VSP_RH_VIDSHOP_EDITED, $video->getVar('name')));
			else
				redirect_header('admin.php?op=list', 8, sprintf(_VSP_RH_VIDSHOP_EDITED, $video->getVar('name')));
			exit(0);	
		} else {
			redirect_header('admin.php', 4, sprintf(_VSP_RH_VIDSHOP_NOCREATION, $video->getVar('name'), implode('<br/>',$video->getErrors())));
			exit(0);		
		}
	}

	function saveEditVideoCategory($cid)
	{
		$videoCatHandler =& xoops_getmodulehandler('video_category', 'vidshop');
		$video = $videoCatHandler->get( $cid );
				
		$video->setVar('name', $_REQUEST["name"]);
		$video->setVar('description', $_REQUEST["description"]);

		if(isset($_POST['xoops_upload_file'])) {
			$fldname = $_FILES[$_POST['xoops_upload_file'][0]];
			$fldname = (get_magic_quotes_gpc()) ? stripslashes($fldname['name']) : $fldname['name'];
			if(xoops_trim($fldname!='')) {
				$destname = md5(time()).'_'.$fldname;
				/**
				* You can attach files to your news, actually : Web pictures (png, gif, jpeg), zip, pdf, gtar, tar, pdf
				*/
				$permittedtypes=array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png' );
				$uploader = new XoopsMediaUploader( XOOPS_UPLOAD_PATH, $permittedtypes, 1024*1024*3);
				$uploader->setTargetFileName($destname);
				if ($uploader->fetchMedia($_REQUEST['xoops_upload_file'][0])) {
					if ($uploader->upload()) {
						$video->setVar('image', str_replace(XOOPS_ROOT_PATH, '', $destname));
					} else {
						echo _AM_UPLOAD_ERROR. ' ' . $uploader->getErrors();
					}
				} else {
					echo $uploader->getErrors();
				}
			}
		}
	
		if ($cid = $videoCatHandler->insert($video)) {				
			if (!strpos($_SERVER['REQUEST_URI'], '/vidshop/admin/'))
				redirect_header('admin.php?op=cats&fct=edit&id='.$cid, 8, sprintf(_VSP_RH_CATEGORY_EDITED, $video->getVar('name')));
			else
				redirect_header('admin.php?op=cats', 8, sprintf(_VSP_RH_CATEGORY_EDITED, $video->getVar('name')));
			exit(0);	
		} else {
			redirect_header('admin.php', 4, sprintf(_VSP_RH_CATEGORY_NOCREATION, $video->getVar('name'), implode('<br/>',$video->getErrors())));
			exit(0);		
		}
	}

	function saveNewVideoCategory()
	{
		$videoCatHandler =& xoops_getmodulehandler('video_category', 'vidshop');
		$video = $videoCatHandler->create();
				
		$video->setVar('name', $_REQUEST["name"]);
		$video->setVar('description', $_REQUEST["description"]);

		if(isset($_POST['xoops_upload_file'])) {
			$fldname = $_FILES[$_POST['xoops_upload_file'][0]];
			$fldname = (get_magic_quotes_gpc()) ? stripslashes($fldname['name']) : $fldname['name'];
			if(xoops_trim($fldname!='')) {
				$destname = md5(time()).'_'.$fldname;
				
				/**
				* You can attach files to your news, actually : Web pictures (png, gif, jpeg), zip, pdf, gtar, tar, pdf
				*/
				$permittedtypes=array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png' );
				$uploader = new XoopsMediaUploader( XOOPS_UPLOAD_PATH, $permittedtypes, 1024*1024*3);
				$uploader->setTargetFileName($destname);
				if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
					if ($uploader->upload()) {
						$video->setVar('image', str_replace(XOOPS_ROOT_PATH, '', $destname));
					} else {
						echo _AM_UPLOAD_ERROR. ' ' . $uploader->getErrors();
					}
				} else {
					echo $uploader->getErrors();
				}
			}
		}
	
		if ($cid = $videoCatHandler->insert($video)) {				
			if (!strpos($_SERVER['REQUEST_URI'], '/vidshop/admin/'))
				redirect_header('admin.php?op=cats&fct=edit&id='.$cid, 8, sprintf(_VSP_RH_CATEGORY_EDITED, $video->getVar('name')));
			else
				redirect_header('admin.php?op=cats', 8, sprintf(_VSP_RH_CATEGORY_EDITED, $video->getVar('name')));
			exit(0);	
		} else {
			redirect_header('index.php', 4, sprintf(_VSP_RH_CATEGORY_NOCREATION, $video->getVar('name'), implode('<br/>',$video->getErrors())));
			exit(0);		
		}
	}
	
	function chronolabs_inline($flash = false)
	{
	
		$ret = '<div style="clear:both; height 10px;">&nbsp;</div>
<div style="clear:both; height 10px;"><center><img src="http://www.chronolabs.org.au/images/banners/loader/supportimage.php?flash=false" /></center></div>
<div style="clear:both;">Chronolabs offer limited free support should you want some development work done please contact us <a href="http://www.chronolabs.org.au/liaise/">on the question for a quote form.</a> We offer a wide range of XOOPS Professional Solution and have options for Basic SEO and marketing of your site as well as Search Engine Optimization for <a href="http://www.xoops.org/">XOOPS</a>. If you are looking for work done with this module/application or are looking for development on your site please contact us.</div>';
		return $ret;
	}
?>