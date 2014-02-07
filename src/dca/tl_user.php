<?php
/**
 * Avatar for Contao Open Source CMS
 *
 * Copyright (C) 2013 Kirsten Roschanski
 * Copyright (C) 2013 Tristan Lins <http://bit3.de>
 *
 * @package    Avatar
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Add palettes
 */
$GLOBALS['TL_DCA']['tl_user']['palettes']['login'] .= ';{avatar_legend:hide},avatar';

foreach (array('admin', 'default', 'group', 'extend', 'custom') as $pal) {
	$GLOBALS['TL_DCA']['tl_user']['palettes'][$pal] = str_replace
	(
		';{account_legend}',
		';{avatar_legend:hide},avatar;{account_legend}',
		$GLOBALS['TL_DCA']['tl_user']['palettes'][$pal]
	);
}

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['avatar'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_user']['avatar'],
	'exclude'   => true,
	'inputType' => 'fileTree',
	'eval'      => array
  (
    'fieldType'     => 'radio', 
    'filesOnly'     => true, 
    'tl_class'      => 'clr', 
    'filename'      => 'member_%s'
  ),
	'sql'       => "binary(16) NULL"
);
