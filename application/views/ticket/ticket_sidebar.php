<?php if (active_project()) { ?>
<div class="sidebarBlock">
  <h2><?php echo lang('view'); ?></h2>
  <div class="blockContent">
    <ul>
      <li><a href="<?php echo get_url('ticket', 'index', array('status' => 'new,open,pending')) // TODO statuses are hard-coded here ?>"><?php echo lang('open tickets') ?></a></li>
      <li><a href="<?php echo get_url('ticket', 'index', array('status'=>'closed')) // TODO status is hard-coded here ?>"><?php echo lang('closed tickets') ?></a></li>
      <li><a href="<?php echo get_url('ticket', 'categories') ?>"><?php echo lang('categories') ?></a></li>
    </ul>
  </div>
</div>
<?php } ?>