<?php if (isset($company) && ($company instanceof Company)) { ?>
<div class="card">
  <div class="cardIcon"><img src="<?php echo $company->getLogoUrl() ?>" alt="<?php echo clean($company->getName()) ?> logo" /></div>
  <div class="cardData">
<?php if (logged_user()->isMemberOfOwnerCompany() && !$company->isOwner()) { ?>
    <div class="cardFavorite <?php if ($company->isFavorite()) { echo "favorite_on"; } else { echo "favorite_off"; }?>">
<?php if (logged_user()->isAdministrator()) { ?>
      <a href="<?php echo $company->getToggleFavoriteUrl($company->getViewUrl()); ?>"><img src="<?php echo get_image_url("icons/favorite.png"); ?>" title="<?php echo lang('toggle favorite'); ?>" alt="<?php echo lang('toggle favorite'); ?>"/></a>
<?php } else { ?>
      <img src="<?php echo get_image_url("icons/favorite.png"); ?>" title="<?php echo ($company->isFavorite() ? lang('favorite') : lang('not favorite')); ?>" alt="<?php echo ($company->isFavorite() ? lang('favorite') : lang('not favorite')); ?>">
<?php } ?>
    </div>
<?php } ?>
    <h2><?php echo clean($company->getName()) ?></h2>
    
    <div class="cardBlock">
      <div><span><?php echo lang('email address') ?>:</span> <a href="mailto:<?php echo $company->getEmail() ?>"><?php echo $company->getEmail() ?></a></div>
      <div><span><?php echo lang('phone number') ?>:</span> <?php echo $company->getPhoneNumber() ? clean($company->getPhoneNumber()) : lang('n/a') ?></div>
      <div><span><?php echo lang('fax number') ?>:</span> <?php echo $company->getFaxNumber() ? clean($company->getFaxNumber()) : lang('n/a') ?></div>
<?php if ($company->hasHomepage()) { ?>
      <div><span><?php echo lang('homepage') ?>:</span> <a href="<?php echo $company->getHomepage() ?>"><?php echo $company->getHomepage() ?></a></div>
<?php } else { ?>
      <div><span><?php echo lang('homepage') ?>:</span> <?php echo lang('n/a') ?></div>
<?php } // if ?>
    </div>
    

    <h2><?php echo lang('address') ?></h2>
    
    <div class="cardBlock" style="margin-bottom: 0">
<?php if ($company->hasAddress()) { ?>
      <?php echo clean($company->getAddress()) ?>
<?php if (trim($company->getAddress2())) { ?>
      <br /><?php echo clean($company->getAddress2()) ?>
<?php } // if ?>
      <br /><?php echo clean($company->getCity()) ?>, <?php echo clean($company->getState()) ?> <?php echo clean($company->getZipcode()) ?>
<?php if (trim($company->getCountry())) { ?>
      <br /><?php echo clean($company->getCountryName()) ?>
<?php } // if ?>
<?php } else { ?>
      <?php echo lang('n/a') ?>
<?php } // if ?>
    </div>
  
  </div>
</div>
<?php } // if ?>
