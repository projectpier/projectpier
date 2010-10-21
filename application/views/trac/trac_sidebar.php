<div class="sidebarBlock">
  <h2><?php echo lang('view'); ?></h2>
  <div class="blockContent">
    <ul>
      <li><a href="<?php echo ProjectTickets::getIndexUrl() ?>" <?php if (isset($closed) && !$closed) echo 'class="selected"'; ?>><?php echo lang('open tracTickets') ?></a></li>
      <li><a href="<?php echo ProjectTickets::getIndexUrl(true) ?>" <?php if (isset($closed) && $closed) echo 'class="selected"'; ?>><?php echo lang('closed tracTickets') ?></a></li>
      <li><a href="<?php echo get_url('trac', 'categories') ?>" <?php if (!isset($closed)) echo 'class="selected"'; ?>><?php echo lang('tracCategories') ?></a></li>
    </ul>
  </div>
</div>