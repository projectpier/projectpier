<?php echo lang('hi john doe', $updated_account->getDisplayName()) ?>,

<?php echo lang('user updated your account', $updated_account->getUpdatedByDisplayName()) ?>. <?php echo lang('visit and login', ROOT_URL) ?>:
<?php echo lang('username') ?>: <?php echo $updated_account->getUsername() ?> 
<?php if (trim($password) != '') { ?>
<?php echo lang('password') ?>: <?php echo $raw_password ?>
<?php } else { ?>

<?php echo lang('password unchanged') ?>
<?php } // if?>

--
<?php echo ROOT_URL ?>
