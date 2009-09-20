<?php
// $Autho: wishcraft $

if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}
/**
 * Class for compunds
 * @author Simon Roberts <simon@xoops.org>
 * @copyright copyright (c) 2009-2003 XOOPS.org
 * @package kernel
 */
class VidshopVideo_cart extends XoopsObject
{

    function VidshopVideo_cart($id = null)
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('uid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('created', XOBJ_DTYPE_INT, null, false);
		$this->initVar('ip', XOBJ_DTYPE_TXTBOX, null, false, 16);
		$this->initVar('addy', XOBJ_DTYPE_TXTBOX, null, false, 255);
		$this->initVar('key', XOBJ_DTYPE_TXTBOX, null, false, 32);
		$this->initVar('items', XOBJ_DTYPE_INT, null, false);
    }
}


/**
* XOOPS policies handler class.
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS user class objects.
*
* @author  Simon Roberts <simon@chronolabs.org.au>
* @package kernel
*/
class VidshopVideo_cartHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db) 
    {
		$this->db = $db;
        parent::__construct($db, "vidshop_video_cart", 'VidshopVideo_cart', 'id', 'key');
    }
	
	function GenerateKey() {
		if (is_object($GLOBALS['xoopsUser']))
			return md5($GLOBALS['xoopsUser']->getVar('uname').microtime());	
		else
			return md5(microtime());
	}
}

?>