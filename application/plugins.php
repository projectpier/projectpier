<?php

  /**
  * PluginManager loader
  *
  * This file contains logic that has been borrowed from my favorite
  *  publishing platform; WordPress
  * The implementation is only a part reproduction and has been modified
  *  to suit the ProjectPier application.
  * @author Mark Brennand
  * @link http://www.activeingredient.com.au
  *
  * The plugin architecture supports both actions and filters. The difference
  * between these is a matter of input; all actions on the same hook receive
  * the same input regardless of order, filters received modified input from
  * previous filters on the same hook.
  * 
  * @see application/models/PluginManager.class.php
  *  
  * @version 1.0
  * @http://www.projectpier.org/
  */

  // find the plugin manager
  include_once 'models/PluginManager.class.php';
  // init the plugin manager
  PluginManager::instance()->init();
  
  /*
  * Convenience function for instance of PluginManager used by hooks throughout
  */
  function plugin_manager() {
    return PluginManager::instance();
  } // plugin_manager
  
  /*
  * Convenience functions for plugin writers
  */
  function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
    return plugin_manager()->add_filter($tag, $function_to_add, $priority, $accepted_args);
  } // add_action
  
  function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
    return plugin_manager()->add_filter($tag, $function_to_add, $priority, $accepted_args);
  } // add_filter
  
  function remove_action($tag, $function_to_remove, $priority = 10) {
    return plugin_manager()->remove_filter($tag, $function_to_remove, $priority);
  } // remove_action
  
  function remove_filter($tag, $function_to_remove, $priority = 10) {
    return plugin_manager()->remove_filter($tag, $function_to_remove, $priority);
  } // remove_filter
  
?>
