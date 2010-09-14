<?php
defined('XOOPS_ROOT_PATH') or die('Restricted access');

class VidshopFormPaypalButton extends XoopsFormElement
{
    /**
     * Text
     *
     * @var string
     * @access private
     */
    var $_value;
	var $_key ='';
	var $_email = '';
	
    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $value Text
     */
    function VidshopFormPaypalButton($caption = '', $email = '', $key = '')
    {
		
        $this->setCaption($caption);
		if (!empty( $email ) && is_string(  $email ))
			$this->_email = $email;
			
		if (!empty( $key ) && is_string( $key )) 
			$this->_key = $key;
		else
			$this->_key = $_COOKIE['vidshop']['key'];
    }
    
    /**
     * Prepare HTML for output
     *
     * @return string
	 *
	 * Form Path:: https://www.paypal.com/cgi-bin/webscr" 
	 *		@ target:: paypal;
	 *		@ method:: post;
	 *			@ Button Name :: I1;
     */
    function render()
    {
			
		$module_handler =& xoops_gethandler('module');
		$xoModule = $module_handler->getByDirname('vidshop');
		$config_handler =& xoops_gethandler('config');
		$xoConfigs = $config_handler->getConfigList($xoModule->getVar('mid'));

		$videoHandler =& xoops_getmodulehandler('video', 'vidshop');
		$cartHandler =& xoops_getmodulehandler('video_cart', 'vidshop');
		$cartItemsHandler =& xoops_getmodulehandler('video_cart_items', 'vidshop');
		
		$criteria = new Criteria('`key`', $this->_key);
		
		$cart = $cartHandler->getObjects($criteria);
	
		if (isset($cart[0])) {
			$cart = $cart[0];
			$_COOKIE['vidshop']['key'] = $cart->getVar('key');
		} else {
			redirect_header('index.php', 4, _VSP_INVALID_SHOP_KEY);
			exit(0);
		}
		
		$criteria = new Criteria('hid', $cart->getVar('id'));
		$items = $cartItemsHandler->getObjects($criteria);

		foreach($items as $item) {
			$ii++;
			$video = $videoHandler->get($item->getVar('vid'));
			$amount = $amount + floatval($video->getVar('price'));
		}
				
		$ret .= '<input type="hidden" name="cmd" value="_xclick">';
		$ret .= '<input type="hidden" name="business" value="' . $xoConfigs['paypalEmail'] . '">';
		$ret .= '<input type="hidden" name="rm" value="2">';
		$ret .= '<input type="hidden" name="notify_url" value="' . /* XOOPS_URL */ 'http://wishcraft.thruhere.net/beeu2' . '/modules/vidshop/ipnppd.php">';
		$ret .= '<input type="hidden" name="no_shipping" value="0">';
		$ret .= '<input type="hidden" name="currency_code" value="'.$xoConfigs['paypalCurrency'].'">';
		$ret .= '<input type="hidden" name="custom" value="' . $this->_key . '">';
		$ret .= '<input type="hidden" name="cancel_return" value="' . /* XOOPS_URL */ 'http://wishcraft.thruhere.net/beeu2' . '/modules/vidshop/download.php?op=cancel">';
		$ret .= '<input type="hidden" name="return" value="' . /* XOOPS_URL */ 'http://wishcraft.thruhere.net/beeu2' . '/modules/vidshop/download.php">';
		$ret .= '<input type="hidden" name="image_url" value="' . /* XOOPS_URL */ 'http://wishcraft.thruhere.net/beeu2' . '/images/logo.png">';
		$ret .= '<input type="hidden" name="item_name" value="'.sprintf($xoConfigs['paypalitemSprintf'], $ii).'">';
		$ret .= '<input type="hidden" name="amount" value="'.$amount.'">';
		//$ret .= '<input type="hidden" name="currency_code_'.$ii.'" value="'.$video->getVar('currency').'">';
		$ret .= '<input type="hidden" name="quantity" value="1">';	

		$ret .= '<input type="submit" value="Make Payment >>" border="0">';
		return $ret;
	}
}

?>