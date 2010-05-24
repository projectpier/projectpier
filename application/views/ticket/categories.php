<?php 

  // Set page title and set crumbs to index
  set_page_title(lang('categories'));
  project_tabbed_navigation(PROJECT_TAB_TICKETS);
  project_crumbs(array(
    array(lang('tickets'), get_url('ticket')),
    array(lang('categories'))
  ));
  if(Category::canAdd(logged_user(), active_project())) {
    add_page_action(lang('add category'), get_url('ticket', 'add_category'));
  }
  add_stylesheet_to_page('project/tickets.css');
?>
<?php if(isset($categories) && is_array($categories) && count($categories)) { ?>
<div id="listing">
<table width="100%" cellpadding="2" border="0">
  <tr bgcolor="#f4f4f4">
    <th>Category</th>
    <th>Description</th>
  </tr>
  <?php foreach($categories as $category) { ?>
    <tr>
      <td><a href="<?php echo $category->getViewUrl() ?>"><?php echo $category->getName() ?></a></td>
      <td><?php echo $category->getShortDescription() ?></td>
    </tr>
  <?php } // foreach ?>
</table>
</div>
<?php } else { ?>
<p><?php echo lang('no categories in project') ?></p>
<?php } // if ?>