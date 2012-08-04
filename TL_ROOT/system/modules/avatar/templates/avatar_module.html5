<?php
/**
 * TYPOlight Avatars :: Default frontend avatar template
 *
 * NOTE: this file was edited with tabs set to 4.
 * @package Avatars
 * @copyright Copyright (C) 2008 by Peter Koch, IBK Software AG
 * @license See accompaning file LICENSE.txt 
 */
$form = &$this->form; 
$text = &$GLOBALS['TL_LANG']['avatar']; 
?>
<!-- indexer::stop -->
<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style) { ?> style="<?php echo $this->style; ?>"<?php } ?>>

<?php if ($this->headline) { ?>
<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php } // if this->headline ?>

<form action="<?php echo $form->formlink; ?>" id="avatar_editform" name="avatar_editform" method="post" enctype="multipart/form-data" >
<div class="formbody">
<input type="hidden" name="avatar_action" value="1" />

<!-- avatar image + reset -->
<?php if ($form->avatar != '') { ?>
<div class="avatar_container"><?php echo Avatar::img($form->avatar); ?></div>
<div id="avatar_reset_container" class="checkbox_container">
  <input type="checkbox" name="avatar_reset" id="avatar_reset" class="checkbox" value="1" />
  <label for="avatar_reset" class="label"><?php echo $text['reset']; ?></label>
</div>
<?php } // if avatar ?>

<!-- upload -->
<div class="label_container"><label for="avatar_file" class="textlabel"><?php echo $text['file'][0]; ?></label></div>
<div class="file_container"><input type="file" name="avatar_file" id="avatar_file" size="70" class="text avatarfile" /></div>
<?php if (property_exists($form, 'avatar_msg')) { ?>
<div class="error_message"><?php echo $form->avatar_msg; ?></div>
<?php } // msg ?>
<div class="hint"><?php echo $form->avatar_hint; ?></div>

<!-- buttons -->
<div class="buttonwrapper">
<input type="submit" id="repository_submitbutton" class="submitbutton" value="<?php echo $text['save']; ?>" />
</div>

</div>
</form>

</div>
<!-- indexer::continue -->
