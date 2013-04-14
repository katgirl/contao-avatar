<?php

/**
 * Avatar for Contao Open Source CMS
 *
 * Copyright (C) 2013 Kirsten Roschanski
 *
 * @package    Avatar
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */
 
namespace Avatar\Widget; 

/**
 * Class AvatarWidget
 *
 * Widget for members avatar.
 * @copyright  Kirsten Roschanski (C) 2013
 * @author     Kirsten Roschanski <kirsten@kat-webdesign.de>
 */
class AvatarWidget extends \Widget implements \uploadable
{
  
   /**
    * Template
    * @var string
    */
	protected $strTemplate = 'form_avatar';
	
	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;
	
	/**
	 * Add specific attributes
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue)
	{
		switch ($strKey)
		{
			case 'maxlength':
				$this->arrConfiguration['maxlength'] = $varValue;
				break;

			case 'mandatory':
				if ($varValue)
				{
					$this->arrAttributes['required'] = 'required';
				}
				else
				{
					unset($this->arrAttributes['required']);
				}
				parent::__set($strKey, $varValue);
				break;

			case 'fSize':
				if ($varValue > 0)
				{
					$this->arrAttributes['size'] = $varValue;
				}
				break;

			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}  


	/**
	 * Validate the input and set the value
	 */
	public function validate()
	{
		$this->maxlength  = $GLOBALS['TL_CONFIG']['avatar_maxsize'];
		$this->extensions = $GLOBALS['TL_CONFIG']['avatar_filetype'];
		$this->uploadFolder  = $GLOBALS['TL_CONFIG']['avatar_dir'];
		$this->storeFile  = $this->uploadFolder != '' ? true : false;

		$arrImage  = deserialize($GLOBALS['TL_CONFIG']['avatar_maxdims']);
		
		$this->import('FrontendUser', 'User');		
		
		// No file specified
		if (!isset($_FILES[$this->strName]) || empty($_FILES[$this->strName]['name']))
		{
			if ($this->mandatory)
			{
				if ($this->strLabel == '')
				{
					$this->addError($GLOBALS['TL_LANG']['ERR']['mdtryNoLabel']);
				}
				else
				{
					$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['mandatory'], $this->strLabel));
				}
			}

