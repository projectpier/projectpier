<?php

 /**
  * PluginManager
  *
  * This file contains logic that has been borrowed from my favorite
  * publishing platform; WordPress
  * The implementation is only a part reproduction and has been modified
  * to suit the ProjectPier application.
  * @author Mark Brennand
  * @link http://www.activeingredient.com.au
  *
  * The plugin architecture supports both actions and filters. The difference
  * between these is a matter of input; all actions on the same hook receive
  * the same input regardless of order, filters received modified input from
  * previous filters on the same hook.
  * 
  * @see application/plugins.php
  *  
  * @version 1.0
  * @http://www.projectpier.org/
  */
  class PluginManager {
    
    /**
    * List of filters and actions
    *
    * @var array
    */
    var $filter_table;
    
    function init() {
      if (isset($this) && ($this instanceof PluginManager)) {
        $this->filter_table= array();
        $activated_plugins = Plugins::getActivatedPlugins();
        
        // Load each plugin
        foreach (array_keys($activated_plugins) as $name) {
          include_once 'plugins/plugin.'.$name.'.php';
        }
        
        // TODO : cleanup up old activated plugins without valid file??
        
      } else {
        PluginManager::instance()->init();
      } // if
    } // init
    
    function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
      if (isset($this->filter_table[$tag][$priority])) {
        foreach ($this->filter_table[$tag][$priority] as $filter) {
          if ($filter['function'] == $function_to_add) {
            return false;
          } // if
        } // foreach
      } // if
      $this->filter_table[$tag][$priority][] = array(
                                               'function'=>$function_to_add,
                                               'accepted_args'=>$accepted_args);
      return true;
    } // add_filter
    
    function remove_filter($tag, $function_to_remove, $priority = 10) {
      $toret = false;
    
      if (isset($this->filter_table[$tag][$priority])) {
        foreach ($this->filter_table[$tag][$priority] as $filter) {
          if ($filter['function'] != $function_to_remove) {
            $new_function_list[] = $filter;
          } else {
            $toret = true;
          } // if
        } // foreach
        $this->filter_table[$tag][$priority] = $new_function_list;
      } // if
      return $toret;
    } // remove_filter
    
    function do_action($tag, $arg='') {
      if (!isset($this->filter_table[$tag])) {
        return;
      } else {
        ksort($this->filter_table[$tag]);
      } // if
      
      $args = array();
      if (is_array($arg) && 1 == count($arg) && is_object($arg[0])) {
        $args[] =& $arg[0];
      } else {
        $args[] = $arg;
      } // if
      for ($a = 2; $a < func_num_args(); $a++) {
        $args[] = func_get_arg($a);
      } // for
      
      foreach ($this->filter_table[$tag] as $priority => $functions) {
        if (!is_null($functions)) {
          foreach ($functions as $f) {
            call_user_func_array($f['function'], array_slice($args, 0, (int)$f['accepted_args']));
          } // foreach
        } // if
      } // foreach
    } // do_action

    function apply_filters($tag,$value) {
      $args = func_get_args();
    
      if (!isset($this->filter_table[$tag]) ) {
        return $value;
      } else {
        ksort($this->filter_table[$tag]);
      } // if
      
      foreach ($this->filter_table[$tag] as $priority => $functions) {
        if (!is_null($functions)) {
          foreach ($functions as $f) {
            $args[1] = $value;
            $value = call_user_func_array($f['function'], array_slice($args, 1,(int)$f['accepted_args']));
          } // foreach
        } // if
      } // foreach
      return $value;
    } // apply_filters
    
    /**
    * Return single PluginManager instance
    *
    * @access public
    * @param void
    * @return PluginManager 
    */
    static function instance() {
      static $instance;
      if (!($instance instanceof PluginManager )) {
        $instance = new PluginManager();
      } // if
      return $instance;
    } // instance
    
  } // PluginManager
?>
