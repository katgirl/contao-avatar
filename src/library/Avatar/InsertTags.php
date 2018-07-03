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

namespace KirstenRoschanski\Avatar;

/**
 * Class InsertTags
 *
 * @copyright  Kirsten Roschanski (C) 2013
 * @copyright  Tristan Lins (C) 2013
 * @author     Kirsten Roschanski <kirsten@kat-webdesign.de>
 * @author     Tristan Lins <tristan.lins@bit3.de>
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

		// get default settings
		$arrDims  = deserialize($GLOBALS['TL_CONFIG']['avatar_maxdims']);
		$strAlt   = $GLOBALS['TL_CONFIG']['avatar_default_alt'];
		$strTitle = $GLOBALS['TL_CONFIG']['avatar_default_title'];
		$strClass = $GLOBALS['TL_CONFIG']['avatar_default_class'];

		// parse query parameters
		$strParams = \StringUtil::decodeEntities($strParams);
		$strParams = str_replace('[&]', '&', $strParams);
		$arrParams = explode('&', $strParams);
		foreach ($arrParams as $strParam) {
			list($key, $value) = explode('=', $strParam);

			switch ($key) {
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

		// if no id given, use the current logged in member
		if (!$arrTag[1]) {
			// if no member is logged in, return anonymous avatar
			if (!FE_USER_LOGGED_IN) {
				return $this->generateAnonymousAvatar($arrDims);
			}
			$arrTag[1] = \FrontendUser::getInstance()->id;
		}

		// search the member record
		if($arrTag[2] == 'be') {
            		$objMember = \UserModel::findById($arrTag[1]);
        	} else {
            		$objMember = \MemberModel::findByPk($arrTag[1]);
        	}

		// return anonymous avatar, if member not found
		if (!$objMember) {
			return $this->generateAnonymousAvatar($arrDims);
		}

		// get the avatar
		$strAvatar = $objMember->avatar;

		// parse the alt and title text
		$strAlt   = \String::parseSimpleTokens($strAlt, $objMember->row());
		$strTitle = \String::parseSimpleTokens($strTitle, $objMember->row());

		// avatar available and file exists
		if ($strAvatar &&
			($objFile = \FilesModel::findByUuid($strAvatar)) &&
			file_exists(TL_ROOT . '/' . $objFile->path)
		) {
			$strAvatar = $objFile->path;
		}

		else if ($GLOBALS['TL_CONFIG']['avatar_fallback_image'] &&
			($objFile = \FilesModel::findByUuid($GLOBALS['TL_CONFIG']['avatar_fallback_image'])) &&
			file_exists(TL_ROOT . '/' . $objFile->path)
		) {
			$strAvatar = $objFile->path;
		}

		// no avatar is set, but gender is available
		else if ($strAvatar == '' && $objMember->gender != '') {
			$strAvatar = "system/modules/avatar/assets/" . $objMember->gender . ".png";
		}

		// fallback to default avatar
		else {
			$strAvatar = 'system/modules/avatar/assets/male.png';
		}

		// resize if size is requested
		$this->resize($strAvatar, $arrDims);

		// generate the img tag
		return sprintf(
			'<img src="%s" width="%s" height="%s" alt="%s" title="%s" class="%s">',
			TL_FILES_URL . $strAvatar,
			$arrDims[0],
			$arrDims[1],
			$strAlt,
			$strTitle,
			$strClass
		);
	}

	protected function resize(&$strAvatar, &$arrDims)
	{
		if ($arrDims[0] || $arrDims[1]) {
			$strAvatar = \Image::get(
				$strAvatar,
				$arrDims[0],
				$arrDims[1],
				$arrDims[2]
			);

			// read the new size to keep proportion
			$objAvatar  = new \File($strAvatar);
			$arrDims[0] = $objAvatar->width;
			$arrDims[1] = $objAvatar->height;
		}
	}

	protected function generateAnonymousAvatar($arrDims)
	{
		if ($GLOBALS['TL_CONFIG']['avatar_fallback_image'] &&
			($objFile = \FilesModel::findByUuid($GLOBALS['TL_CONFIG']['avatar_fallback_image']))
		) {
			$strAvatar = $objFile->path;
		}
		else {
			$strAvatar = 'system/modules/avatar/assets/male.png';
		}

		$this->resize($strAvatar, $arrDims);

		return sprintf(
			'<img src="%s" width="%s" height="%s" alt="%s" title="%s" class="%s">',
			$strAvatar,
			$arrDims[0],
			$arrDims[1],
			$GLOBALS['TL_CONFIG']['avatar_anonymous_alt'],
			$GLOBALS['TL_CONFIG']['avatar_anonymous_title'],
			$GLOBALS['TL_CONFIG']['avatar_anonymous_class']
		);
	}
}
