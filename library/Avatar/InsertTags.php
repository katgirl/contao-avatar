<?php

/**
 * Avatar for Contao Open Source CMS
 *
 * Copyright (C) 2013 Kirsten Roschanski
 *
 * @package    Avatar
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

namespace Avatar;

/**
 * Class InsertTags
 *
 * @copyright  Kirsten Roschanski (C) 2013
 * @author     Kirsten Roschanski <kirsten@kat-webdesign.de>
 */
class InsertTags extends \System
{
	/**
	 * replace Inserttag
	 *
	 * @param string
	 *
	 * @return string
	 */
	public function replaceTags($strTag)
	{
		list($strTag, $strParams) = trimsplit('?', $strTag);
		$arrTag = trimsplit('::', $strTag);

		if ($arrTag[0] != 'avatar') {
			return false;
		}

		$strParams = \String::decodeEntities($strParams);
		$strParams = str_replace('[&]', '&', $strParams);
		$arrParams = explode('&', $strParams);

		//get_Settings
		$arrDims  = deserialize($GLOBALS['TL_CONFIG']['avatar_maxdims']);
		$strAlt   = $GLOBALS['TL_CONFIG']['avatar_default_alt'];
		$strTitle = $GLOBALS['TL_CONFIG']['avatar_default_title'];
		$strClass = $GLOBALS['TL_CONFIG']['avatar_default_class'];

		foreach ($arrParams as $strParam)
		{
			list($key, $value) = explode('=', $strParam);

			switch ($key)
			{
				case 'width':
					$arrDims[0] = $value;
					break;

				case 'height':
					$arrDims[1] = $value;
					break;

				case 'alt':
					$strAlt = specialchars($value);
					break;

				case 'title':
					$strTitle = specialchars($value);
					break;

				case 'class':
					$strClass = $value;
					break;

				case 'mode':
					$arrDims[2] = $value;
					break;
			}
		}
		if (!$arrTag[1]) {
			if (!FE_USER_LOGGED_IN) {
				return $this->generateAnonymousAvatar($arrDims);
			}

			$objMember = \MemberModel::findByPk(\FrontendUser::getInstance()->id);

			if (!$objMember) {
				return $this->generateAnonymousAvatar($arrDims);
			}

			$strAvatar = \FrontendUser::getInstance()->avatar;
			$strAlt    = \String::parseSimpleTokens($strAlt, $objMember->row());
			$strTitle  = \String::parseSimpleTokens($strTitle, $objMember->row());

			if ($strAvatar == '' && \FrontendUser::getInstance()->gender != '') {
				return '<img src="' . TL_FILES_URL . \Image::get(
					"system/modules/avatar/assets/" . \FrontendUser::getInstance()->gender . ".png",
					$arrDims[0],
					$arrDims[1],
					$arrDims[2]
				) . '" width="' . $arrDims[0] . '" height="' . $arrDims[1] . '" alt="' . $strAlt . '" title="' . $strTitle . '" class="' . $strClass . '">';
			}
		}
		elseif (is_numeric($arrTag[1])) {
			$objUser = \Database::getInstance()
				->prepare("SELECT * FROM tl_member WHERE id=?")
				->execute($arrTag[1]);

			if (!$objUser->next()) {
				return $this->generateAnonymousAvatar($arrDims);
			}

			$strAvatar = $objUser->avatar;
			$strAlt    = \String::parseSimpleTokens($strAlt, $objUser->row());
			$strTitle  = \String::parseSimpleTokens($strTitle, $objUser->row());
		}

		$objFile = \FilesModel::findByPk($strAvatar);

		if ($objFile === null) {
			$strAvatar = 'system/modules/avatar/assets/male.png';
		}
		else {
			$strAvatar = $objFile->path;
		}

		return '<img src="' . TL_FILES_URL . \Image::get(
			$strAvatar,
			$arrDims[0],
			$arrDims[1],
			$arrDims[2]
		) . '" width="' . $arrDims[0] . '" height="' . $arrDims[1] . '" alt="' . $strAlt . '" title="' . $strTitle . '" class="' . $strClass . '">';
	}

	protected function generateAnonymousAvatar($arrDims)
	{
		return '<img src="' . TL_FILES_URL . \Image::get(
			"system/modules/avatar/assets/male.png",
			$arrDims[0],
			$arrDims[1],
			$arrDims[2]
		) . '" width="' . $arrDims[0] . '" height="' . $arrDims[1] . '" alt="' . $GLOBALS['TL_CONFIG']['avatar_anonymous_alt'] . '" title="' . $GLOBALS['TL_CONFIG']['avatar_anonymous_title'] . '" class="' . $GLOBALS['TL_CONFIG']['avatar_anonymous_class'] . '">';
	}
}

?>