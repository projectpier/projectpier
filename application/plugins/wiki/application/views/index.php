<?php

/**
 * @author Alex Mayhew
 * @copyright 2008
 */

	set_page_title($revision->getName());
  project_tabbed_navigation(PROJECT_TAB_WIKI);
  project_crumbs(lang('wiki'));
  if ($page->canAdd(logged_user(), active_project())) {
    add_page_action(lang('add wiki page'), $page->getAddUrl());
  } // if
  if ($page->canEdit(logged_user(), active_project()) && !$page->isNew() && (!$page->isLocked() || $page->canUnlock(logged_user()))) {
		add_page_action(lang('edit wiki page'), $page->getEditUrl());
	} // if
	if (!$page->isNew()) {
		add_page_action(lang('view page history'), $page->getViewHistoryUrl());
	} // if
	
	if ($page->canDelete(logged_user(), active_project()) && !$page->isNew() && ((isset($iscurrev) && $iscurrev) || !isset($currev))) {
		add_page_action(lang('delete wiki page'), $page->getDeleteUrl());
	} // if
	add_page_action(lang('wiki all pages'), $page->getAllPagesUrl());
	
	add_inline_css_to_page('.wikiPageLocked{float:right; font-weight:bolder; border: 2px solid #D15151; padding: 2px; color: #fff; background-color: #ED6E6E}');
  
?>
<?php if ($page->getLocked()) { ?>
<div class="wikiPageLocked"><?php echo lang('wiki page locked by', $page->getLockedByUser()->getUserName()); ?></div>
<?php } // if ?>
<div id="wiki-page-content">
<?php echo wiki_links(do_textile($revision->getContent())); ?>
</div>