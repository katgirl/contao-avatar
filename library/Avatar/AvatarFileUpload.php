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
class AvatarFileUpload extends \Widget implements \uploadable
{

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget';

	/**
	 * Order ID
	 * @var string
	 */
	protected $strOrderId;

	/**
	 * Order name
	 * @var string
	 */
	protected $strOrderName;

	/**
	 * Order field
	 * @var string
	 */
	protected $strOrderField;


	/**
	 * Load the database object
	 * @param array
	 */
	public function __construct($arrAttributes=null)
	{
		$this->import('Database');
		parent::__construct($arrAttributes);

		$this->strOrderField = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['avatar'];

		// Prepare the order field
		if ($this->strOrderField != '')
		{
			$this->strOrderId = $this->strOrderField . str_replace($this->strField, '', $this->strId);
			$this->strOrderName = $this->strOrderField . str_replace($this->strField, '', $this->strName);

			// Retrieve the order value
			$objRow = $this->Database->prepare("SELECT {$this->strOrderField} FROM {$this->strTable} WHERE id=?")
						   ->limit(1)
						   ->execute($this->activeRecord->id);

			$this->{$this->strOrderField} = $objRow->{$this->strOrderField};
		}
	}


	/**
	 * Return an array if the "multiple" attribute is set
	 * @param mixed
	 * @return mixed
	 */
	protected function validator($varInput)
	{
		// Store the order value
		if ($this->strOrderField != '')
		{
			$this->Database->prepare("UPDATE {$this->strTable} SET {$this->strOrderField}=? WHERE id=?")
						   ->execute(\Input::post($this->strOrderName), \Input::get('id'));
		}

		// Return the value as usual
		if (strpos($varInput, ',') === false)
		{
			return intval($varInput);
		}
		else
		{
			$arrValue = array_map('intval', array_filter(explode(',', $varInput)));
			return $arrValue[0];
		}
	}



	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$strValues = '';
		$arrValues = array();
		
		if (!empty($this->varValue)) // Can be an array
		{
			$strValues = implode(',', array_map('intval', (array)$this->varValue));
			$objFiles = $this->Database->execute("SELECT id, path, type FROM tl_files WHERE id IN($strValues) ORDER BY " . $this->Database->findInSet('id', $strValues));
			$allowedDownload = trimsplit(',', strtolower($GLOBALS['TL_CONFIG']['avatar_filetype']));

			while ($objFiles->next())
			{
				// File system and database seem not in sync
				if (!file_exists(TL_ROOT . '/' . $objFiles->path))
				{
					continue;
				}

				$objFile = new \File($objFiles->path);
				if (in_array($objFile->extension, $allowedDownload) && !preg_match('/^meta(_[a-z]{2})?\.txt$/', $objFile->basename))
				{
					$arrValues[$objFiles->id] = $this->generateImage($objFile->icon) . ' ' . $objFiles->path;
				}
			}

			// Apply a custom sort order
			if ($this->strOrderField != '' && $this->{$this->strOrderField} != '')
			{
				$arrNew = array();
				$arrOrder = array_map('intval', explode(',', $this->{$this->strOrderField}));

				foreach ($arrOrder as $i)
				{
					if (isset($arrValues[$i]))
					{
						$arrNew[$i] = $arrValues[$i];
						unset($arrValues[$i]);
					}
				}

				if (!empty($arrValues))
				{
					foreach ($arrValues as $k=>$v)
					{
						$arrNew[$k] = $v;
					}
				}

				$arrValues = $arrNew;
				unset($arrNew);
			}
		}

		$return = '<input type="hidden" name="'.$this->strName.'" id="ctrl_'.$this->strId.'" value="'.$strValues.'">' . (($this->strOrderField != '') ? '
  <input type="hidden" name="'.$this->strOrderName.'" id="ctrl_'.$this->strOrderId.'" value="'.$this->{$this->strOrderField}.'">' : '') . '
  <div class="selector_container" id="target_'.$this->strId.'">' . (($this->strOrderField != '' && count($arrValues)) ? '
    <p id="hint_'.$this->strId.'" class="sort_hint">' . $GLOBALS['TL_LANG']['MSC']['dragItemsHint'] . '</p>' : '') . '
    <ul id="sort_'.$this->strId.'" class="'.trim((($this->strOrderField != '') ? 'sortable ' : '').($this->blnIsGallery ? 'sgallery' : '')).'">';

		foreach ($arrValues as $k=>$v)
		{
			$return .= '<li data-id="'.$k.'">'.$v.'</li>';
		}

		$return .= '</ul>
    <p><a href="contao/file.php?do='.\Input::get('do').'&amp;table='.$this->strTable.'&amp;field='.$this->strField.'&amp;act=show&amp;id='.\Input::get('id').'&amp;value='.$strValues.'&amp;rt='.REQUEST_TOKEN.'" class="tl_submit" onclick="Backend.getScrollOffset();Backend.openModalSelector({\'width\':765,\'title\':\''.specialchars(str_replace("'", "\\'", $GLOBALS['TL_LANG']['MOD']['files'][0])).'\',\'url\':this.href,\'id\':\''.$this->strId.'\'});return false">'.$GLOBALS['TL_LANG']['MSC']['changeSelection'].'</a></p>' . (($this->strOrderField != '') ? '
    <script>Backend.makeMultiSrcSortable("sort_'.$this->strId.'", "ctrl_'.$this->strOrderId.'");window.addEvent("sm_hide",function(){$("hint_'.$this->strId.'").destroy();$("sort_'.$this->strId.'").removeClass("sortable")})</script>' : '') . '
  </div>';

		return $return;
	}
}	
