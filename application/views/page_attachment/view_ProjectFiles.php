<?php
$file = $attachment->getObject();
if ($file->canView(logged_user())) {
?>
  <div class="fileAttachment">
<?php if ($file->isPrivate()) { ?>
    <div class="private" title="<?php echo lang('private file') ?>"><span><?php echo lang('private file') ?></span></div>
<?php } // if ?>
    <div class="fileIcon"><img src="<?php echo $file->getTypeIconUrl() ?>" alt="<?php echo $file->getFilename() ?>" /></div>
    <div class="fileInfo">
      <span class="fileDescription"><?php echo $attachment->getText() ?>:</span>
      <span class="fileName"><a href="<?php echo $file->getDetailsUrl() ?>" title="<?php echo lang('view file details') ?>"><?php echo clean($file->getFilename()) ?></a></span>
    </div>
    <div class="clear"></div>
  </div>
<?php } ?>