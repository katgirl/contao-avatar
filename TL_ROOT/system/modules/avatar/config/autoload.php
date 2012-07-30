<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Avatar
 * @link    http://www.contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array(
	'Avatar'				=> 'system/modules/avatar/Avatar.php',
	'AvatarForm'			=> 'system/modules/avatar/AvatarForm.php',
	'AvatarModule'			=> 'system/modules/avatar/AvatarModule.php',
	'AvatarTags'			=> 'system/modules/avatar/AvatarTags.php',
	'AvatarWidget'			=> 'system/modules/avatar/AvatarWidget.php',
));

TemplateLoader::addFiles(array(
		'avatar_form'		=> 'system/modules/avatar/templates/',
		'avatar_module'		=> 'system/modules/avatar/templates/',
		'avatar_widget'		=> 'system/modules/avatar/templates/',
));
