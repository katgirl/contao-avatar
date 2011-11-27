<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
/**
 * TYPOlight Avatars :: Avatar insert tags
 *
 * NOTE: this file was edited with tabs set to 4.
 * @package Avatars
 * @copyright Copyright (C) 2008 by Peter Koch, IBK Software AG
 * @license See accompaning file LICENSE.txt
 */

class AvatarTags extends System
{

	public function replaceTags($content, $template)
	{
		if (TL_MODE == 'BE') return $content;
		$tags = array();
		preg_match_all('#{{avatar(::(\d+)(::(be|fe))?)?}}#i', $content, $tags, PREG_SET_ORDER);
		$done = array();
		foreach ($tags as $tag) {
			$t = $tag[0];
			if (in_array($t, $done)) continue;
			$n = count($tag);
			$replace = '';
			if ($n==1) {
				if (FE_USER_LOGGED_IN) {
					$this->import('FrontendUser', 'User');
					$replace = Avatar::img($this->User->avatar);
				} // if
			} else {
				$this->import('Database');
				$table = $n==5 && $tag[4]=='be' ? 'user' : 'member';
				$q = $this->Database
					->prepare("select `avatar` from `tl_$table` where `id`=?")
					->execute($tag[2]);
				if ($q->next()) $replace = Avatar::img($q->avatar);
			} // if
			$content = str_replace($t, $replace, $content);
			$done[] = $t;
		} // foreach
		return $content;
	} // replacetags
	
} // AvatarTags

?>