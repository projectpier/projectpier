<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
  <head>
<?php if (active_project() instanceof Project) { ?>
    <title><?php echo clean(active_project()->getName()) ?> - <?php echo get_page_title() ?> @ <?php echo clean(owner_company()->getName()) ?></title>
<?php } else { ?>
    <title><?php echo get_page_title() ?> @ <?php echo clean(owner_company()->getName()) ?></title>
<?php } // if ?>
    
<?php echo stylesheet_tag('project_website.css') ?> 
<?php echo meta_tag('content-type', 'text/html; charset=utf-8', true) ?> 
<link rel="Shortcut Icon" href="favicon.ico" type="image/x-icon" />
<?php echo add_javascript_to_page('yui/yahoo/yahoo-min.js') ?>
<?php echo add_javascript_to_page('yui/dom/dom-min.js') ?>
<?php echo add_javascript_to_page('yui/event/event-min.js') ?>
<?php echo add_javascript_to_page('yui/animation/animation-min.js') ?>
<?php echo add_javascript_to_page('app.js') ?>
<?php echo use_widget('UserBoxMenu') ?>
<?php echo render_page_head() ?>
  </head>
  <body>
<?php echo render_system_notices(logged_user()) ?>
    <div id="wrapper">
    
      <!-- header -->
      <div id="headerWrapper">
        <div id="header">
          <h1><a href="<?php echo active_project()->getOverviewUrl() ?>"><?php echo clean(active_project()->getName()) ?></a></h1>
          <div id="userboxWrapper"><?php echo render_user_box(logged_user()) ?></div>
        </div>
      </div>
      <!-- /header -->
      
      <div id="tabsWrapper">
        <div id="tabs">
<?php if (is_array(tabbed_navigation_items())) { ?>
          <ul>
<?php foreach (tabbed_navigation_items() as $tabbed_navigation_item) { ?>
            <li id="tabbed_navigation_item_<?php echo $tabbed_navigation_item->getID() ?>" <?php if ($tabbed_navigation_item->getSelected()) { ?> class="active" <?php } ?>><a href="<?php echo $tabbed_navigation_item->getUrl() ?>"><?php echo clean($tabbed_navigation_item->getTitle()) ?></a></li>
<?php } // foreach ?>
          </ul>
<?php } // if ?>
        </div>
      </div>
      
      <div id="crumbsWrapper">
        <div id="crumbsBlock">
          <div id="crumbs">
<?php if (is_array(bread_crumbs())) { ?>
            <ul>
<?php foreach (bread_crumbs() as $bread_crumb) { ?>
<?php if ($bread_crumb->getUrl()) { ?>
              <li>&raquo; <a href="<?php echo $bread_crumb->getUrl() ?>"><?php echo clean($bread_crumb->getTitle()) ?></a></li>
<?php } else {?>
              <li>&raquo; <span><?php echo clean($bread_crumb->getTitle()) ?></span></li>
<?php } // if {?>
<?php } // foreach ?>
            </ul>
<?php } // if ?>
          </div>
          <div id="searchBox">
            <form action="<?php echo active_project()->getSearchUrl() ?>" method="get">
              <div>
<?php
  $search_field_default_value = lang('search') . '...';
  $search_field_attrs = array(
    'onfocus' => 'if (value == \'' . $search_field_default_value . '\') value = \'\'',
    'onblur' => 'if (value == \'\') value = \'' . $search_field_default_value . '\'');
?>
                <?php echo input_field('search_for', array_var($_GET, 'search_for', $search_field_default_value), $search_field_attrs) ?><button type="submit"><?php echo lang('search button caption') ?></button>
                <input type="hidden" name="c" value="project" />
                <input type="hidden" name="a" value="search" />
                <input type="hidden" name="active_project" value="<?php echo active_project()->getId() ?>" />
              </div>
            </form>
          </div>
        </div>
      </div>
      
      <!-- content wrapper -->
      <div id="outerContentWrapper">
        <div id="innerContentWrapper">
<?php if (!is_null(flash_get('success'))) { ?>
          <div id="success" onclick="this.style.display = 'none'"><?php echo clean(flash_get('success')) ?></div>
<?php } ?>
<?php if (!is_null(flash_get('error'))) { ?>
          <div id="error" onclick="this.style.display = 'none'"><?php echo clean(flash_get('error')) ?></div>
<?php } ?>

          <h1 id="pageTitle"><?php echo get_page_title() ?></h1>
          <div id="pageContent">
            <div id="content">
<?php if (is_array(page_actions())) { ?>
            <div id="page_actions">
              <ul>
<?php foreach (page_actions() as $page_action) { ?>
                <li><a href="<?php echo $page_action->getURL() ?>"><?php echo clean($page_action->getTitle()) ?></a></li>
<?php } // foreach ?>
              </ul>
            </div>
<?php } // if ?>
              <!-- Content -->
              <?php echo $content_for_layout ?>
              <!-- /Content -->
            </div>
<?php if (isset($content_for_sidebar)) { ?>
            <div id="sidebar"><?php echo $content_for_sidebar ?></div>
<?php } // if ?>
            <div class="clear"></div>
          </div>
        </div>
        
        <!--Footer -->
        <div id="footer">
          <div id="copy">
<?php if (is_valid_url($owner_company_homepage = owner_company()->getHomepage())) { ?>
            <?php echo lang('footer copy with homepage', date('Y'), $owner_company_homepage, clean(owner_company()->getName())) ?>
<?php } else { ?>
            <?php echo lang('footer copy without homepage', date('Y'), clean(owner_company()->getName())) ?>
<?php } // if ?>
          </div>
          <div id="productSignature"><?php echo product_signature() ?></div>
        </div>
      </div>
      <!-- /content wrapper -->
      
    </div>
  </body>
</html>
