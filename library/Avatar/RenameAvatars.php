<?php

/**
 * Avatar for Contao Open Source CMS
 *
 * Copyright (C) 2013 Kirsten Roschanski
 *
 * @package    Avatar
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

namespace KirstenRoschanski\Avatar;

/**
 * Class InsertTags
 *
 * @copyright  Kirsten Roschanski (C) 2013, bit3 UG (C) 2013
 * @author     Kirsten Roschanski <kirsten@kat-webdesign.de>
 * @author     Tristan Lins <tristan.lins@bit3.de>
 */
class RenameAvatars extends \Backend implements \executable
{
	public function isActive()
	{
		return (\Input::post('FORM_SUBMIT') == 'tl_rename_avatars');
	}

	public function run()
	{
		$arrJobs               = array();
		$objTemplate           = new \BackendTemplate('be_rename_avatars');
		$objTemplate->isActive = $this->isActive();

		// Confirmation message
		if ($_SESSION['RENAME_AVATARS_CONFIRM'] != '') {
			$objTemplate->message = sprintf(
				'<p class="tl_confirm">%s</p>' . "\n",
				$_SESSION['RENAME_AVATARS_CONFIRM']
			);

			$_SESSION['RENAME_AVATARS_CONFIRM'] = '';
		}

		// Add potential error messages
		if (!empty($_SESSION['TL_ERROR']) && is_array($_SESSION['TL_ERROR'])) {
			foreach ($_SESSION['TL_ERROR'] as $message) {
				$objTemplate->message .= sprintf('<p class="tl_error">%s</p>' . "\n", $message);
			}

			$_SESSION['TL_ERROR'] = array();
		}

		// Run the jobs
		if (\Input::post('FORM_SUBMIT') == 'tl_rename_avatars') {
			/** @var \Files $files */
			$files = \Files::getInstance();

			/** @var string $uploadDir */
			$uploadDir = \FilesModel::findByPk($GLOBALS['TL_CONFIG']['avatar_dir']);
			if ($uploadDir) {
				$uploadDir = $uploadDir->path;
			}
			else {
				$_SESSION['TL_ERROR'][] = 'Upload dir is invalid!';
				$this->reload();
			}

			/** @var \MemberModel $member */
			$member = \MemberModel::findBy(array('avatar!=?'), '');

			while ($member->next()) {
				$avatarRecord = \FilesModel::findByPk($member->avatar);
				if ($avatarRecord) {
					$avatar = $avatarRecord->path;
				}
				else {
					$_SESSION['TL_ERROR'][] = sprintf('Avatar for user ID %d is invalid', $member->id);
					continue;
				}

				$pathinfo = pathinfo($avatar);

				$newName = standardize(
					\String::parseSimpleTokens($GLOBALS['TL_CONFIG']['avatar_name'], $member->row())
				);

				if ($pathinfo['filename'] != $newName) {
					$newPath = $uploadDir . '/' . $newName . '.' . $pathinfo['extension'];
					$files->rename($avatar, $newPath);
					$avatarRecord->path = $newPath;
					$avatarRecord->name = $newName;
					$avatarRecord->save();
				}
			}

			$_SESSION['RENAME_AVATARS_CONFIRM'] = $GLOBALS['TL_LANG']['tl_maintenance']['avatarsRenamed'];
			$this->reload();
		}

		$objTemplate->action   = ampersand(\Environment::get('request'));
		$objTemplate->headline = $GLOBALS['TL_LANG']['tl_maintenance']['renameAvatars'];
		$objTemplate->submit   = specialchars($GLOBALS['TL_LANG']['tl_maintenance']['doRenameAvatars']);
		$objTemplate->help     = $GLOBALS['TL_LANG']['tl_maintenance']['renameAvatarsHelp'];

		return $objTemplate->parse();
	}
}