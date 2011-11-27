<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
/**
 * TYPOlight Avatars :: Data container array for table tl_member
 *
 * NOTE: this file was edited with tabs set to 4.
 * @package Avatars
 * @copyright Copyright (C) 2008 by Peter Koch, IBK Software AG
 * @license See accompaning file LICENSE.txt
 */

/**
 * Add palette
 */
$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = 
	str_replace(
		';{account_legend},disable,start,stop', 
		';{avatar_legend:hide},avatar;{account_legend},disable,start,stop', 
		$GLOBALS['TL_DCA']['tl_member']['palettes']['default']
	);

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_member']['fields']['avatar'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_member']['avatar'],
	'exclude'			=> true,
	'inputType'			=> 'avatar',
	'eval'				=> array('filename'=>'member_%s', 'feEditable'=>true, 'feGroup'=>'helpdesk')
);

?>