<?php
$contact = $attachment->getObject();
$company = $contact->getCompany();
?>
<div class="contactAttachment">
  <fieldset>
  <legend><?php echo $attachment->getText();?></legend>
  <div class="contactAvatar"><img src="<?php echo $contact->getAvatarUrl() ?>" alt="<?php echo clean($contact->getDisplayName()) ?> <?php echo lang('avatar') ?>" /></div>
<?php if (logged_user()->isMemberOfOwnerCompany() && !$contact->isMemberOfOwnerCompany()) { ?>
  <div class="contactFavorite <?php if ($contact->isFavorite()) { echo "favorite_on"; } else { echo "favorite_off"; }?>">
<?php if (logged_user()->isAdministrator()) { ?>
    <a href="<?php echo $contact->getToggleFavoriteUrl($contact->getCompany()->getViewUrl()); ?>"><img src="<?php echo get_image_url("icons/favorite.png"); ?>" title="<?php echo lang('toggle favorite'); ?>" alt="<?php echo lang('toggle favorite'); ?>"/></a>
<?php } else { ?>
    <img src="<?php echo get_image_url("icons/favorite.png"); ?>" title="<?php echo ($contact->isFavorite() ? lang('favorite') : lang('not favorite')); ?>" alt="<?php echo ($contact->isFavorite() ? lang('favorite') : lang('not favorite')); ?>">
<?php } ?>
  </div>
<?php } ?>
  <div class="contactName"><a href="<?php echo $contact->getCardUrl() ?>"><?php echo clean($contact->getDisplayName()) ?></a><?php if ($contact->getTitle() != '') echo " &mdash; ".clean($contact->getTitle()) ?> @ <a href="<?php echo $company->getCardUrl(); ?>"><?php echo $company->getName(); ?></a></div>
  <div class="contactInfo">
    <div class="cardBlock">
<?php if (trim($contact->getEmail()) != '') { ?>
      <div><span><?php echo lang('email address') ?>:</span> <a href="mailto:<?php echo $contact->getEmail() ?>"><?php echo $contact->getEmail() ?></a></div>
<?php } // if ?>
<?php if (trim($contact->getOfficeNumber()) != '') { ?>
      <div><span><?php echo lang('office phone number') ?>:</span> <?php echo clean($contact->getOfficeNumber()) ?></div>
<?php } // if ?>
<?php if (trim($contact->getFaxNumber()) != '') { ?>
      <div><span><?php echo lang('fax number') ?>:</span> <?php echo clean($contact->getFaxNumber()) ?></div>
<?php } // if ?>
<?php if (trim($contact->getMobileNumber()) != '') { ?>
      <div><span><?php echo lang('mobile phone number') ?>:</span> <?php echo clean($contact->getMobileNumber()) ?></div>
<?php } // if ?>
<?php if (trim($contact->getHomeNumber()) != '') { ?>
      <div><span><?php echo lang('home phone number') ?>:</span> <?php echo clean($contact->getHomeNumber()) ?></div>
<?php } // if ?>
<?php if ($company->hasAddress()) { ?>
      <div>
        <span><?php echo lang('address') ?>:</span><br/>
        <?php echo $company->getAddress().', '.$company->getAddress2(); ?><br/>
        <?php echo $company->getCity().', '.$company->getState().' '.$company->getZipcode(); ?>
      </div>
<?php } // if ?>
    </div>
  </div>
  <div class="clear"></div>
  </fieldset>
</div>