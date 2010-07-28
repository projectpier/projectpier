<?php

  set_page_title(lang('plugins'));
  administration_tabbed_navigation(ADMINISTRATION_TAB_PLUGINS);
  administration_crumbs(lang('plugins'));
  add_stylesheet_to_page('project/messages.css');
?>
<?php if (isset($plugins) && is_array($plugins) && count($plugins)) { ?>
<script language="JavaScript">
	function toggleVisibility(me){
		if (me.style.visibility=="hidden"){
			me.style.visibility="visible";
			}
		else {
			me.style.visibility="hidden";
			}
		}
</script>
<div id="plugins">
  <form action="<?php echo get_url('administration', 'update_plugins') ?>" method="post">
  <fieldset>
    <legend><?php echo lang('list of plugins') ?></legend>
  
  <?php tpl_display(get_template_path('form_errors')) ?>
  
<?php foreach ($plugins as $name=>$id) {  ?>
    
    <div class="objectOption">
      <div class="optionLabel"><label><?php echo ucwords(str_replace('_',' ',$name)) ?>:</label></div>
      <div class="optionControl">
	  <?php if ('-'==$id) { ?>
	  	<input id="<?php echo $name; ?>Yes" class="yes_no" value="1" type="radio" name="plugins[<?php echo $name; ?>]" /> <label class="yes_no" for="<?php echo $name; ?>Yes"><?php echo lang('activated'); ?> </label> 
	  	<input id="<?php echo $name; ?>No" class="yes_no" value="0" type="radio" checked="checked" name="plugins[<?php echo $name; ?>]" /> <label class="yes_no" for="<?php echo $name; ?>No"><?php echo lang('deactivated'); ?></label>
	  <?php } else { ?>
	    <input id="<?php echo $name; ?>Yes" onclick="javascript:toggleVisibility(document.getElementById('keep_data_<?php echo $name; ?>'))" checked="checked" class="yes_no" value="1" type="radio" name="plugins[<?php echo $name; ?>]" /> <label class="yes_no" for="<?php echo $name; ?>Yes"><?php echo lang('activated'); ?> </label> 
	    <input id="<?php echo $name; ?>No" onclick="javascript:toggleVisibility(document.getElementById('keep_data_<?php echo $name; ?>'))" class="yes_no" value="0" type="radio" name="plugins[<?php echo $name; ?>]" /> <label class="yes_no" for="<?php echo $name; ?>No"><?php echo lang('deactivated'); ?> </label>
	  <?php } ?>
      </div>
      
      	<div id="keep_data_<?php echo $name; ?>" style="visibility:hidden" class="optionControl">
      		<?php echo lang('what to do with data') ?><br /> 
      		<?php echo yes_no_widget('plugins['.$name.'_data]', $name.'_data', true, lang('keep data'), lang('delete data')) ?> 
      	</div>
      
    </div>
    
<?php } ?>
    <p><?php echo submit_button(lang('update plugins')) ?></p>
  </fieldset>
  </form>
</div>

<?php } else { ?>

<p><?php echo lang('no plugins found') ?></p>

<?php } // if ?>
<!-- 
<p>DEMO NOTES ONLY - To be removed</p>
<p>Features of the demo Project Links plugin<br />&nbsp;&nbsp;- Adds new menu tab in project view
<br />&nbsp;&nbsp;- Adds new option to 'Add link' in project overview page
<br />&nbsp;&nbsp;- Allows admin/owner company member to add to list of 'hyperlinks' to project
<br />&nbsp;&nbsp;- Removes three project menu tabs; Forms, Tags and People
<br />&nbsp;&nbsp;- On activate; adds new table to DB
<br />&nbsp;&nbsp;- On de-activate; removes table from DB</p>
<p>Anatomy of the demo Project Links plugin (<b>bold</b> represents plugin files).</p>
<ul>
	<li>application
<ul>
	<li>controllers
<ul>
	<li><b>ProjectLinkController.php</b></li>
</ul>
</li>
	<li>models
<ul>
	<li><b>project_links
</b>
<ul>
	<li><b>base</b>
<ul>
	<li><b>BaseProjectLinks.class.php</b></li>
	<li><b>BaseProjectLink.class.php</b></li>
</ul>
</li>
	<li><b>ProjectLinks.class.php</b></li>
	<li><b>ProjectLink.class.php</b></li>
</ul>
</li>
</ul>
</li>
	<li>plugins
<ul>
	<li><b>plugin.project_links.php</b></li>
</ul>
</li>
	<li>views
<ul>
	<li><b>links</b>
<ul>
	<li><b>index.php</b></li>
	<li><b>edit_link.php</b></li>
</ul>
</li>
</ul>
</li>
</ul>
</li>
	<li>language
<ul>
	<li>en_us
<ul>
	<li><b>project_links.php</b></li>
</ul>
</li>
</ul>
</li>
</ul>
 -->
