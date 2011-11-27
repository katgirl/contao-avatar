<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
/**
 * TYPOlight Avatars :: Language file for avatar (en)
 *
 * NOTE: this file was edited with tabs set to 4.
 * @package Avatars
 * @copyright Copyright (C) 2008 by Peter Koch, IBK Software AG
 * @license See accompaning file LICENSE.txt
 */

$text = &$GLOBALS['TL_LANG']['avatar'];

$text['file'] 			= array("Upload new avatar", "Allowed file types: %s.<br/>\nMaximum file size: %s bytes.<br/>\nMaximum dimensions: %s x %s pixels.");

$text['save']			= 'Save avatar';
$text['reset']			= 'Reset to default';

$text['invalidfilext']	= '<em>%s</em> has an invalid extension.';
$text['errinisize']		= '<em>%s</em> exceeds upload_max_filesize in php.ini.';
$text['errformsize']	= '<em>%s</em> exceeds MAX_FILE_SIZE in the form.';
$text['errpartial']		= '<em>%s</em> was only partially uploaded.';
$text['notmpdir']		= 'Missing a temporary folder.';
$text['cantwrite']		= '<em>%s</em> failed to write file to disk.';
$text['errorno']		= '<em>%s</em> upload error %s.';
$text['filetoobig']		= '<em>%s</em> exceeds allowed maximum size.';
$text['imgtoobig']		= '<em>%s</em> exceeds allowed dimensions.';

?>