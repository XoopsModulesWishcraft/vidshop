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
class VidshopVideo extends XoopsObject
{

    function VidshopVideo($id = null)
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('cid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 128);
		$this->initVar('image', XOBJ_DTYPE_OTHER, null, false, 255);
		$this->initVar('description', XOBJ_DTYPE_OTHER, null, false);
		$this->initVar('download', XOBJ_DTYPE_OTHER, null, true, 255);
		$this->initVar('price', XOBJ_DTYPE_OTHER, 0, true);
		$this->initVar('embedded', XOBJ_DTYPE_OTHER, null, false);
		$this->initVar('hit', XOBJ_DTYPE_INT, null, false);
		$this->initVar('uid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('comments', XOBJ_DTYPE_INT, null, false);
		$this->initVar('video_tags', XOBJ_DTYPE_TXTBOX, null, true, 255);
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
class VidshopVideoHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db) 
    {
		$this->db = $db;
        parent::__construct($db, "vidshop_video", 'VidshopVideo', "id", "name");
    }
}

?>