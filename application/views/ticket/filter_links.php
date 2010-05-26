<?php

echo '<a href="'.get_url('ticket', 'index', array_merge($params, array($property_name=> ''))).'" '.($params[$property_name] == "" ? 'class="selected"' : '').'>'.lang('all').'</a> ';

foreach ($properties as $property) {
  echo '<a href="'.get_url('ticket', 'index', array_merge($params, array($property_name=> $property))).'" '.(preg_match("/^(.*,)?$property(,.*)?$/", $params[$property_name]) ? 'class="selected"' : '').'>'.lang($property).'</a> ';

  if (preg_match("/^(.*,)?$property(,.*)?$/", $params[$property_name])) {
    echo '<a href="'.get_url('ticket', 'index', array_merge($params, array($property_name => preg_replace(array("/^$property,+/", "/,+$property,+/", "/,+$property$/", "/^$property$/"), array('', ',', '', ''), $params[$property_name])))).'">-</a> ';
  } else {
    echo '<a href="'.get_url('ticket', 'index', array_merge($params, array($property_name => ($params[$property_name] == "" ? $property : $params[$property_name].','.$property)))).'">+</a> ';
  }
}
?>