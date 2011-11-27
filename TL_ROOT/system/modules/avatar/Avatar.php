<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
/**
 * TYPOlight Avatars :: Backend avatar utilities
 *
 * NOTE: this file was edited with tabs set to 4.
 * @package Avatars
 * @copyright Copyright (C) 2008 by Peter Koch, IBK Software AG
 * @license See accompaning file LICENSE.txt
 */

class Avatar
{
	public static function filename($avatar)
	{
		$avatar = trim($avatar);
		if ($avatar != '') return $avatar;
		$dir = trim($GLOBALS['TL_CONFIG']['avatar_dir']);
		if ($dir == '') $dir = $GLOBALS['TL_CONFIG']['uploadPath'].'/avatars';
		$dims = (int)$GLOBALS['TL_CONFIG']['avatar_maxdims'];
		if (!in_array($dims, array(32, 48, 64, 80, 100, 128))) $dims = 80;
		return trim($dir.'/default'.$dims.'.png');
	} // filename
	
	public static function img($avatar, $alt = 'Avatar', $classes = 'avatar', $attribs = '')
	{
		$avatar = self::filename($avatar);
		if ($avatar == '') return '';
		$imgsize = @getimagesize(TL_ROOT.'/'.$avatar);
		if ($imgsize === false) return '';
		return sprintf('<img src="%s" %s alt="%s" class="%s" %s/>', $avatar, $imgsize[3], $alt, $classes, $attribs);
	} // img	
} // Avatar

?>