			return;
		}

		$file = $_FILES[$this->strName];
		$maxlength_kb = $this->getReadableSize($this->maxlength);

		// Romanize the filename
		$file['name'] = utf8_romanize($file['name']);

		// File was not uploaded
		if (!is_uploaded_file($file['tmp_name']))
		{
			if (in_array($file['error'], array(1, 2)))
			{
				$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['filesize'], $maxlength_kb));
				$this->log('File "'.$file['name'].'" exceeds the maximum file size of '.$maxlength_kb, 'FormFileUpload validate()', TL_ERROR);
			}

			if ($file['error'] == 3)
			{
				$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['filepartial'], $file['name']));
				$this->log('File "'.$file['name'].'" was only partially uploaded', 'FormFileUpload validate()', TL_ERROR);
			}

			unset($_FILES[$this->strName]);
			return;
		}

		// File is too big
		if ($this->maxlength > 0 && $file['size'] > $this->maxlength)
		{
			$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['filesize'], $maxlength_kb));
			$this->log('File "'.$file['name'].'" exceeds the maximum file size of '.$maxlength_kb, 'FormFileUpload validate()', TL_ERROR);

			unset($_FILES[$this->strName]);
			return;
		}

		$strExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
		$uploadTypes = trimsplit(',', $this->extensions);

		// File type is not allowed
		if (!in_array(strtolower($strExtension), $uploadTypes))
		{
			$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $strExtension));
			$this->log('File type "'.$strExtension.'" is not allowed to be uploaded ('.$file['name'].')', 'FormFileUpload validate()', TL_ERROR);

			unset($_FILES[$this->strName]);
			return;
		}

		if (($arrImageSize = @getimagesize($file['tmp_name'])) != false)
		{
			// Image exceeds maximum image width
			if ($arrImageSize[0] > $arrImage[0])
			{
				$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['filewidth'], $file['name'], $arrImage[0]));
				$this->log('File "'.$file['name'].'" exceeds the maximum image width of '.$GLOBALS['TL_CONFIG']['imageWidth'].' pixels', 'FormFileUpload validate()', TL_ERROR);

				unset($_FILES[$this->strName]);
				return;
			}

			// Image exceeds maximum image height
			if ($arrImageSize[1] > $arrImage[1])
			{
				$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['fileheight'], $file['name'], $arrImage[1]));
				$this->log('File "'.$file['name'].'" exceeds the maximum image height of '.$GLOBALS['TL_CONFIG']['imageHeight'].' pixels', 'FormFileUpload validate()', TL_ERROR);

				unset($_FILES[$this->strName]);
				return;
			}
		}

		// Store file in the session and optionally on the server
		if (!$this->hasErrors())
		{
			$_SESSION['FILES'][$this->strName] = $_FILES[$this->strName];
			$this->log('File "'.$file['name'].'" uploaded successfully', 'FormFileUpload validate()', TL_FILES);

			if ($this->storeFile)
			{
				$intUploadFolder = $this->uploadFolder;

				if ($this->User->assignDir && $this->User->homeDir)
				{
					$intUploadFolder = $this->User->homeDir;
				}

				$objUploadFolder = \FilesModel::findByPk($intUploadFolder);

				// The upload folder could not be found
				if ($objUploadFolder === null)
				{
					throw new \Exception("Invalid upload folder ID $intUploadFolder");
				}

				$strUploadFolder = $objUploadFolder->path;

				// Store the file if the upload folder exists
				if ($strUploadFolder != '' && is_dir(TL_ROOT . '/' . $strUploadFolder))
				{
					$this->import('Files');

					// Do not overwrite existing files
					if ($this->doNotOverwrite && file_exists(TL_ROOT . '/' . $strUploadFolder . '/' . $file['name']))
					{
						$offset = 1;
						$pathinfo = pathinfo($file['name']);
						$name = $pathinfo['filename'];

						$arrAll = scan(TL_ROOT . '/' . $strUploadFolder);
						$arrFiles = preg_grep('/^' . preg_quote($name, '/') . '.*\.' . preg_quote($pathinfo['extension'], '/') . '/', $arrAll);

						foreach ($arrFiles as $strFile)
						{
							if (preg_match('/__[0-9]+\.' . preg_quote($pathinfo['extension'], '/') . '$/', $strFile))
							{
								$strFile = str_replace('.' . $pathinfo['extension'], '', $strFile);
								$intValue = intval(substr($strFile, (strrpos($strFile, '_') + 1)));

								$offset = max($offset, $intValue);
							}
						}

						$file['name'] = str_replace($name, $name . '__' . ++$offset, $file['name']);
					}

					$this->Files->move_uploaded_file($file['tmp_name'], $strUploadFolder . '/' . $file['name']);
					$this->Files->chmod($strUploadFolder . '/' . $file['name'], $GLOBALS['TL_CONFIG']['defaultFileChmod']);

					$_SESSION['FILES'][$this->strName] = array
					(
						'name' => $file['name'],
						'type' => $file['type'],
						'tmp_name' => TL_ROOT . '/' . $strUploadFolder . '/' . $file['name'],
						'error' => $file['error'],
						'size' => $file['size'],
						'uploaded' => true
					);

					$this->loadDataContainer('tl_files');

					if ($GLOBALS['TL_DCA']['tl_files']['config']['databaseAssisted'])
					{
						$strFile = $strUploadFolder . '/' . $file['name'];
						$objFile = \FilesModel::findByPath($strFile);

						if ($objFile !== null)
						{
							$objFile->tstamp = time();
							$objFile->path   = $strFile;
							$objFile->hash   = md5_file(TL_ROOT . '/' . $strFile);
							$objFile->save();
						}
						else
						{
							$objFile = new \File($strFile, true);

							$objNew = new \FilesModel();
							$objNew->pid       = $objUploadFolder->id;
							$objNew->tstamp    = time();
							$objNew->type      = 'file';
							$objNew->path      = $strFile;
							$objNew->extension = $objFile->extension;
							$objNew->hash      = md5_file(TL_ROOT . '/' . $strFile);
							$objNew->name      = $objFile->basename;
							$objNew->save();
						}

						// Update the hash of the target folder
						$objFolder = new \Folder($strUploadFolder);
						$objUploadFolder->hash = $objFolder->hash;
						$objUploadFolder->save();
					}
					
					// Update Userdata
					$this->value = \Database::getInstance()->prepare("SELECT id FROM tl_files WHERE hash=?")->execute( md5_file(TL_ROOT . '/' . $strFile) )->id;

					$this->log('File "'.$file['name'].'" has been moved to "'.$strUploadFolder.'"', 'FormFileUpload validate()', TL_FILES);
				}
			}
		}
			
		unset($_FILES[$this->strName]);
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		global $objPage;

		$this->maxlength  = $GLOBALS['TL_CONFIG']['avatar_maxsize'];
		$this->extensions = $GLOBALS['TL_CONFIG']['avatar_filetype'];
		$arrImage         = deserialize($GLOBALS['TL_CONFIG']['avatar_maxdims']);
		
		$this->import('FrontendUser', 'User');

		$strAvatar = $this->User->avatar;
		$strAlt    = $this->User->firstname . " " . $this->User->lastname;
		
		$objFile = \FilesModel::findByPk($strAvatar);
		$template = '';
		
		if ( $objFile !== null )     
		  $template .= '<img src="' . TL_FILES_URL . \Image::get($objFile->path, $arrImage[0], $arrImage[1], $arrImage[2]) . '" width="' . $arrImage[0] . '" height="' . $arrImage[1] . '" alt="' . $strAlt . '" class="avatar">';
		elseif ( $this->User->gender != '' )
		  $template .= '<img src="' . TL_FILES_URL . \Image::get("system/modules/avatar/assets/" . $this->User->gender . ".png", $arrImage[0], $arrImage[1], $arrImage[2]) . '" width="' . $arrImage[0] . '" height="' . $arrImage[1] . '" alt="Avatar" class="avatar">';       
		else
		  $template .= '<img src="' . TL_FILES_URL . \Image::get("system/modules/avatar/assets/male.png", $arrImage[0], $arrImage[1], $arrImage[2]) . '" width="' . $arrImage[0] . '" height="' . $arrImage[1] . '" alt="Avatar" class="avatar">';       
	 
		$template .= sprintf('<input type="file" name="%s" id="ctrl_%s" class="upload%s"%s%s',
								$this->strName,
								$this->strId,
								(strlen($this->strClass) ? ' ' . $this->strClass : ''),
								$this->getAttributes(),
								$this->strTagEnding);
						
		$template .= sprintf($GLOBALS['TL_LANG']['AVATAR']['file']['1'],
							  $this->extensions,
							  $this->maxlength,
							  $arrImage[0],
							  $arrImage[1]); 
		
		return $template;
	}
}  
