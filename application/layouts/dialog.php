<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
  <head>
    <title><?php echo get_page_title() ?></title>
<?php echo stylesheet_tag('dialog.css') ?> 
<?php echo meta_tag('content-type', 'text/html; charset=utf-8', true) ?> 
<link rel="Shortcut Icon" href="favicon.ico" type="image/x-icon" />
<?php echo render_page_head() ?>
  </head>
  <body>
    <div id="dialog">
      <h1><?php echo get_page_title() ?></h1>
<?php if (!is_null(flash_get('success'))) { ?>
          <div id="success" onclick="this.style.display = 'none'"><?php echo clean(flash_get('success')) ?></div>
<?php } ?>
<?php if (!is_null(flash_get('error'))) { ?>
          <div id="error" onclick="this.style.display = 'none'"><?php echo clean(flash_get('error')) ?></div>
<?php } ?>
<?php echo $content_for_layout ?>
    </div>
  </body>
</html>
