------------------------------------------------------------
 <?php echo lang('dont reply wraning') ?> 
------------------------------------------------------------

<?php echo lang('new message posted', $new_message->getTitle(), $new_message->getProject()->getName()) ?>. 

<?php
/* Send the message body unless the configuration file specifically says not to:
** to prevent sending the body of email messages add the following to config.php
** For config.php:  define('SHOW_MESSAGE_BODY', false);
*/
if ((!defined('SHOW_MESSAGE_BODY')) or (SHOW_MESSAGE_BODY == true)) {
  echo "\n----------------\n";
  echo $new_message->getText();
  echo "\n----------------\n\n";
}
?>

<?php echo lang('view new message') ?>:

- <?php echo str_replace('&amp;', '&', $new_message->getViewUrl()) ?> 

Company: <?php echo owner_company()->getName() ?> 
Project: <?php echo $new_message->getProject()->getName() ?> 

--
<?php echo ROOT_URL ?>
