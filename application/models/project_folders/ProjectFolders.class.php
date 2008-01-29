<?php

  /**
  * ProjectFolders, generated on Tue, 04 Jul 2006 06:46:08 +0200 by 
  * DataObject generation tool
  *
  * @http://www.projectpier.org/
  */
  class ProjectFolders extends BaseProjectFolders {
  
    /**
    * Return array of project folders
    *
    * @param Project $project
    * @return array
    */
    static function getProjectFolders(Project $project) {
      return self::findAll(array(
        'conditions' => array('`project_id` = ?', $project->getId()),
        'order' => '`name`'
      )); // findAll
    } // getProjectFolders
    
  } // ProjectFolders 

?>
