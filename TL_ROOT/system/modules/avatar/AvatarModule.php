<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
/**
 * TYPOlight Avatars :: Frontend avatar module
 *
 * NOTE: this file was edited with tabs set to 4.
 * @package Avatars
 * @copyright Copyright (C) 2008 by Peter Koch, IBK Software AG
 * @license See accompaning file LICENSE.txt
 */

class AvatarModule extends Module
{
	protected $strTemplate = 'avatar_module';
	protected $form;
	
	public function generate()
	{
		if (TL_MODE == 'BE') {
			$objTemplate = new BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### PERSONAL AVATAR ###';
			return $objTemplate->parse();
		} // if

		// Return if there is no logged in user
		if (!FE_USER_LOGGED_IN) return '';
		return parent::generate();
	} // generate

	protected function compile()
	{
		global $objPage;
		$GLOBALS['TL_LANGUAGE'] = $objPage->language;
		$this->import('FrontendUser', 'User');
		$this->import('Database');
		$db = &$this->Database;
		
		$this->loadLanguageFile('avatar');
		$text = &$GLOBALS['TL_LANG']['avatar']; 
		$conf = &$GLOBALS['TL_CONFIG'];
		
		$this->form = new stdClass();
		$this->Template->form = $this->form;
		$form = $this->form;
		
		$form->formlink	= ($conf['rewriteURL'] ? '' : 'index.php/') . $this->getPageIdFromUrl() . URL_SUFFIX;
		
		if ((int)$this->Input->post('avatar_action')>0) {
			// check reset
			if ((int)$this->Input->post('avatar_reset')>0) {
				// save empty string
				$db	->prepare("update `tl_member` set avatar='' where `id`=?")
					->execute($this->User->id);
				$this->reload();
			} // if

			$ok = true;
			
			// check upload
			$tmp = $_FILES['avatar_file']['tmp_name'];
			if ($tmp == '') $ok = false;
			$avfile = &$_FILES['avatar_file'];
			
			if ($ok) {
				// check for errors
				$err = $avfile['error'];
				if ($err != UPLOAD_ERR_OK) {
					switch ($err) {
						case UPLOAD_ERR_INI_SIZE:
							$form->avatar_msg = sprintf($text['errinisize'],$fname);
							break;
						case UPLOAD_ERR_FORM_SIZE:
							$form->avatar_msg = sprintf($text['errformsize'],$fname);
							break;
						case UPLOAD_ERR_PARTIAL:
							$form->avatar_msg = sprintf($text['errpartial'],$fname);
							break;
						case UPLOAD_ERR_NO_TMP_DIR:
							$form->avatar_msg = $text['notmpdir'];
							break;
						case UPLOAD_ERR_CANT_WRITE:
							$form->avatar_msg = sprintf($text['cantwrite'],$fname);
							break;
						default:
							$form->avatar_msg = sprintf($text['errorno'],$fname, $err);
					} // switch
					$ok = false;
				} // if
			} // if
			
			if ($ok) {
				// check file extension
				$fname = $avfile['name'];
				$parts = pathinfo($fname);
				if (!in_array($parts['extension'], explode(',',$conf['validImageTypes']))) {
					$form->avatar_msg = sprintf($text['invalidfilext'],$fname);
					$ok = false;
				} // if
			} // if
			
			if ($ok) {
				// check file size
				$size = $avfile['size'];
				if ($size > $conf['avatar_maxsize']) {
					$form->avatar_msg = sprintf($text['filetoobig'],$fname);
					$ok = false;
				} // if
			} // if
				
			if ($ok) {
				// check for valid image
				$imgsize = @getimagesize($tmp);
				if ($imgsize === false) {
					$form->avatar_msg = sprintf($text['novalidimage'],$fname);
					$ok = false;
				} // if
			} // if
				
			if ($ok) {
				// check dimensions
				if ($imgsize[0]>$conf['avatar_maxdims'] ||$imgsize[1]>$conf['avatar_maxdims']) {
					$form->avatar_msg = sprintf($text['imgtoobig'],$fname);
					$ok = false;
				} // if
			} // if

			if ($ok) {
				// save file
				$avfile = $conf['avatar_dir'].'/tl_member_'.$this->User->id.'.'.$parts['extension'];
				$this->import('Files');
				$this->Files->move_uploaded_file($tmp, $avfile);
				$this->Files->chmod($avfile, 0644);
				$db	->prepare("update `tl_member` set avatar=? where `id`=?")
					->execute(array($avfile, $this->User->id));
				$this->reload();
			} // if
		} // if
		
		// get current avatar file
		$form->avatar = Avatar::filename($this->User->avatar);
		if ($form->avatar != '') {
			$imgsize = @getimagesize(TL_ROOT.'/'.$form->avatar);
			if ($imgsize === false)
				$form->avatar = '';
			else
				$form->dims = $imgsize[3];
		} // if

		// prepare upload hint
		$form->avatar_hint =
			sprintf(
				$text['file'][1], 
				$conf['validImageTypes'],
				$conf['avatar_maxsize'],
				$conf['avatar_maxdims'],
				$conf['avatar_maxdims']
			);
	} // compile
	
} // AvatarModule

?>