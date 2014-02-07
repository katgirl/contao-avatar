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
 * Add palette
 */
$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] =
	str_replace(
		';{account_legend}',
		';{avatar_legend:hide},avatar;{account_legend}',
		$GLOBALS['TL_DCA']['tl_member']['palettes']['default']
	);

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_member']['fields']['avatar'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_member']['avatar'],
	'exclude'   => true,
	'inputType' => 'avatar',
	'eval'      => array
  (
    'fieldType'      => 'radio',
    'filesOnly'      => true,
    'tl_class'       => 'clr',
    'filename'       => 'member_%s',
    'feViewable'     => true,
    'feEditable'     => true,
    'feGroup'        => 'personal',
    'doNotOverwrite' => !$GLOBALS['TL_CONFIG']['avatar_rename']
	),
	'sql'       => "binary(16) NULL"
);
