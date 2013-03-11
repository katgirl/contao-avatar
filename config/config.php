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

