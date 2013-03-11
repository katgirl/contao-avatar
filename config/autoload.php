<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @package Avatar
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Avatar',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Modules
	'Avatar\Module\AvatarModule' 		=> 'system/modules/avatar/modules/AvatarModule.php',

	// Library
	'Avatar\InsertTags'          		=> 'system/modules/avatar/library/Avatar/InsertTags.php',
	'Avatar\Widget\AvatarWidget' 		=> 'system/modules/avatar/library/Avatar/AvatarWidget.php',
	'Avatar\Widget\AvatarFileUpload' 	=> 'system/modules/avatar/library/Avatar/AvatarFileUpload.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'form_avatar' => 'system/modules/avatar/templates',
	'mod_avatar'  => 'system/modules/avatar/templates',
));
