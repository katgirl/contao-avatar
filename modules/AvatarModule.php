<?php
/**
 * Avatar for Contao Open Source CMS
 *
 * Copyright (C) 2013 Kirsten Roschanski
 *
 * @package    Avatar
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */
 
namespace Avatar\Module; 

/**
 * Class AvatarModule
 *
 * Front end module Avatar.
 * @copyright  Kirsten Roschanski (C) 2013
 * @author     Kirsten Roschanski <kirsten@kat-webdesign.de>
 */
class AvatarModule extends \Module
{
  
  /**
   * Template
   * @var string
   */
	protected $strTemplate = 'mod_avatar';
  
  /**
   * File name
   * @var string
   */
	protected $strName = 'avatar_file'; 
  
  /**
   * Form fields
   * @var array
   */
	protected $form;
  
	
  /**
   * Return a wildcard in the back end
   * @return string
   */
	public function generate()
	{
		if (TL_MODE == 'BE') {
			$objTemplate = new \BackendTemplate('be_wildcard');
      
			$objTemplate->wildcard = '### PERSONAL AVATAR ###';
      
		    $objTemplate->title    = $this->headline;
		    $objTemplate->id       = $this->id;
		    $objTemplate->link     = $this->name;
		    $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;
	  
			return $objTemplate->parse();
		}

		if (FE_USER_LOGGED_IN !== true)
		{
			return '';
		}
    
		return parent::generate();
	}

  /**
   * Generate module
   * @return void
   */
	protected function compile()
	{
		global $objPage;

		$this->maxlength  = $GLOBALS['TL_CONFIG']['avatar_maxsize'];
		$this->extensions = $GLOBALS['TL_CONFIG']['avatar_filetype'];
		$this->uploadFolder  = $GLOBALS['TL_CONFIG']['avatar_dir'];
		$this->storeFile  = $this->uploadFolder != '' ? true : false;

		$arrImage  = deserialize($GLOBALS['TL_CONFIG']['avatar_maxdims']);
		
		$this->import('FrontendUser', 'User');

		$strAvatar = $this->User->avatar;
		$strAlt    = $this->User->firstname . " " . $this->User->lastname;
		
		$objFile = \FilesModel::findByPk($strAvatar);
		
		if ( $objFile !== null )     
		  $this->Template->avatar = '<img src="' . TL_FILES_URL . \Image::get($objFile->path, $arrImage[0], $arrImage[1], $arrImage[2]) . '" width="' . $arrImage[0] . '" height="' . $arrImage[1] . '" alt="' . $strAlt . '" class="avatar">';
		elseif ( $this->User->gender != '' )
		  $this->Template->avatar = '<img src="' . TL_FILES_URL . \Image::get("system/modules/avatar/assets/" . $this->User->gender . ".png", $arrImage[0], $arrImage[1], $arrImage[2]) . '" width="' . $arrImage[0] . '" height="' . $arrImage[1] . '" alt="Avatar" class="avatar">';       
		else
		  $this->Template->avatar = '<img src="' . TL_FILES_URL . \Image::get("system/modules/avatar/assets/male.png", $arrImage[0], $arrImage[1], $arrImage[2]) . '" width="' . $arrImage[0] . '" height="' . $arrImage[1] . '" alt="Avatar" class="avatar">';       
	 
		$this->Template->action = ampersand(\Environment::get('request')); 
		$this->Template->formId = 'avatar_'. $this->id;
		$this->Template->method = 'post';  
		$this->Template->enctype = 'multipart/form-data';  
		$this->Template->attributes = '';  
		$this->Template->avatar_reset_label = $GLOBALS['TL_LANG']['AVATAR']['reset'];
		$this->Template->avatar_submit_value = $GLOBALS['TL_LANG']['AVATAR']['save'];
		$this->Template->avatar_file_label = sprintf($GLOBALS['TL_LANG']['AVATAR']['file']['1'],
													  $this->extensions,
													  $this->maxlength,
													  $arrImage[0],
													  $arrImage[1]);  

		// Confirm or remove a subscription
		if ( \Input::get('token') )
		{
			static::changeSubscriptionStatus($objTemplate);
			return;
		}
  
		// Remove the avatar
		if ( \Input::postRaw('avatar_reset') ) 
		{
				\Database::getInstance()->prepare("UPDATE `tl_member` SET avatar='' WHERE `id`=?")->execute($this->User->id);
				$this->reload();
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
				$this->Template->error = sprintf($GLOBALS['TL_LANG']['ERR']['filesize'], $maxlength_kb);
				$this->log('File "'.$file['name'].'" exceeds the maximum file size of '.$maxlength_kb, 'FormFileUpload validate()', TL_ERROR);
			}

			if ($file['error'] == 3)
			{
				$this->Template->error = sprintf($GLOBALS['TL_LANG']['ERR']['filepartial'], $file['name']);
				$this->log('File "'.$file['name'].'" was only partially uploaded', 'FormFileUpload validate()', TL_ERROR);
			}

			unset($_FILES[$this->strName]);
			return;
		}

