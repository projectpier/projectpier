<?php 
  add_stylesheet_to_page('project/messages.css'); 
?>
<tr class="message <?php echo $odd_or_even; ?>">
  <td>
    <?php if ($message->isPrivate()) { ?>
        <div class="private" title="<?php echo lang('private message') ?>"><span><?php echo lang('private message') ?></span></div>
    <?php } // if ?>
  </td>
  <td>
    <?php echo format_datetime($message->getCreatedOn(), "m/d/Y, h:ia"); ?>
  </td>
  <td>
    <a href="<?php echo $message->getViewUrl() ?>"><?php echo clean($message->getTitle()) ?></a>
  </td>
  <td>
    <a href="<?php echo $message->getCreatedBy()->getCardUrl() ?>"><?php echo clean($message->getCreatedBy()->getDisplayName()) ?></a>
  </td>
  <td>
    <a href="<?php echo $message->getViewUrl() ?>#objectComments"><?php echo $message->countComments() ?></a>
  </td>
  <td>
    <?php echo count($message->getAttachedFiles()) ?>
  </td>
</tr>