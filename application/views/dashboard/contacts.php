<?php 

  // Set page title and set crumbs to index
  set_page_title(lang('contacts'));
  dashboard_tabbed_navigation(DASHBOARD_TAB_CONTACTS);
  dashboard_crumbs(lang('contacts'));
  
  if (logged_user()->isAdministrator(owner_company())) {
    add_page_action(lang('add company'), get_url('company', 'add_client'));
    add_page_action(lang('add contact'), get_url('contact', 'add'));
  }
?>
<?php add_stylesheet_to_page('dashboard/company_list.css') ?>
<div id="companiesList">
  <div id="companiesPaginationTop"><?php echo advanced_pagination($companies_pagination, get_url('dashboard', 'contacts', array('page' => '#PAGE#'))) ?></div>

<?php
$counter = 0;
foreach ($companies as $company) {
  $counter++;
?>
  <div class="listedCompany <?php echo $counter % 2 ? 'even' : 'odd' ?>">
    <div class="companyLogo"><img src="<?php echo $company->getLogoUrl() ?>" alt="<?php echo clean($company->getName()) ?> <?php echo lang('logo') ?>" /></div>
    <div class="companyDetails">
<?php if (logged_user()->isMemberOfOwnerCompany() && !$company->isOwner()) { ?>
      <div class="companyFavorite <?php if ($company->isFavorite()) { echo "favorite_on"; } else { echo "favorite_off"; }?>">
<?php if (logged_user()->isAdministrator()) { ?>
        <a href="<?php echo $company->getToggleFavoriteUrl($company->getCardUrl()); ?>"><img src="<?php echo get_image_url("icons/favorite.png"); ?>" title="<?php echo lang('toggle favorite'); ?>" alt="<?php echo lang('toggle favorite'); ?>"/></a>
<?php } else { ?>
        <img src="<?php echo get_image_url("icons/favorite.png"); ?>" title="<?php echo ($company->isFavorite() ? lang('favorite') : lang('not favorite')); ?>" alt="<?php echo ($company->isFavorite() ? lang('favorite') : lang('not favorite')); ?>">
<?php } // if ?>
      </div>
<?php } // if ?>
      <div class="companyName"><a href="<?php echo $company->getCardUrl() ?>"><?php echo clean($company->getName()) ?></a><?php if ($company->isOwner()) { ?> (<?php echo lang('owner company'); ?>)<?php } // if ?></div>
<?php
$options = array();
if ($company->canEdit(logged_user())) {
  $options[] = '<a href="' . $company->getEditUrl() . '">' . lang('edit') . '</a>';
} // if
if ($company->canDelete(logged_user())) {
  $options[] = '<a href="' . $company->getDeleteClientUrl() . '">' . lang('delete') . '</a>';
} // if
?>
      <div class="companyOptions"><?php echo implode(' | ', $options) ?></div>
      <div class="companyContacts">
<?php $contacts = $company->getContacts(); ?>
<?php if (is_array($contacts) && count($contacts)) { ?>
        <fieldset>
          <legend><?php echo lang('contacts'); ?></legend>
<?php foreach ($contacts as $contact) { ?>
          <span class="companyContact">
<?php if ($contact->hasAvatar()) { ?>
            <span class="contactAvatar"><a href="<?php echo $contact->getCardUrl() ?>"><img src="<?php echo $contact->getAvatarUrl(); ?>" alt="<?php echo clean($contact->getDisplayName()) ?> <?php echo lang('avatar') ?>" /></a></span>
<?php } // if ?>
            <a href="<?php echo $contact->getCardUrl() ?>"><?php echo $contact->getDisplayName(); ?></a>
          </span>
<?php  } // foreach ?>
        </fieldset>
<?php } // if ?>
      </div>
      <div class="companyProjects">
<?php $projects = $company->getActiveProjects(); ?>
<?php if (is_array($projects) && count($projects)) { ?>
        <fieldset>
          <legend><?php echo lang('active projects'); ?></legend>
<?php foreach ($projects as $project) { ?>
          <span class="companyProject">
            <a href="<?php echo $project->getOverviewUrl(); ?>"><?php echo $project->getName(); ?></a>
          </span>
<?php } // foreach ?>
        </fieldset>
<?php } // if ?>
      </div>
      <div class="clear"></div>
    </div>
  </div>
<?php } // foreach ?>
  <div id="companiesPaginationBottom"><?php echo advanced_pagination($companies_pagination, get_url('dashboard', 'contacts', array('page' => '#PAGE#'))) ?></div>
</div>