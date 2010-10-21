<?php

  /**
  * Categories, generated on Wed, 19 Jul 2006 22:17:32 +0200 by 
  * DataObject generation tool
  *
  * @http://www.projectpier.org/
  */
  class Categories extends BaseCategories {
    
    /**
    * Return tickets that belong to specific project
    *
    * @param Project $project
    * @param boolean $include_private Include private tickets in the result
    * @return array
    */
    static function getProjectCategories(Project $project) {
      return self::findAll(array(
        'conditions' => array('`project_id` = ?', $project->getId()),
        'order' => '`name`',
      )); // findAll
    } // getProjectCategories
    
  } // Categories 

?>