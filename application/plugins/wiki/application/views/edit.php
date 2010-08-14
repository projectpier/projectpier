<?php

/**
 * @author Alex Mayhew
 * @copyright 2008
 */

$project_crumbs = array(
  array(lang('wiki'), get_url('wiki')));
if (!$page->isNew()){
  $project_crumbs[] = array($revision->getName(), $page->getViewUrl());
  $project_crumbs[] = array(lang('edit wiki page'));
} else {
	$project_crumbs[] = array(lang('add wiki page'));
} // if


if (!$revision->getName()) {
  set_page_title(lang('wiki'));
} else {
  set_page_title(lang('editing', $revision->getName()));
} // if
project_tabbed_navigation(PROJECT_TAB_WIKI);
project_crumbs($project_crumbs);

?>

<?php if ($page->isNew()) { ?>
<form action="<?php echo $page->getAddUrl() ?>" method="POST">
<?php } else { ?>
<form action="<?php echo $page->getEditUrl() ?>" method="POST">
<?php } // if ?>
<?php tpl_display(get_template_path('form_errors')) ?>

<div id="wiki-field-name">
<?php if ($revision->getName()) { ?>
<?php echo text_field('wiki[name]', $revision->getName(), array('type' => 'hidden')) ?>
<?php } else { ?>
<?php echo label_tag(lang('name'), 'wikiFormName', true) ?>
<?php echo text_field('wiki[name]', $revision->getName(), array('class' => 'long', 'id' => 'wikiFormName')) ?>
<?php } // if ?>
</div>
<div id="wiki-field-content">
<?php echo label_tag(lang('wiki page content'), 'wikiFormContent', true) ?>
<?php echo textarea_field('wiki[content]', $revision->getContent(), array('cols' => 400, 'class' => 'shot', 'id' => 'wikiFormContent')) ?>
</div>
<div id-"wiki-field-log">
<?php echo label_tag(lang('wiki log message'), 'wikiFormLog') ?>
<?php echo text_field('wiki[log_message]', ($page->isNew() ? 'Page created' : ''), array('class' => 'long', 'id' => 'wikiFormLog')) ?>
</div>
<div id="wiki-field-indexpage">
<?php echo label_tag(lang('wiki set page as index'), 'wikiFormIndexYes') ?>
<?php echo yes_no_widget('wiki[project_index]', 'wikiFormIndex', $page->getProjectIndex(), lang('yes'), lang('no')) ?>
</div>
<div id="wiki-field-sidebarpage">
<?php echo label_tag(lang('wiki set page as sidebar'), 'wikiFormSidebarYes') ?>
<?php echo yes_no_widget('wiki[project_sidebar]', 'wikiFormSidebar', $page->getProjectSidebar(), lang('yes'), lang('no')) ?>
</div>
<?php if ($page->canLock(logged_user())) { ?>
<div id="wiki-field-lockpage">
<?php echo label_tag(lang('wiki lock page'), 'wikiFormLockPageYes') ?>
<?php echo yes_no_widget('wiki[locked]', 'wikiFormLockPage', $page->getLocked(), lang('yes'), lang('no')) ?><br/>
<?php if ($page->getLocked()) { ?>
	<?php echo lang('wiki page locked'); ?><br/>
	<?php echo lang('wiki page locked by on', $page->getLockedByUser()->getUsername(), format_datetime($page->getLockedOn())); ?><br/>
<?php } else { ?>
	<?php echo lang('wiki page not locked'); ?><br/>
<?php } // if ?>
</div>
<?php } // if ?>

<?php echo submit_button($page->isNew() ? lang('add wiki page') : lang('edit wiki page')) ?>

</form>