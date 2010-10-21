<?php

/**
 * @author Alex Mayhew
 * @copyright 2008
 */

class BaseRevision extends ApplicationDataObject {

  /**
  * Taken from Project links plugin
  *
  * @param mixed $method
  * @param mixed $args
  * @return
  */
  function __call($method, $args) {
    // If we have a getter or setter call
    if (preg_match('/(set|get)(_)?/', $method, $type)) {
      // Get the name of the column it wants to get / set
      $col = substr(strtolower(preg_replace('([A-Z])', '_$0', $method)), 4);

      if ($type[1] == 'get') {
        // If we want to get a value
        return $this->getColumnValue($col);

      } elseif ($type[1] == 'set' && count($args)) {
        // Else if we want to set a value, and we have an argument
        return $this->setColumnValue($col, $args[0]);
      } // if
    } // if
    
    return false;
  } // __call

  /**
  * Return instance of manager
  * 
  * @return
  */
  function manager() {
    return Revisions::instance();
  } // manager

}

?>
