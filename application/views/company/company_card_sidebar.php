<?php if (is_array($contacts) && count($contacts)) { ?>
<div class="companyContacts">
  <h4><?php echo lang('contacts'); ?></h4>
<?php foreach ($contacts as $contact) { ?>
  <div class="companyContact">
<?php if ($contact->hasAvatar()) { ?>
    <span class="contactAvatar"><a href="<?php echo $contact->getCardUrl() ?>"><img src="<?php echo $contact->getAvatarUrl(); ?>" alt="<?php echo clean($contact->getDisplayName()) ?> <?php echo lang('avatar') ?>" /></a></span>
<?php } // if ?>
    <a href="<?php echo $contact->getCardUrl() ?>"><?php echo $contact->getDisplayName(); ?></a>
<?php if (logged_user()->isMemberOfOwnerCompany() && !$contact->isMemberOfOwnerCompany()) { ?>
    <span class="contactFavorite <?php if ($contact->isFavorite()) { echo "favorite_on"; } else { echo "favorite_off"; }?>">
      <img src="<?php echo get_image_url("icons/favorite.png"); ?>" title="<?php echo ($contact->isFavorite() ? lang('favorite') : lang('not favorite')); ?>" alt="<?php echo ($contact->isFavorite() ? lang('favorite') : lang('not favorite')); ?>">
    </span>
<?php } ?>

  </div>
<?php } // foreach ?>
</div>
<?php } // if ?>
<?php if (is_array($active_projects) && count($active_projects)) { ?>
<div class="companyProjects">
  <h4><?php echo lang('active projects'); ?></h4>
  <ul>
<?php foreach ($active_projects as $project) { ?>
  <li class="companyProject">
    <a href="<?php echo $project->getOverviewUrl(); ?>"><?php echo $project->getName(); ?></a>
  </li>
<?php } // foreach ?>
</div>
<?php } // if ?>