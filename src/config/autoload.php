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
	'KirstenRoschanski\Avatar\AvatarBackend'           => 'system/modules/avatar/library/Avatar/AvatarBackend.php',
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
