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
class VidshopVideo_category extends XoopsObject
{

    function VidshopVideo_category($id = null)
    {
        $this->initVar('cid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('weight', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 128);
		$this->initVar('image', XOBJ_DTYPE_OTHER, null, false, 255);
        $this->initVar('description', XOBJ_DTYPE_OTHER, null, false);		
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
class VidshopVideo_categoryHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db) 
    {
		$this->db = $db;
        parent::__construct($db, "vidshop_video_category", 'VidshopVideo_category', "cid", "name");
    }
}

?>