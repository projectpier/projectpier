<?php 

  // Set page title and set crumbs to index
  set_page_title($category->isNew() ? lang('add tracCategory') : lang('edit tracCategory'));
  project_tabbed_navigation(PROJECT_TAB_TICKETS);
  project_crumbs(array(
    array(lang('tickets'), get_url('trac')),
    array(lang('tracCategories'), get_url('trac','categories')),
    array($category->isNew() ? lang('add tracCategory') : lang('edit tracCategory'))
  )); 
  $canEdit = $category->isNew() || $category->canEdit(logged_user());
  add_stylesheet_to_page('project/tickets.css');
?>

<?php if($category->isNew()) { ?>
<form action="<?php echo get_url('trac', 'add_category') ?>" method="post" enctype="multipart/form-data">
<?php } else { ?>
<form action="<?php echo $category->getEditUrl() ?>" method="post">
<?php } // if?>

<?php tpl_display(get_template_path('form_errors')) ?>

  <div>
    <?php echo label_tag(lang('name'), 'categoryFormName', $canEdit) ?>
<?php if ($canEdit) { ?>
    <?php echo text_field('category[name]', array_var($category_data, 'name'), array('id' => 'categoryFormName', 'class' => 'title')) ?>
<?php } else { ?>
    <div class="header"><?php echo clean($category->getName()) ?></a></div>
<?php } // if?>
  </div>
  
  <div>
    <?php echo label_tag(lang('description'), 'categoryFormDescription') ?>
<?php if ($canEdit) { ?>
    <?php echo textarea_field('category[description]', array_var($category_data, 'description'), array('id' => 'categoryFormDescription', 'class' => 'short')) ?>
<?php } else { ?>
    <div class="desc"><?php echo clean($category->getDescription()) ?></a></div>
<?php } // if?>
  </div>

<?php if ($canEdit) { ?>
  <?php echo submit_button($category->isNew() ? lang('add tracCategory') : lang('edit tracCategory')) ?>
<?php } // if?>
<?php if(!$category->isNew() && $category->canDelete(logged_user())) { ?>
    <a href="<?php echo $category->getDeleteUrl() ?>" onclick="return confirm('<?php echo lang('confirm delete category') ?>')"><?php echo lang('delete') ?></a>
<?php } // if ?>
</form>