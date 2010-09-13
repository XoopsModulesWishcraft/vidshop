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
class VidshopVideo_downloads extends XoopsObject
{

    function VidshopVideo_downloads($id = null)
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('vid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('uid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('download', XOBJ_DTYPE_TXTBOX, null, false, 255);
		$this->initVar('ip', XOBJ_DTYPE_TXTBOX, null, false, 16);
		$this->initVar('addy', XOBJ_DTYPE_TXTBOX, null, false, 255);
		$this->initVar('downloads', XOBJ_DTYPE_INT, null, false);
		$this->initVar('key', XOBJ_DTYPE_TXTBOX, null, false, 32);
		$this->initVar('created', XOBJ_DTYPE_INT, null, false);		
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
class VidshopVideo_downloadsHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db) 
    {
		$this->db = $db;
        parent::__construct($db, "vidshop_video_downloads", 'VidshopVideo_downloads', 'id', 'download');
    }
}

?>