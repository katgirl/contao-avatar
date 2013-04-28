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
 * Presettings
 */
$GLOBALS['TL_CONFIG']['avatar_fallback_image']  = '';
$GLOBALS['TL_CONFIG']['avatar_dir']             = '';
$GLOBALS['TL_CONFIG']['avatar_name']            = 'avatar_##id##_##firstname##_##lastname##';
$GLOBALS['TL_CONFIG']['avatar_default_alt']     = '##firstname## ##lastname##';
$GLOBALS['TL_CONFIG']['avatar_anonymous_alt']   = 'Avatar';
$GLOBALS['TL_CONFIG']['avatar_default_title']   = '##firstname## ##lastname##';
$GLOBALS['TL_CONFIG']['avatar_anonymous_title'] = '';
$GLOBALS['TL_CONFIG']['avatar_default_class']   = 'avatar';
$GLOBALS['TL_CONFIG']['avatar_anonymous_class'] = 'avatar avatar-anonymous';


/**
 * Back end form fields
 */
$GLOBALS['BE_FFL']['avatar'] = 'Avatar\Widget\AvatarFileUpload';


/**
 * FRONT end form fields
 */
$GLOBALS['TL_FFL']['avatar'] = 'Avatar\Widget\AvatarWidget';


/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['user']['avatar'] = 'Avatar\Module\AvatarModule';


/**
 * Register hooks
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('Avatar\InsertTags', 'replaceTags');


/**
 * Maintenance
 */
if ($GLOBALS['TL_CONFIG']['avatar_rename']) {
	$GLOBALS['TL_MAINTENANCE']['avatar'] = 'Avatar\RenameAvatars';
}
