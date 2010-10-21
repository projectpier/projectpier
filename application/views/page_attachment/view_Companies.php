<?php
$company = $attachment->getObject();
?>
<div class="companyAttachment">
  <fieldset>
  <legend><?php echo $attachment->getText();?></legend>
  <div class="companyName"><a href="<?php echo $company->getViewUrl(); ?>"><?php echo $company->getName(); ?></a></div>
  <div class="companyInfo">
    <div class="cardBlock">
<?php if (trim($company->getEmail()) != '') { ?>
      <div><span><?php echo lang('email address') ?>:</span> <a href="mailto:<?php echo $company->getEmail() ?>"><?php echo $company->getEmail() ?></a></div>
<?php } ?>
<?php if (trim($company->getPhoneNumber()) != '') { ?>
      <div><span><?php echo lang('phone number') ?>:</span> <?php echo clean($company->getPhoneNumber()) ?></div>
<?php } ?>
<?php if (trim($company->getFaxNumber()) != '') { ?>
      <div><span><?php echo lang('fax number') ?>:</span> <?php echo clean($company->getFaxNumber()) ?></div>
<?php } ?>
<?php if ($company->hasHomepage()) { ?>
      <div><span><?php echo lang('homepage') ?>:</span> <a href="<?php echo $company->getHomepage() ?>"><?php echo $company->getHomepage() ?></a></div>
<?php } ?>
    </div>
  </div>
  </fieldset>
</div>