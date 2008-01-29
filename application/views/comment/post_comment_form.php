<fieldset>
  <legend><?php echo lang('add comment') ?></legend>

<form action="<?php echo Comment::getAddUrl($comment_form_object) ?>" method="post" enctype="multipart/form-data">
<?php tpl_display(get_template_path('form_errors')) ?>

<?php if ($comment_form_object->columnExists('comments_enabled') && !$comment_form_object->getCommentsEnabled() && logged_user()->isAdministrator()) { ?>
  <p class="error"><?php echo lang('admin notice comments disabled') ?></p>
<?php } // if ?>

  <div class="formAddCommentText">
    <?php echo label_tag(lang('text'), 'addCommentText', true) ?>
    <?php echo textarea_field("comment[text]", '', array('class' => 'comment', 'id' => 'addCommentText')) ?>
  </div>
    
<?php if (logged_user()->isMemberOfOwnerCompany()) { ?>
  <fieldset>
    <legend><?php echo lang('options') ?></legend>
    
    <div class="objectOption">
      <div class="optionLabel"><label><?php echo lang('private comment') ?>:</label></div>
      <div class="optionControl"><?php echo yes_no_widget('comment[is_private]', 'addCommentIsPrivate', false, lang('yes'), lang('no')) ?></div>
      <div class="optionDesc"><?php echo lang('private comment desc') ?></div>
    </div>
  </fieldset>
<?php } // if ?>

<?php if ($comment_form_comment->canAttachFile(logged_user(), active_project())) { ?>
  <?php echo render_attach_files() ?>
<?php } // if ?>
    
  <?php echo submit_button(lang('add comment')) ?>
</form>
</fieldset>
