<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
/**
 * TYPOlight Avatars :: Data container array for table tl_user
 *
 * NOTE: this file was edited with tabs set to 4.
 * @package Avatars
 * @copyright Copyright (C) 2008 by Peter Koch, IBK Software AG
 * @license See accompaning file LICENSE.txt
 */

class avatar_user extends Backend
{
	/**
	 *	Add avatar to a palette
	 */
	public function addToPalette($pal, $before=null)
	{
		if ($before)
			$GLOBALS['TL_DCA']['tl_user']['palettes'][$pal] = 
				str_replace(
					$before, 
					';{avatar_legend:hide},avatar'.$before, 
					$GLOBALS['TL_DCA']['tl_user']['palettes'][$pal]
				);
		else
			$GLOBALS['TL_DCA']['tl_user']['palettes'][$pal] .= ';{avatar_legend:hide},avatar';
	} // addToPalette

} // helpdesk_user

/**
 * Add palettes
 */
$this->import('avatar_user');
$this->avatar_user->addToPalette('login');
foreach (array('admin','default','group','extend','custom') as $pal) 
	$this->avatar_user->addToPalette($pal, ';{account_legend},disable,start,stop');

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['avatar'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_user']['avatar'],
	'exclude'			=> true,
	'inputType'			=> 'avatar',
	'eval'				=> array('filename'=>'user_%s')
);

?>