		// File is too big
		if ($this->maxlength > 0 && $file['size'] > $this->maxlength)
		{
			$this->Template->error = sprintf($GLOBALS['TL_LANG']['ERR']['filesize'], $maxlength_kb);
			$this->log('File "'.$file['name'].'" exceeds the maximum file size of '.$maxlength_kb, 'FormFileUpload validate()', TL_ERROR);

			unset($_FILES[$this->strName]);
			return;
		}

		$strExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
		$uploadTypes = trimsplit(',', $this->extensions);

		// File type is not allowed
		if (!in_array(strtolower($strExtension), $uploadTypes))
		{
			$this->Template->error = sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $strExtension);
			$this->log('File type "'.$strExtension.'" is not allowed to be uploaded ('.$file['name'].')', 'FormFileUpload validate()', TL_ERROR);

			unset($_FILES[$this->strName]);
			return;
		}

		if (($arrImageSize = @getimagesize($file['tmp_name'])) != false)
		{
			// Image exceeds maximum image width
			if ($arrImageSize[0] > $arrImage[0])
			{
				$this->Template->error = sprintf($GLOBALS['TL_LANG']['ERR']['filewidth'], $file['name'], $arrImage[0]);
				$this->log('File "'.$file['name'].'" exceeds the maximum image width of '.$arrImage[0].' pixels', 'FormFileUpload validate()', TL_ERROR);

				unset($_FILES[$this->strName]);
				return;
			}

			// Image exceeds maximum image height
			if ($arrImageSize[1] > $arrImage[1])
			{
				$this->Template->error = sprintf($GLOBALS['TL_LANG']['ERR']['fileheight'], $file['name'], $arrImage[1]);
				$this->log('File "'.$file['name'].'" exceeds the maximum image height of '.$arrImage[1].' pixels', 'FormFileUpload validate()', TL_ERROR);

				unset($_FILES[$this->strName]);
				return;
			}
		}
		
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

			// Generate the DB entries
			if ($GLOBALS['TL_DCA']['tl_files']['config']['databaseAssisted'])
			{
			  $strFile = $strUploadFolder . '/' . $file['name'];
			  $objFile = \FilesModel::findByPath($strFile);

			  // Existing file is being replaced (see #4818)
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
      $strFile = $strUploadFolder . '/' . $file['name'];
			$objFile = \FilesModel::findByPath($strFile);
      
      // new Avatar for Member
			\Database::getInstance()->prepare("UPDATE tl_member SET avatar=? WHERE id=?")->execute($objFile->id,$this->User->id);
			
			$this->log('File "'.$file['name'].'" has been moved to "'.$strUploadFolder.'"', 'FormFileUpload validate()', TL_FILES);
		  }
		}

		unset($_FILES[$this->strName]);
		$this->reload();
	} 
	
}
