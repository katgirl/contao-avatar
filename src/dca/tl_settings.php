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
 * Add to palette
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'avatar_rename';
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{avatar_legend},avatar_fallback_image,'.
	'avatar_dir,avatar_default,' .
	'avatar_maxsize,avatar_filetype,avatar_maxdims,avatar_resize,' .
	'avatar_rename,' .
	'avatar_default_alt,avatar_anonymous_alt,' .
	'avatar_default_title,avatar_anonymous_title,' .
	'avatar_default_class,avatar_anonymous_class';

$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['avatar_rename'] = 'avatar_name';

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_fallback_image'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_fallback_image'],
	'exclude'   => true,
	'inputType' => 'fileTree',
	'eval'      => array('fieldType' => 'radio', 'files' => true, 'filesOnly' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_dir'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_dir'],
	'exclude'   => true,
	'inputType' => 'fileTree',
	'eval'      => array('fieldType' => 'radio', 'mandatory' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_maxsize'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_maxsize'],
	'exclude'   => true,
	'inputType' => 'text',
	'eval'      => array('rgxp' => 'digit', 'mandatory' => true, 'tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_filetype'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_filetype'],
	'exclude'   => true,
	'inputType' => 'text',
	'eval'      => array('mandatory' => true, 'tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_maxdims'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_maxdims'],
	'exclude'   => true,
	'inputType' => 'imageSize',
	'options'   => $GLOBALS['TL_CROP'],
	'reference' => &$GLOBALS['TL_LANG']['MSC'],
	'eval'      => array('rgxp' => 'digit', 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_resize'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_resize'],
	'exclude'   => true,
	'inputType' => 'checkbox',
	'eval'      => array('tl_class' => 'w50 m12')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_rename'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_rename'],
	'exclude'   => true,
	'inputType' => 'checkbox',
	'eval'      => array('tl_class' => 'clr w50 m12', 'submitOnChange' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_name'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_name'],
	'exclude'   => true,
	'inputType' => 'text',
	'eval'      => array('tl_class' => 'w50', 'mandatory' => true, 'allowHtml' => true, 'preserveTags' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_default_alt'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_default_alt'],
	'exclude'   => true,
	'inputType' => 'text',
	'eval'      => array('tl_class' => 'clr w50', 'allowHtml' => true, 'preserveTags' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_anonymous_alt'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_anonymous_alt'],
	'exclude'   => true,
	'inputType' => 'text',
	'eval'      => array('tl_class' => 'w50', 'allowHtml' => true, 'preserveTags' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_default_title'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_default_title'],
	'exclude'   => true,
	'inputType' => 'text',
	'eval'      => array('tl_class' => 'w50', 'allowHtml' => true, 'preserveTags' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_anonymous_title'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_anonymous_title'],
	'exclude'   => true,
	'inputType' => 'text',
	'eval'      => array('tl_class' => 'w50', 'allowHtml' => true, 'preserveTags' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_default_class'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_default_class'],
	'exclude'   => true,
	'inputType' => 'text',
	'eval'      => array('tl_class' => 'w50', 'allowHtml' => true, 'preserveTags' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_anonymous_class'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_anonymous_class'],
	'exclude'   => true,
	'inputType' => 'text',
	'eval'      => array('tl_class' => 'w50', 'allowHtml' => true, 'preserveTags' => true)
);
