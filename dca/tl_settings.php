<?php
/**
 * Avatar for Contao Open Source CMS
 *
 * Copyright (C) 2013 Kirsten Roschanski
 *
 * @package    Avatar
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Add to palette
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{avatar_legend},avatar_dir,avatar_default,avatar_maxsize,avatar_filetype,avatar_maxdims,avatar_resize';

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_dir'] = array
(
	'label'		  =>	&$GLOBALS['TL_LANG']['tl_settings']['avatar_dir'],
	'exclude'	  =>	true,
	'inputType'	=> 'fileTree',
	'eval'		  => array('fieldType'=>'radio', 'mandatory'=>true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_maxsize'] = array
(
	'label'		  =>	&$GLOBALS['TL_LANG']['tl_settings']['avatar_maxsize'],
	'exclude'	  =>	true,
	'inputType'	=>	'text',
	'eval'		  =>	array('rgxp'=>'digit', 'mandatory'=>true, 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_filetype'] = array
(
  'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_filetype'],
	'exclude'	  =>	true,
	'inputType'	=>	'text',
	'eval'		  =>	array('mandatory'=>true, 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_maxdims'] = array
(
  'label'     => &$GLOBALS['TL_LANG']['tl_settings']['avatar_maxdims'],
  'exclude'   => true,
  'inputType' => 'imageSize',
  'options'   => $GLOBALS['TL_CROP'],
  'reference' => &$GLOBALS['TL_LANG']['MSC'],
  'eval'      => array('rgxp'=>'digit', 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_resize'] = array
(
	'label'		=>	&$GLOBALS['TL_LANG']['tl_settings']['avatar_resize'],
	'exclude'	=>	true,
	'inputType'	=>	'checkbox',
	'eval'		=>	array('tl_class'=>'w50 m12')
);
