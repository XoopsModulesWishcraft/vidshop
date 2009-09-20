<?php
	
	include_once( 'form.objects.php' );
	
	function formCheckout(){
		$cform = new XoopsThemeForm(_VSP_FRM_CHECKOUT_FORM, 'checkout', 'https://www.paypal.com/cgi-bin/webscr');
		$module_handler =& xoops_gethandler('module');
		$xoModule = $module_handler->getByDirname('vidshop');
		$config_handler =& xoops_gethandler('config');
		$xoConfigs = $config_handler->getConfigList($xoModule->getVar('mid'));			

		$cform->addElement(new VidshopFormPaypalButton(_VSP_FRM_CHECKOUT_PAYPAL, $xoConfigs['paypalEmail'], $_COOKIE['vidshop']['key']));
		
		return $cform->render();
	}
	
	function formCart($key)
	{
		$module_handler =& xoops_gethandler('module');
		$xoModule = $module_handler->getByDirname('vidshop');
		$config_handler =& xoops_gethandler('config');
		$xoConfigs = $config_handler->getConfigList($xoModule->getVar('mid'));

		$videoHandler =& xoops_getmodulehandler('video', 'vidshop');
		$cartHandler =& xoops_getmodulehandler('video_cart', 'vidshop');
		$cartItemsHandler =& xoops_getmodulehandler('video_cart_items', 'vidshop');
		
		$criteria = new Criteria('`key`', $key);
		$cart = $cartHandler->getObjects($criteria);
		if (isset($cart[0])) {
			$cart = $cart[0];
			$_COOKIE['vidshop']['key'] = $cart->getVar('key');
		} else {
			redirect_header('index.php', 4, _VSP_INVALID_SHOP_KEY);
			exit(0);
		}

		$items = $cartItemsHandler->getAll(new Criteria('hid', $cart->getVar('id')));

		$lform = new XoopsThemeForm(_VSP_FRM_CART_LIST, 'videocartlist');
		$lform->setExtra('enctype="multipart/form-data"');
	
		foreach($items as $key => $item) {
			$video = $videoHandler->get($item->getVar('vid'));

			$lele[$key] = new XoopsFormElementTray(sprintf(_VSP_ELE_VIDSHOP_LIST, $video->getVar('id')));
			$lele[$key]->setDescription( $video->getVar('name') );
			$lele[$key]->addElement(new XoopsFormLabel('', '<a href="'.XOOPS_URL.'/modules/vidshop/scoop.php?op=removefromcart&id='.$video->getVar('id').'&uri='.XOOPS_URL.'/modules/vidshop/cart.php">Remove</a>'));
			$lele[$key]->addElement(new XoopsFormLabel('', 'Price: '.$video->getVar('price'). ' ' .$video->getVar('currency')));
			$lform->addElement($lele[$key]);
			
		}
		if ($_GET['op']!='view') {
			$lform->addElement(new XoopsFormHidden('op', 'checkout'));
			$lform->addElement(new XoopsFormHidden('fct', 'checkout'));
			
			$lform->addElement(new XoopsFormButton('', 'checkout_submit', 'Check Out & Pay', "submit"));
		}
		return $lform->render();
	}

	function formDownload()
	{
		$module_handler =& xoops_gethandler('module');
		$xoModule = $module_handler->getByDirname('vidshop');
		$config_handler =& xoops_gethandler('config');
		$xoConfigs = $config_handler->getConfigList($xoModule->getVar('mid'));

		$videoHandler =& xoops_getmodulehandler('video', 'vidshop');
		$downloadsHandler =& xoops_getmodulehandler('video_downloads', 'vidshop');
		
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
			$criteria->add(new CriteriaCompo(Criteria('key', $_COOKIE['vidshop']['key']), 'OR'), 'OR');			
		
		$criteria = new Criteria('hid', $cart->getVar('id'));
		$items = $downloadsHandler->getObject($criteria);

		$lform = new XoopsThemeForm(_VSP_FRM_CART_LIST, 'videocartlist');
		$lform->setExtra('enctype="multipart/form-data"');
	
		foreach($items as $key => $item) {
			$video = $videoHandler->get($item->getVar('vid'));

			$lele[$key] = new XoopsFormElementTray(sprintf(_VSP_ELE_VIDSHOP_LIST, $video->getVar('id')));
			$lele[$key]->setDescription( $video->getVar('name') );
			$lele[$key]->addElement(new XoopsFormLabel('', '<a href="'.$xoConfigs['download_spot'].'/'.$item->getVar('download').'?passkey='.$item->getVar('key').'">Download</a>'));
			$lele[$key]->addElement(new XoopsFormLabel('', 'Price: '.$video->getVar('price'). ' ' .$video->getVar('currency')));
			$lform->addElement($lele[$key]);
			
		}
		return $lform->render();
	}
	
	
	function formVideoCats($cid)
	{
		$cform = new XoopsThemeForm(_VSP_FRM_VIDSHOP_CATS_FORM, 'vidshopcats');
		$cform->setExtra('enctype="multipart/form-data"');
		
		$videoCatsHandler =& xoops_getmodulehandler('video_category', 'vidshop');
		
		if (intval($cid)<>0)
			$vcats =& $videoCatsHandler->get(intval($cid));
		else
			$vcats =& $videoCatsHandler->create();
			
		$cform->addElement(new XoopsFormText(_VSP_FRM_VIDSHOP_WEIGHT, 'weight', 4, 13, !isset($_REQUEST['weight'])?$vcats->getVar('weight'):$_REQUEST['weight']));
		$cform->addElement(new XoopsFormText(_VSP_FRM_VIDSHOP_NAME, 'name', 70, 128, !isset($_REQUEST['name'])?$vcats->getVar('name'):$_REQUEST['name']));
		if (strlen($vcats->getVar('image')))
			$cform->addElement(new XoopsFormLabel(_VSP_FRM_VIDSHOP_CURRENT_IMAGE, "<img src='".XOOPS_URL."/uploads/".$vcats->getVar('image')."' width='160px'>"), false);
		$cform->addElement(new XoopsFormFile(_VSP_FRM_VIDSHOP_IMAGE, 'image', 1024*1024*3), false);
		$description_editor = !isset($_REQUEST['description_editor'])?'xinha':$_REQUEST['description_editor'];
		$cform->addElement(new XoopsFormSelectEditor($cform, 'description_editor', $description_editor));
		$description_config['name'] = 'description';
		$description_config['editor'] = $description_editor;
		$description_config['value'] = !isset($_REQUEST['description'])?$vcats->getVar('description'):$_REQUEST['description'];
		$description_config['width'] = 379;
		$description_config['height'] = 479;
		$ele_description = new XoopsFormEditor(_VSP_FRM_VIDSHOP_DESCRIPTION, 'description', $description_config);
		$ele_description->setDescription(_VSP_FRM_VIDSHOP_DESCRIPTION_DESC);
		$cform->addElement($ele_description);
		$cform->addElement(new XoopsFormHidden('description_editor_current', $description_editor));
		$cform->addElement(new XoopsFormHidden('cid', $cid));
		$cform->addElement(new XoopsFormHidden('op', $_REQUEST['op']));
		$cform->addElement(new XoopsFormHidden('fct', 'save'));
		
		$cform->addElement(new XoopsFormButton('', 'contents_submit', _SUBMIT, "submit"));
		return $cform->render();
	
	}

	function formCatsList($video_objects, $videoHandler)
	{
		$lform = new XoopsThemeForm(_VSP_FRM_CATEGORY_LIST, 'videocatlist');
		$lform->setExtra('enctype="multipart/form-data"');
		foreach($video_objects as $key => $video) {
			$lele[$key] = new XoopsFormElementTray(sprintf(_VSP_ELE_VIDSHOP_LIST, $video->getVar('cid')));
			$lele[$key]->setDescription( $video->getVar('name') );
			$lele[$key]->addElement(new XoopsFormLabel('', '<a href="'.XOOPS_URL.'/modules/vidshop/admin/admin.php?op=cats&fct=edit&id='.$video->getVar('cid').'">'._EDIT.'</a>'));
			$lele[$key]->addElement(new XoopsFormLabel('', '<a href="'.XOOPS_URL.'/modules/vidshop/admin/admin.php?op=cats&fct=delete&id='.$video->getVar('cid').'">'._DELETE.'</a>'));
			$lform->addElement($lele[$key]);
			
		}
		return $lform->render();
	}
		
	function formVideo($video_id) {
		$cform = new XoopsThemeForm(_VSP_FRM_VIDSHOP_FORM, 'vidshop');
		$cform->setExtra('enctype="multipart/form-data"');
		$videoHandler =& xoops_getmodulehandler('video', 'vidshop');
		
		if (intval($_REQUEST['id'])<>0)
			$video =& $videoHandler->get(intval($_REQUEST['id']));
		else
			$video =& $videoHandler->create();
		
		$description_editor = !isset($_REQUEST['description_editor'])?'xinha':$_REQUEST['description_editor'];
		
		$cat = new VidshopFormSelectCategory(_VSP_FRM_CATEGORY, 'cid', $video->getVar('cid'));
		$cat->setDescription(_VSP_FRM_CATEGORY_DESC);
		$cform->addElement($cat);

		$cform->addElement(new XoopsFormText(_VSP_FRM_VIDSHOP_NAME, 'name', 70, 128, !isset($_REQUEST['name'])?$video->getVar('name'):$_REQUEST['name']));
		if (strlen($video->getVar('image')))
			$cform->addElement(new XoopsFormLabel(_VSP_FRM_VIDSHOP_CURRENT_IMAGE, "<img src='".XOOPS_URL."/uploads/".$video->getVar('image')."' width='160px'>"), false);
		
		$cform->addElement(new XoopsFormFile(_VSP_FRM_VIDSHOP_IMAGE, 'image', 1024*1024*3), false);
		
		$cform->addElement(new XoopsFormSelectEditor($cform, 'description_editor', $description_editor));
		$description_config['name'] = 'description';
		$description_config['editor'] = $description_editor;
		$description_config['value'] = !isset($_REQUEST['description'])?$video->getVar('description'):$_REQUEST['description'];
		$description_config['width'] = 379;
		$description_config['height'] = 479;
		$ele_description = new XoopsFormEditor(_VSP_FRM_VIDSHOP_DESCRIPTION, 'description', $description_config);
		$ele_description->setDescription(_VSP_FRM_VIDSHOP_DESCRIPTION_DESC);
		$cform->addElement($ele_description);

		$value = (string)$video->getVar('download');
		$file = new VidshopFormSelectFiles(_VSP_FRM_DOWNLOAD, 'file', $value, 1, false);
		$file->setValue($value);
		$file->setDescription(_VSP_FRM_DOWNLOAD_DESC);
		$cform->addElement($file);
	
		$cform->addElement(new XoopsFormText(_VSP_FRM_VIDSHOP_PRICE, 'price', 13, 10, !isset($_REQUEST['price'])?$video->getVar('price'):$_REQUEST['price']));
				
		$currency = new VidshopFormSelectCurrencies(_VSP_FRM_CURRENCY, 'curr', isset($_REQUEST['curr'])?$video->getVar('currency'):$_REQUEST['curr']);
		$currency->setDescription(_VSP_FRM_CURRENCY_DESC);
		$currency->setValue(isset($_REQUEST['curr'])?$video->getVar('currency'):$_REQUEST['curr']);
		$cform->addElement($currency);

		$cform->addElement(new XoopsFormTextArea(_VSP_FRM_VIDSHOP_EMBEDDED, 'embedded', !isset($_REQUEST['embedded'])?$video->getVar('embedded'):$_REQUEST['embedded'], 5, 70));

		$cform->addElement(new XoopsFormTag('video_tags', 70, 255, $_REQUEST['id']));

		$cform->addElement(new XoopsFormHidden('description_editor_current', $description_editor));
		$cform->addElement(new XoopsFormHidden('id', $_REQUEST['id']));
		$cform->addElement(new XoopsFormHidden('op', $_REQUEST['op']));
		$cform->addElement(new XoopsFormHidden('fct', 'save'));
		
		$cform->addElement(new XoopsFormButton('', 'contents_submit', _SUBMIT, "submit"));
		return $cform->render();
	}	
	
	function formList($video_objects, $videoHandler)
	{
		$lform = new XoopsThemeForm(_VSP_FRM_VIDSHOP_LIST, 'videolist');
		$lform->setExtra('enctype="multipart/form-data"');
		foreach($video_objects as $key => $video) {
			$lele[$key] = new XoopsFormElementTray(sprintf(_VSP_ELE_VIDSHOP_LIST, $video->getVar('id')));
			$lele[$key]->setDescription( $video->getVar('name') );
			$lele[$key]->addElement(new XoopsFormLabel('', '<a href="'.XOOPS_URL.'/modules/vidshop/admin/admin.php?op=edit&id='.$video->getVar('id').'">'._EDIT.'</a>'));
			$lele[$key]->addElement(new XoopsFormLabel('', '<a href="'.XOOPS_URL.'/modules/vidshop/admin/admin.php?op=delete&id='.$video->getVar('id').'">'._DELETE.'</a>'));
			$lele[$key]->addElement(new XoopsFormTag('video_tags', 35, 255, $video->getVar('id')));
			$lform->addElement($lele[$key]);
			
		}
		return $lform->render();
	}
	
	function formVote($session)
	{
		$cform = new XoopsThemeForm(_VSP_FRM_VOTE_FORM, 'vote');
		$cform->setExtra('enctype="multipart/form-data"');
		$votesel = new XoopsFormSelect(_VSP_FRM_VOTE_STARS, 'stars',0 , 10);
		$votesel->addOption('10', '10 Stars');
		$votesel->addOption('9', '9 Stars');
		$votesel->addOption('8', '8 Stars');
		$votesel->addOption('7', '7 Stars');
		$votesel->addOption('6', '6 Stars');
		$votesel->addOption('5', '5 Stars');
		$votesel->addOption('4', '4 Stars');
		$votesel->addOption('3', '3 Stars');
		$votesel->addOption('2', '2 Stars');
		$votesel->addOption('1', '1 Stars');
		$cform->addElement($votesel);
		$cform->addElement(new XoopsFormHidden('op', 'vote'));
		$cform->addElement(new XoopsFormHidden('id', $session['id']));
		$cform->addElement(new XoopsFormHidden('ip', $session['ip']));
		$cform->addElement(new XoopsFormHidden('addy', $session['addy']));
		$cform->addElement(new XoopsFormButton('', 'contents_submit', _SUBMIT, "submit"));
		return $cform->render();
	}

?>
