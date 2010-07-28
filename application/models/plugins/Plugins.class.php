<?php

  /**
  * Plugins
  *
  * @http://www.projectpier.org/
  */
  class Plugins extends BasePlugins {
  
    /**
    * Return array of all plugins based on plugin files on filesystem
    *
    * @param none
    * @return array
    */
    static function getAllPlugins() {
      $results = array();
      $dir = APPLICATION_PATH.'/plugins';
      $files = get_files($dir, array('php'), true);
      // reformat by extracting names
      foreach ((array)$files as $file) {
        if (preg_match('/plugin.(.*).php/', $file, $matches)) {
          $results[$matches[1]] = '-';
        } // if
      } // foreach
      
      // now sort by name
      ksort($results);
      // now get activated plugins
      $conditions = array('`installed` = 1');
      $plugins = Plugins::findAll(array(
        'conditions' => $conditions
        ));
      foreach ((array)$plugins as $plugin) {
        if (array_key_exists($plugin->getName(),$results)) {
          $results[$plugin->getName()] = $plugin->getId();
        } else {
          // TODO : remove from DB here??
        } // if
      } // foreach

      return $results;
    } // getAllPlugins
    
    /**
    * Return array of all activated plugins based on plugin files on filesystem
    *
    * @param none
    * @return array
    */
    static function getActivatedPlugins() {
      
      $results = Plugins::getAllPlugins();
      
      foreach ($results as $name => $id) {
        if ('-' == $id) {
          unset($results[$name]);
        } // if
      } // foreach
      
      return $results;
    } // getActivatedPlugins
    
    /**
    * Return array of all activated plugins
    *
    * @param none
    * @return array
    */
    static function getNamesFromDB() {
      $names = array();
      $plugins = Plugins::findAll(array()); // findAll
      if (is_array($plugins)) {
        foreach ($plugins as $plugin) {
          $names[] = $plugin->getName();
        } // foreach
      } // if
      return $names;
    } // getActivatedPlugins
    
  } // Plugins 

?>
