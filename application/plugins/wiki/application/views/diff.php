<?php

/**
 * @author Alex Mayhew
 * @copyright 2008
 */

set_page_title(lang('wiki compare rev1 rev2', $rev1->getRevision(), $rev2->getRevision()));
project_tabbed_navigation(PROJECT_TAB_WIKI);
project_crumbs(array(
  array(lang('wiki'), get_url('wiki')),
  array($revision->getName(), $page->getViewUrl()),
  array(lang('wiki page diff')))
);


$css = <<< CSSSNP
.ins {
  background-color: #ddffdd;
  color: #008800;
  text-decoration: underline;
}

.del {
  background-color: #ffeeee;
  color: #bb0000;
  text-decoration: line-through;
}
CSSSNP;
	
add_inline_css_to_page($css);
 
?>


<?php echo nl2br($diff); ?>

<p>&nbsp;</p>
<table>
<tr><th colspan="2"><a href="<?php echo $rev1->getViewUrl() ?>"><?php echo lang('wiki page revision no', $rev1->getRevision()) ?></a></th></tr>
<tr><td><?php echo lang('wiki page revision title')?>:</td><td><?php echo $rev1->getName() ?></td></tr>
<tr><td><?php echo lang('wiki page revision author')?>:</td><td><a href="<?php echo $rev1->getCreatedBy()->getCardUrl() ?>"><?php echo $rev1->getCreatedBy()->getUsername() ?></a></td></tr>
<tr><td><?php echo lang('wiki page revision created')?>:</td><td><?php echo format_date($rev1->getCreatedOn()) ?></td></tr>
<tr><td><?php echo lang('wiki page revision log message')?>:</td><td><?php echo $rev1->getLogMessage() ?></td></tr>
</table>
<p>&nbsp;</p>
<table>
<tr><th colspan="2"><a href="<?php echo $rev2->getViewUrl()?>"><?php echo lang('wiki page revision no', $rev2->getRevision()) ?></a></th></tr>
<tr><td><?php echo lang('wiki page revision title')?>:</td><td><?php echo $rev2->getName() ?></td></tr>
<tr><td><?php echo lang('wiki page revision author')?>:</td><td><a href="<?php echo $rev2->getCreatedBy()->getCardUrl()?>"><?php echo $rev2->getCreatedBy()->getUsername() ?></a></td></tr>
<tr><td><?php echo lang('wiki page revision created')?>:</td><td><?php echo format_date($rev2->getCreatedOn()) ?></td></tr>
<tr><td><?php echo lang('wiki page revision log message')?>:</td><td><?php echo $rev2->getLogMessage() ?></td></tr>
</table>



