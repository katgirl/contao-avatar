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
		$arrDims = deserialize($GLOBALS['TL_CONFIG']['avatar_maxdims']);

		$strAlt = false;
		$strClass = 'avatar';

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
				return '<img src="' . TL_FILES_URL . \Image::get(
					"system/modules/avatar/assets/male.png",
					$arrDims[0],
					$arrDims[1],
					$arrDims[2]
				) . '" width="' . $arrDims[0] . '" height="' . $arrDims[1] . '" alt="Avatar" class="' . $strClass . '">';
			}

			$strAvatar = \FrontendUser::getInstance()->avatar;
			$strAlt    = $strAlt ? $strAlt : \FrontendUser::getInstance()->firstname . " " . \FrontendUser::getInstance()->lastname;

			if ($strAvatar == '' && \FrontendUser::getInstance()->gender != '') {
				return '<img src="' . TL_FILES_URL . \Image::get(
					"system/modules/avatar/assets/" . \FrontendUser::getInstance()->gender . ".png",
					$arrDims[0],
					$arrDims[1],
					$arrDims[2]
				) . '" width="' . $arrDims[0] . '" height="' . $arrDims[1] . '" alt="' . ($strAlt ? $strAlt : 'Avatar') . '" class="' . $strClass . '">';
			}
		}
		elseif (is_numeric($arrTag[1])) {
			$objUser = \Database::getInstance()
				->prepare("SELECT * FROM tl_member WHERE id=?")
				->execute($arrTag[1]);
			$strAvatar = $objUser->avatar;
			$strAlt = $strAlt ? $strAlt :$objUser->firstname . " " . $objUser->lastname;
		}

		$objFile = \FilesModel::findByPk($strAvatar);

		if ($objFile !== null) {
			return '<img src="' . TL_FILES_URL . \Image::get(
				$objFile->path,
				$arrDims[0],
				$arrDims[1],
				$arrDims[2]
			) . '" width="' . $arrDims[0] . '" height="' . $arrDims[1] . '" title="' . $strAlt . '" alt="' . $strAlt . '" class="' . $strClass . '">';
		}
		else {
			return '<img src="' . TL_FILES_URL . \Image::get(
				"system/modules/avatar/assets/male.png",
				$arrDims[0],
				$arrDims[1],
				$arrDims[2]
			) . '" width="' . $arrDims[0] . '" height="' . $arrDims[1] . '" alt="Avatar" class="avatar">';
		}
	}
}

?>