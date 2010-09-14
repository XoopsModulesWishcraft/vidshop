<?php

function vidshop_com_update($item_id, $total_num)
{
	$videoHandler =& xoops_getmodulehandler('video', 'vidshop');
	$video = $videoHandler->get($item_id);
	$video->setVar('comments', $total_num);
	@$videoHandler->insert($video);
} 

function vidshop_com_approve(&$comment)
{ 
    // notification mail here
} 


?>