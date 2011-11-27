<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
/**
 * TYPOlight Avatars :: Data container array for tl_settings
 *
 * NOTE: this file was edited with tabs set to 4.
 * @package Avatars
 * @copyright Copyright (C) 2008 by Peter Koch, IBK Software AG
 * @license See accompaning file LICENSE.txt
 */

/**
 * Add to palette
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{avatar_legend},avatar_dir,avatar_default,avatar_maxsize,avatar_maxdims';

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_dir'] = array(
	'label'		=>	&$GLOBALS['TL_LANG']['tl_settings']['avatar_dir'],
	'exclude'	=>	true,
	'inputType'	=> 'fileTree',
	'eval'		=> array('fieldType'=>'radio', 'mandatory'=>true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_maxsize'] = array(
	'label'		=>	&$GLOBALS['TL_LANG']['tl_settings']['avatar_maxsize'],
	'exclude'	=>	true,
	'inputType'	=>	'text',
	'eval'		=>	array('rgxp'=>'digit', 'maxlength'=>6, 'mandatory'=>true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_maxdims'] = array(
	'label'		=>	&$GLOBALS['TL_LANG']['tl_settings']['avatar_maxdims'],
	'exclude'	=>	true,
	'inputType'	=> 'select',
	'options'	=> array(32, 48, 64, 80, 100, 128),
	'eval'		=> array('mandatory'=>true)
);

?>