<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
/**
 * TYPOlight Helpdesk :: Configuration file
 *
 * Copyright (C) 2007 by Acenes. See accompaning files LICENSE.txt and GPL2.txt 
 * for license conditions and author contact information.
 *
 * NOTE: this file was edited with tabs set to 4.
 */

/**
 * FRONT END MODULES
 */
$GLOBALS['FE_MOD']['user'] += array('avatar' => 'AvatarModule');

/**
 * BACK END FORM FIELDS
 */
$GLOBALS['BE_FFL'] += array('avatar' => 'AvatarWidget');

/**
 * FRONT END FORM FIELDS
 */
$GLOBALS['TL_FFL'] += array('avatar' => 'AvatarForm');

/**
 * HOOKS
 */
$GLOBALS['TL_HOOKS'][(VERSION=='2.5' && (int)BUILD<10) ? 'outputTemplate' : 'outputFrontendTemplate'][] = array('AvatarTags', 'replaceTags');


?>