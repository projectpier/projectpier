------------------------------------------------------------
 <?php echo lang('dont reply wraning') ?> 
------------------------------------------------------------

<?php echo lang('new ticket posted', $ticket->getSummary(), $ticket->getProject()->getName()) ?>. 

<?php
/* Send the message body unless the configuration file specifically says not to:
** to prevent sending the body of email messages add the following to config.php
** For config.php:  define('SHOW_MESSAGE_BODY', false);
*/
if ((!defined('SHOW_TICKET_BODY')) or (SHOW_TICKET_BODY == true)) {
  echo "\n----------------\n";
if($ticket->getAssignedTo()) {
  echo lang('assigned to').': '.clean($ticket->getAssignedTo()->getObjectName())."\n";
}
  echo lang('priority').': '.lang($ticket->getPriority())."\n";
  echo lang('type').': '.lang($ticket->getType())."\n";
if($ticket->getCategory()) {
  echo lang('category').': '.clean($ticket->getCategory()->getName())."\n";
}
  echo "\n";
  echo $ticket->getDescription();
  echo "\n----------------\n\n";
}
?>

<?php echo lang('view new ticket') ?>:

- <?php echo str_replace('&amp;', '&', $ticket->getViewUrl()) ?> 

Company: <?php echo owner_company()->getName() ?> 
Project: <?php echo $ticket->getProject()->getName() ?> 

--
<?php echo ROOT_URL ?>