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
class VidshopVideo_cart_items extends XoopsObject
{

    function VidshopVideo_cart_items($id = null)
    {
        $this->initVar('hid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('vid', XOBJ_DTYPE_INT, null, false);
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
class VidshopVideo_cart_itemsHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db) 
    {
		$this->db = $db;
        parent::__construct($db, "vidshop_video_cart_items", 'VidshopVideo_cart_items');
    }
}

?>