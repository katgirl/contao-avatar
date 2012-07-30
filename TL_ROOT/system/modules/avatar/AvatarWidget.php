<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
/**
 * TYPOlight Avatars :: Backend avatar widget
 *
 * NOTE: this file was edited with tabs set to 4.
 * @package Avatars
 * @copyright Copyright (C) 2008 by Peter Koch, IBK Software AG
 * @license See accompaning file LICENSE.txt
 */

class AvatarWidget extends Widget implements uploadable
{
	protected $avatarDirectory;
	protected $blnSubmitInput = true;
	protected $strTemplate = 'avatar_widget';
	protected $filename = 'avatar_%s';
	protected $maxdims = 80;
	protected $maxsize = 10000;

	public function __construct($arrAttributes=false)
	{
		parent::__construct($arrAttributes);
		$this->decodeEntities = true;
		$this->loadLanguageFile('avatar');

		
		if (version_compare(VERSION, 3, '<')) {
			$this->avatarDirectory = $GLOBALS['TL_CONFIG']['avatar_dir'];
		}
		else {
			$objFile = \FilesModel::findByPk($GLOBALS['TL_CONFIG']['avatar_dir']);
			$this->avatarDirectory = $objFile->path;
		}
		
		// overwrite defaults
		$dca = &$GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval'];
		$this->filename = $dca['filename'] ? $dca['filename'] : $this->strTable.'_%s';
		$this->maxdims = $dca['maxdims'] ? $dca['maxdims'] : $GLOBALS['TL_CONFIG']['avatar_maxdims'];
		$this->maxsize = $dca['maxsize'] ? $dca['maxsize'] : $GLOBALS['TL_CONFIG']['avatar_maxsize'];
	} // __construct

	public function __set($strKey, $varValue)
	{
		switch ($strKey) {
			case 'maxlength':
				$this->arrAttributes[$strKey] = ($varValue > 0) ? $varValue : '';
				break;
			default:
				parent::__set($strKey, $varValue);
				break;
		} // switch
	} // __set

	public static function filename($avatar)
	{
		$avatar = trim($avatar);
		if ($avatar != '') return $avatar;
		return trim($this->avatarDirectory.'/default'.$GLOBALS['TL_CONFIG']['avatar_maxdims'].'.png');
	} // filename

	protected function validator($aParam)
	{
		// check reset
		if ($this->Input->post($this->strName . '_reset')) {
			return '';
		} // if

		// check upload
		$key = $this->strName.'_file';
		if (!$_FILES || !array_key_exists($key, $_FILES) || !strlen($_FILES[$key]['name'])) {
			return $this->varValue;
		} // if

		$fname = $_FILES[$key]['name'];
		$tmp = $_FILES[$key]['tmp_name'];

		// check for errors
		$text = &$GLOBALS['TL_LANG']['avatar']; 
		$err = $_FILES[$key]['error'];
		if ($err != UPLOAD_ERR_OK) {
			switch ($err) {
				case UPLOAD_ERR_INI_SIZE:
					$this->addError(sprintf($text['errinisize'],$fname));
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$this->addError(sprintf($text['errformsize'],$fname));
					break;
				case UPLOAD_ERR_PARTIAL:
					$this->addError(sprintf($text['errpartial'],$fname));
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$this->addError($text['notmpdir']);
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$this->addError(sprintf($text['cantwrite'],$fname));
					break;
				default:
					$this->addError(sprintf($text['errorno'],$fname, $err));
					break;
			} // switch
			return '';
		} // if
				
		// check file extension
		$parts = pathinfo($fname);
		if (!in_array($parts['extension'], explode(',',$GLOBALS['TL_CONFIG']['validImageTypes']))) {
			$this->addError(sprintf($text['invalidfilext'],$fname));
			return '';
		} // if
		
		// check file size
		$size = $_FILES[$this->strName.'_file']['size'];
		if ($size > $this->maxsize) {
			$this->addError(sprintf($text['filetoobig'],$fname));
			return '';
		} // if
		
		// check for valid image
		$imgsize = @getimagesize($tmp);
		if ($imgsize === false) {
			$this->addError(sprintf($text['novalidimage'],$fname));
			return '';
		} // if
		
		// check dimensions
		if ($imgsize[0] > $this->maxdims ||$imgsize[1] > $this->maxdims) {
			$this->addError(sprintf($text['imgtoobig'],$fname));
			return '';
		} // if

				
		// save file
		if (!$this->hasErrors()) {
			$avfile = $this->avatarDirectory.'/'.sprintf($this->filename, $this->currentRecord).'.'.$parts['extension'];
			$this->import('Files');

			$this->Files->move_uploaded_file($tmp, $avfile);
			$this->Files->chmod($avfile, 0644);
			$this->blnSubmitInput = true;
		}

		return $avfile;
	} // validator

	public function generate()
	{
		$img = Avatar::img($this->varValue);
		if ($img == '') return $img;
		$text = &$GLOBALS['TL_LANG']['avatar']; 
		return 
			"$img<br/>\n".
			sprintf(
				'  <div id="ctrl_%s" class="tl_checkbox_container">',
				$this->strName
			).
			sprintf(
				'  <input type="checkbox" name="%s_reset" id="ctrl_%s_reset" class="tl_checkbox avatar%s" value="1" onfocus="Backend.getScrollOffset();"/>'."\n",
				$this->strName,
				$this->strId,
				(strlen($this->strClass) ? ' ' . $this->strClass : '')
			).
			sprintf('  <label for="ctrl_%s_reset" class="avatarreset%s">%s</label></div>',
				$this->strId,
				(strlen($this->strClass) ? ' ' . $this->strClass : ''),
				$text['reset']
			);
	} // generate

	public function generateUploadLabel()
	{
		$text = &$GLOBALS['TL_LANG']['avatar']; 
		return sprintf('<label for="ctrl_%s_file" class="avatarfile%s">%s</label>',
						$this->strId,
						(strlen($this->strClass) ? ' ' . $this->strClass : ''),
						$text['file'][0]);
	} // generateUploadlabel

	public function generateUpload()
	{
		$text = &$GLOBALS['TL_LANG']['avatar'];
		$conf = &$GLOBALS['TL_CONFIG'];
		$info = '';
		if ($conf['showHelp'])
			$info = 
				sprintf(
					$text['file'][1], 
					$conf['validImageTypes'],
					$this->maxsize,
					$this->maxdims,
					$this->maxdims
				);
		return 
			sprintf(
				'<input type="file" name="%s_file" id="ctrl_%s_file" size="40" class="tl_text avatarfile%s" %s onfocus="Backend.getScrollOffset();" />%s',
				$this->strName,
				$this->strId,
				(strlen($this->strClass) ? ' ' . $this->strClass : ''),
				$this->getAttributes(),
				($info!='' ? "\n  " . '<p class="tl_help">'.$info.'</p>' : '')
			);
	} // generateUpload
	
} // Avatar

?>