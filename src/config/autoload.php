<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
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
	'KirstenRoschanski',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Library
	'KirstenRoschanski\Avatar\InsertTags'              => 'system/modules/avatar/library/Avatar/InsertTags.php',
	'KirstenRoschanski\Avatar\Widget\AvatarFileUpload' => 'system/modules/avatar/library/Avatar/AvatarFileUpload.php',
	'KirstenRoschanski\Avatar\RenameAvatars'           => 'system/modules/avatar/library/Avatar/RenameAvatars.php',
	'KirstenRoschanski\Avatar\Widget\AvatarWidget'     => 'system/modules/avatar/library/Avatar/AvatarWidget.php',

	// Modules
	'KirstenRoschanski\Avatar\Module\AvatarModule'     => 'system/modules/avatar/modules/AvatarModule.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_rename_avatars' => 'system/modules/avatar/templates',
	'mod_avatar'        => 'system/modules/avatar/templates',
	'form_avatar'       => 'system/modules/avatar/templates',
));
