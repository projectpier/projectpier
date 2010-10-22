<div class="sidebarBlock">
  <h2><?php echo $sidebar_revision->getName() ?></h2>
  <div class="blockContent">
    <?php echo wiki_links(do_textile($sidebar_revision->getContent())) ?>
<?php if (!$sidebar_page->isNew() && $sidebar_page->canEdit(logged_user())) { ?>
    <p><a href="<?php echo $sidebar_page->getEditUrl() ?>"><?php echo lang('edit') ?></a></p>
<?php } // if ?>
    </div>
  </div>
<?php if (isset($sidebar_links) && count($sidebar_links)) { ?>
    <div class="sidebarBlock">
      <ul>
        <?php foreach ($sidebar_links as $spage) { ?>
          <li><a href="<?php echo $spage['view_url'] ?>"><?php echo $spage['name'] ?></a></li>
        <?php } // foreach ?>
      </ul>
    </div>
<?php } // if ?>
