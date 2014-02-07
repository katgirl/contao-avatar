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

namespace KirstenRoschanski\Avatar\Widget;

/**
 * Class AvatarWidget
 *
 * Widget for members avatar.
 *
 * @copyright  Kirsten Roschanski (C) 2013
 * @copyright  Tristan Lins (C) 2013
 * @author     Kirsten Roschanski <kirsten@kat-webdesign.de>
 * @author     Tristan Lins <tristan.lins@bit3.de>
 */
class AvatarFileUpload extends \Widget implements \uploadable
{

	/**
	 * Submit user input
	 *
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 *
	 * @var string
	 */
	protected $strTemplate = 'be_widget';

	/**
	 * Order ID
	 *
	 * @var string
	 */
	protected $strOrderId;

	/**
	 * Order name
	 *
	 * @var string
	 */
	protected $strOrderName;

	/**
	 * Order field
	 *
	 * @var string
	 */
	protected $strOrderField;


	/**
	 * Load the database object
	 *
	 * @param array
	 */
	public function __construct($arrAttributes = null)
	{
		$this->import('Database');
		parent::__construct($arrAttributes);

		$this->strOrderField = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['avatar'];

		// Prepare the order field
		if ($this->strOrderField != '') {
			$this->strOrderId   = $this->strOrderField . str_replace($this->strField, '', $this->strId);
			$this->strOrderName = $this->strOrderField . str_replace($this->strField, '', $this->strName);

			// Retrieve the order value
			$objRow = $this->Database
				->prepare("SELECT {$this->strOrderField} FROM {$this->strTable} WHERE id=?")
				->limit(1)
				->execute($this->activeRecord->id);

			$tmp = deserialize($objRow->{$this->strOrderField});
      $this->{$this->strOrderField} = (!empty($tmp) && is_array($tmp)) ? array_filter($tmp) : array();
		}
	}


	/**
	 * Return an array if the "multiple" attribute is set
	 *
	 * @param mixed
	 *
	 * @return mixed
	 */
	protected function validator($varInput)
	{
    // Store the order value
    if ($this->strOrderField != '')
    {
      $arrNew = array_map('String::uuidToBin', explode(',', \Input::post($this->strOrderName)));

      // Only proceed if the value has changed
      if ($arrNew !== $this->{$this->strOrderField})
      {
        $this->Database->prepare("UPDATE {$this->strTable} SET tstamp=?, {$this->strOrderField}=? WHERE id=?")
                                   ->execute(time(), serialize($arrNew), \Input::get('id'));

        $this->objDca->createNewVersion = true; // see #6285
      }
    }

    // Return the value as usual
    if ($varInput == '')
    {
      if ($this->mandatory)
      {
        $this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['mandatory'], $this->strLabel));
      }

      return '';
    }
    elseif (strpos($varInput, ',') === false)
    {
      $varInput = \String::uuidToBin($varInput);
      return $this->blnIsMultiple ? array($varInput) : $varInput;
    }
    else
    {
      $arrValue = array_filter(explode(',', $varInput));
      return $this->blnIsMultiple ? array_map('String::uuidToBin', $arrValue) : \String::uuidToBin($arrValue[0]);
    }
	}


	/**
	 * Generate the widget and return it as string
	 *
	 * @return string
	 */
	public function generate()
	{
    $arrSet = array();
    $arrValues = array();
    $blnHasOrder = ($this->strOrderField != '' && is_array($this->{$this->strOrderField}));
    $arrImage = deserialize($GLOBALS['TL_CONFIG']['avatar_maxdims']);

    if (!empty($this->varValue)) // Can be an array
    {
      $objFiles = \FilesModel::findMultipleByUuids((array)$this->varValue);
      $allowedDownload = trimsplit(',', strtolower($GLOBALS['TL_CONFIG']['allowedDownload']));

      if ($objFiles !== null)
      {
        while ($objFiles->next())
        {
          // File system and database seem not in sync
          if (!file_exists(TL_ROOT . '/' . $objFiles->path))
          {
            continue;
          }

          $arrSet[] = $objFiles->uuid;

          if ($objFiles->type == 'folder')
          {
            continue;
          }

          $objFile = new \File($objFiles->path, true);
          $strInfo = $objFiles->path . ' <span class="tl_gray">(' . $this->getReadableSize($objFile->size) . ($objFile->isGdImage ? ', ' . $arrImage[0] . 'x' . $arrImage[1] . ' px' : '') . ')</span>';
          $arrValues[$objFiles->uuid] = \Image::getHtml(\Image::get($objFiles->path, $arrImage[0], $arrImage[1], $arrImage[2]), '', 'class="gimage" title="' . specialchars($strInfo) . '"');
        }
      }
    }
   
    if(count($arrValues) === 0)
    {
      $objFile = \FilesModel::findByUuid($GLOBALS['TL_CONFIG']['avatar_fallback_image']);    
      $strInfo = $objFile->path . ' <span class="tl_gray">(' . $this->getReadableSize($objFile->size) . ($objFile->isGdImage ? ', ' . $arrImage[0] . 'x' . $arrImage[1] . ' px' : '') . ')</span>';
      $arrValues[$objFile->uuid] = \Image::getHtml(\Image::get($objFile->path, $arrImage[0], $arrImage[1], $arrImage[2]), '', 'class="gimage" title="' . specialchars($strInfo) . '"');
    }

    // Load the fonts for the drag hint (see #4838)
    $GLOBALS['TL_CONFIG']['loadGoogleFonts'] = true;

    // Convert the binary UUIDs
    $strSet = implode(',', array_map('String::binToUuid', $arrSet));
    $strOrder = $blnHasOrder ? implode(',', array_map('String::binToUuid', $this->{$this->strOrderField})) : '';

    $return = '<input type="hidden" name="'.$this->strName.'" id="ctrl_'.$this->strId.'" value="'.$strSet.'">' . ($blnHasOrder ? '
<input type="hidden" name="'.$this->strOrderName.'" id="ctrl_'.$this->strOrderId.'" value="'.$strOrder.'">' : '') . '
<div class="selector_container">' . (($blnHasOrder && count($arrValues)) ? '
<p class="sort_hint">' . $GLOBALS['TL_LANG']['MSC']['dragItemsHint'] . '</p>' : '') . '
<ul id="sort_'.$this->strId.'" class="'.trim(($blnHasOrder ? 'sortable ' : '').($this->blnIsGallery ? 'sgallery' : '')).'">';

    foreach ($arrValues as $k=>$v)
    {
      $return .= '<li data-id="'.\String::binToUuid($k).'">'.$v.'</li>';
    }

    $return .= '</ul>
<p><a href="contao/file.php?do='.\Input::get('do').'&amp;table='.$this->strTable.'&amp;field='.$this->strField.'&amp;act=show&amp;id='.\Input::get('id').'&amp;value='.$strSet.'&amp;rt='.REQUEST_TOKEN.'" class="tl_submit" onclick="Backend.getScrollOffset();Backend.openModalSelector({\'width\':765,\'title\':\''.specialchars(str_replace("'", "\\'", $GLOBALS['TL_LANG']['MSC']['filepicker'])).'\',\'url\':this.href,\'id\':\''.$this->strId.'\'});return false">'.$GLOBALS['TL_LANG']['MSC']['changeSelection'].'</a></p>' . ($blnHasOrder ? '
<script>Backend.makeMultiSrcSortable("sort_'.$this->strId.'", "ctrl_'.$this->strOrderId.'")</script>' : '') . '
</div>';

    if (!\Environment::get('isAjaxRequest'))
    {
      $return = '<div>' . $return . '</div>';
    }

    return $return;
	}
}	
