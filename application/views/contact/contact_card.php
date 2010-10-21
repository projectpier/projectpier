<?php if (isset($contact) && ($contact instanceof Contact)) { ?>
<div class="card">
  <div class="cardIcon"><img src="<?php echo $contact->getAvatarUrl() ?>" alt="<?php echo clean($contact->getDisplayName()) ?> avatar" /></div>
  <div class="cardData">
<?php if (logged_user()->isMemberOfOwnerCompany() && !$contact->isMemberOfOwnerCompany()) { ?>
      <div class="cardFavorite <?php if ($contact->isFavorite()) { echo "favorite_on"; } else { echo "favorite_off"; }?>">
<?php if (logged_user()->isAdministrator()) { ?>
        <a href="<?php echo $contact->getToggleFavoriteUrl($contact->getCardUrl()); ?>"><img src="<?php echo get_image_url("icons/favorite.png"); ?>" title="<?php echo lang('toggle favorite'); ?>" alt="<?php echo lang('toggle favorite'); ?>"/></a>
<?php } else { ?>
        <img src="<?php echo get_image_url("icons/favorite.png"); ?>" title="<?php echo ($contact->isFavorite() ? lang('favorite') : lang('not favorite')); ?>" alt="<?php echo ($contact->isFavorite() ? lang('favorite') : lang('not favorite')); ?>">
<?php } ?>
      </div>
<?php } ?>
    <h2><?php echo clean($contact->getDisplayName()) ?></h2>
    
    <div class="cardBlock">
      <div><span><?php echo lang('contact title') ?>:</span> <?php echo $contact->getTitle() ? clean($contact->getTitle()) : lang('n/a') ?></div>
      <div><span><?php echo lang('company') ?>:</span> <a href="<?php echo $contact->getCompany()->getCardUrl() ?>"><?php echo clean($contact->getCompany()->getName()) ?></a></div>
    </div>
    
    <h2><?php echo lang('contact online') ?></h2>
    
    <div class="cardBlock">
      <div><span><?php echo lang('email address'); ?>:</span> <?php if ($contact->getEmail()) { ?><a href="mailto:<?php echo clean($contact->getEmail()) ?>"><?php echo clean($contact->getEmail()); ?></a><?php } else { echo lang('n/a'); } ?></div>
      
<?php if (is_array($im_values = $contact->getImValues()) && count($im_values)) { ?>
      <table class="imAddresses">
<?php foreach ($im_values as $im_value) { ?>
<?php if ($im_type = $im_value->getImType()) { ?>
        <tr>
          <td><img src="<?php echo $im_type->getIconUrl() ?>" alt="<?php echo $im_type->getName() ?>" /></td>
          <td><?php echo clean($im_value->getValue()) ?> <?php if ($im_value->getIsDefault()) { ?><span class="desc">(<?php echo lang('primary im service') ?>)</span><?php } ?></td>
        </tr>
<?php } // if ?>
<?php } // foreach ?>
      </table>
<?php } // if ?>

    </div>
    
    <h2><?php echo lang('contact offline') ?></h2>
    
    <div class="cardBlock" style="margin-bottom: 0">
      <div><span><?php echo lang('office phone number') ?>:</span> <?php echo $contact->getOfficeNumber() ? clean($contact->getOfficeNumber()) : lang('n/a') ?></div>
      <div><span><?php echo lang('fax number') ?>:</span> <?php echo $contact->getFaxNumber() ? clean($contact->getFaxNumber()) : lang('n/a') ?></div>
      <div><span><?php echo lang('mobile phone number') ?>:</span> <?php echo $contact->getMobileNumber() ? clean($contact->getMobileNumber()) : lang('n/a') ?></div>
      <div><span><?php echo lang('home phone number') ?>:</span> <?php echo $contact->getHomeNumber() ? clean($contact->getHomeNumber()) : lang('n/a') ?></div>
    </div>
  
  </div>
</div>
<?php } // if ?>
