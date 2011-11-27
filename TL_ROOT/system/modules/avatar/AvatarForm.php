<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
/**
 * TYPOlight Avatars :: Avatar form widget
 *
 * NOTE: this file was edited with tabs set to 4.
 * @package Avatars
 * @copyright Copyright (C) 2008 by Peter Koch, IBK Software AG
 * @license See accompaning file LICENSE.txt
 */

class AvatarForm extends Widget implements uploadable
{
	protected $blnSubmitInput = true;
	protected $strTemplate = 'avatar_form';
	protected $filename = 'avatar_%s';
	protected $maxdims = 80;
	protected $maxsize = 10000;

	public function __construct($arrAttributes=false)
	{
		parent::__construct($arrAttributes);
		$this->decodeEntities = true;
		$this->loadLanguageFile('avatar');
		$this->import('FrontendUser', 'User');

		// overwrite defaults
		$dca = &$GLOBALS['TL_DCA']['tl_member']['fields'][$this->strName]['eval'];
		$this->filename = $dca['filename'] ? $dca['filename'] : 'tl_member_%s';
		$this->maxdims = $dca['maxdims'] ? $dca['maxdims'] : $GLOBALS['TL_CONFIG']['avatar_maxdims'];
		$this->maxsize = $dca['maxsize'] ? $dca['maxsize'] : $GLOBALS['TL_CONFIG']['avatar_maxsize'];
	} // __construct

	public function __set($strKey, $varValue)
	{
		switch ($strKey) {
			case 'maxlength':
				$this->arrAttributes[$strKey] = ($varValue > 0) ? $varValue : '';
				break;
			case 'mandatory':
				$this->arrConfiguration['mandatory'] = $varValue ? true : false;
				break;
			default:
				parent::__set($strKey, $varValue);
				break;
		} // switch
	} // __set

	public function validate()
	{
		// check reset
		if ($this->Input->post($this->strName.'_reset')) {
			$this->varValue = '';
			return;
		} // if

		// check upload
		$key = $this->strName.'_file';
		if (!$_FILES || !array_key_exists($key, $_FILES) || !strlen($_FILES[$key]['name'])) {
			return;
		} // if

		$avfile = &$_FILES[$key];
		$fname = $avfile['name'];
		$tmp = $avfile['tmp_name'];
		$conf = &$GLOBALS['TL_CONFIG'];
		$text = &$GLOBALS['TL_LANG']['avatar'];

		// check for errors
		$err = $avfile['error'];
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
		} // if

		// check file extension
		$parts = pathinfo($fname);
		if (!in_array($parts['extension'], explode(',',$conf['validImageTypes']))) {
			$this->addError(sprintf($text['invalidfilext'],$fname));
		} // if
				
		// check file size
		$size = $avfile['size'];
		if ($size > $this->maxsize) {
			$this->addError(sprintf($text['filetoobig'],$fname));
			return;
		} // if
		
		// check for valid image
		$imgsize = @getimagesize($tmp);
		if ($imgsize === false) {
			$this->addError(sprintf($text['novalidimage'],$fname));
			return;
		} // if
		
		// check dimensions
		if ($imgsize[0] > $this->maxdims ||$imgsize[1] > $this->maxdims) {
			$this->addError(sprintf($text['imgtoobig'],$fname));
			return;
		} // if

		// save file
		if (!$this->hasErrors()) {
			$avfile = $GLOBALS['TL_CONFIG']['avatar_dir'].'/'.sprintf($this->filename,$this->User->id).'.'.$parts['extension'];
			$this->import('Files');
			$this->Files->move_uploaded_file($tmp, $avfile);
			$this->Files->chmod($avfile, 0644);
			$this->varValue = $avfile;
		} // if
	}

	public function generate()
	{
		$fname = $this->strName;
		$img = Avatar::img($this->User->$fname);
		if ($img == '') return $img;
		$text = &$GLOBALS['TL_LANG']['avatar']; 
		return 
			"$img<br/>\n".
			sprintf(
				'<div id="%s_reset_container" class="tl_checkbox_container">',
				$this->strName
			)."\n".
			sprintf(
				'  <input type="checkbox" name="%s_reset" id="ctrl_%s_reset" class="checkbox avatar%s" value="1" />',
				$this->strName,
				$this->strId,
				(strlen($this->strClass) ? ' ' . $this->strClass : '')
			)."\n".
			sprintf('  <label for="ctrl_%s_reset" class="label%s">%s</label>',
				$this->strId,
				(strlen($this->strClass) ? ' ' . $this->strClass : ''),
				$text['reset']
			)."\n</div>\n";
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
				'<input type="file" name="%s_file" id="ctrl_%s_file" size="40" class="text upload avatarfile%s"%s />%s',
				$this->strName,
				$this->strId,
				(strlen($this->strClass) ? ' ' . $this->strClass : ''),
				$this->getAttributes(),
				($info!='' ? "\n".'<div class="hint">'.$info.'</div>'."\n" : '')
			);
	} // generateUpload
}

?>