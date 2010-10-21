<?php add_stylesheet_to_page('admin/contact_list.css') ?>
<?php echo $contact = $attachment->getObject(); ?>
<div id="contactsList">
<h2><?php echo $attachment->getText();?></h2>
<div class="contactAvatar"><img src="<?php echo $contact->getAvatarUrl() ?>" alt="<?php echo clean($contact->getDisplayName()) ?> <?php echo lang('avatar') ?>" /></div>
<div class="contactDetails">
<?php if (logged_user()->isMemberOfOwnerCompany() && !$contact->isMemberOfOwnerCompany()) { ?>
  <div class="contactFavorite <?php if ($contact->isFavorite()) { echo "favorite_on"; } else { echo "favorite_off"; }?>">
  <?php if (logged_user()->isAdministrator()) { ?>
    <a href="<?php echo $contact->getToggleFavoriteUrl($contact->getCompany()->getViewUrl()); ?>"><img src="<?php echo get_image_url("icons/favorite.png"); ?>" title="<?php echo lang('toggle favorite'); ?>" alt="<?php echo lang('toggle favorite'); ?>"/></a>
    <?php } else { ?>
      <img src="<?php echo get_image_url("icons/favorite.png"); ?>" title="<?php echo ($contact->isFavorite() ? lang('favorite') : lang('not favorite')); ?>" alt="<?php echo ($contact->isFavorite() ? lang('favorite') : lang('not favorite')); ?>">
      <?php } ?>
    </div>
    <?php } ?>
    <div class="contactName"><a href="<?php echo $contact->getCardUrl() ?>"><?php echo clean($contact->getDisplayName()) ?></a><?php if ($contact->getTitle() != '') echo " &mdash; ".clean($contact->getTitle()) ?></div>
    <div class="clear"></div>
  </div>
</div>  
