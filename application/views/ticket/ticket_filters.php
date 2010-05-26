<h4><a href="#" onclick="var s=document.getElementById('ticketsFiltersContent'); s.style.display = (s.style.display=='none'?'block':'none');"><?php echo lang('filters') ?></a></h4>
<div id="ticketsFiltersContent" <?php if (!$filtered) { echo "style='display:none'";} ?>>
  <div id="statusFilters">
    <strong><?php echo lang('status'); ?>:</strong>
    <?php
    $this->assign('properties', get_ticket_statuses());
    $this->assign('property_name', 'status');
    $this->includeTemplate(get_template_path('filter_links', 'ticket'));
    ?>
  </div>
  <div id="priorityFilters">
    <strong><?php echo lang('priority'); ?>:</strong>
    <?php
    $this->assign('properties', get_ticket_priorities());
    $this->assign('property_name', 'priority');
    $this->includeTemplate(get_template_path('filter_links', 'ticket'));
    ?>
  </div>
  <div id="typeFilters">
    <strong><?php echo lang('type'); ?>:</strong>
    <?php
    $this->assign('properties', get_ticket_types());
    $this->assign('property_name', 'type');
    $this->includeTemplate(get_template_path('filter_links', 'ticket'));
    ?>
  </div>
  <div id="categoryFilters">
    <strong><?php echo lang('category'); ?>:</strong>
    <?php
    $categories = Categories::getProjectCategories(active_project());
    $property_name = 'category_id';
    
    // TODO make filter_links template more flexible so that it can be used with Categories and not only text.
    echo '<a href="'.get_url('ticket', 'index', array_merge($params, array($property_name=> ''))).'" '.($params[$property_name] == "" ? 'class="selected"' : '').'>'.lang('all').'</a> ';

    foreach ($categories as $category) {
      $category_id = $category->getId();
      echo '<a href="'.get_url('ticket', 'index', array_merge($params, array($property_name=> $category->getId()))).'" '.(preg_match("/^(.*,)?$category_id(,.*)?$/", $params[$property_name]) ? 'class="selected"' : '').'>'.$category->getName().'</a> ';
      if (preg_match("/^(.*,)?$category_id(,.*)?$/", $params[$property_name])) {
        echo '<a href="'.get_url('ticket', 'index', array_merge($params, array($property_name => preg_replace(array("/^$category,/", "/,$category,/", "/,$category$/","/^$category$/"), array('', ',', '', ''), $params[$property_name])))).'">-</a> ';
      } else {
        echo '<a href="'.get_url('ticket', 'index', array_merge($params, array($property_name => ($params[$property_name] == "" ? $category_id : $params[$property_name].','.$category_id)))).'">+</a> ';
      }
    }
    ?>
  </div>
</div><!-- // ticketsFiltersContent -->