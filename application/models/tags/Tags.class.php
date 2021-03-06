<?php

  /**
  * Tags, generated on Wed, 05 Apr 2006 06:44:54 +0200 by 
  * DataObject generation tool
  *
  * @http://www.projectpier.org/
  */
  class Tags extends BaseTags {
  
    /**
    * Return tags for specific object
    *
    * @access public
    * @param ApplicationDataObject $object
    * @param string $manager_class
    * @return array
    */
    function getTagsByObject(ApplicationDataObject $object, $manager_class) {
      return self::findAll(array(
        'conditions' => array('`rel_object_id` = ? AND `rel_object_manager` = ?', $object->getObjectId(), get_class($object->manager())),
        'order' => '`tag`'
      )); // findAll
    } // getTagsByObject
    
    /**
    * Return tag names as array for specific object
    *
    * @access public
    * @param ApplicationDataObject $object
    * @param string $manager_class
    * @return array
    */
    function getTagNamesByObject(ApplicationDataObject $object, $manager_class) {
      $rows = DB::executeAll('SELECT `tag` FROM ' .  self::instance()->getTableName(true) . ' WHERE `rel_object_id` = ? AND `rel_object_manager` = ? ORDER BY `tag`', $object->getId(), $manager_class);
      
      if (!is_array($rows)) {
        return null;
      }
      
      $tags = array();
      foreach ($rows as $row) {
        $tags[] = $row['tag'];
      }
      return $tags;
    } // getTagNamesByObject
    
    /**
    * Clear tags of specific object
    *
    * @access public
    * @param ApplicationDataObject $object
    * @param string $manager_class
    * @return boolean
    */
    function clearObjectTags(ApplicationDataObject $object, $manager_class) {
      $tags = $object->getTags(); // save the tags list
      if (is_array($tags)) {
        foreach ($tags as $tag) {
          $tag->delete();
        }
      } // if
    } // clearObjectTags
    
    /**
    * Set tags for specific object
    *
    * @access public
    * @param array $tags Array of tags... Can be NULL or empty
    * @param ApplicationDataObject $object
    * @param string $manager_class
    * @param Project $project
    * @return null
    */
    function setObjectTags($tags, ApplicationDataObject $object, $manager_class, $project = null) {
      self::clearObjectTags($object, $manager_class);
      if (is_array($tags) && count($tags)) {
        $tags = array_unique($tags);
        foreach ($tags as $tag_name) {
          
          if (trim($tag_name) <> '') {
            $tag = new Tag();
            
            if ($project instanceof Project) {
              $tag->setProjectId($project->getId());
            } else {
              $tag->setProjectId(0);
            } // if
            $tag->setTag($tag_name);
            $tag->setRelObjectId($object->getId());
            $tag->setRelObjectManager($manager_class);
            $tag->setIsPrivate($object->isPrivate());
            
            $tag->save();
          } // if
          
        } // foreach
      } // if
      return true;
    } // setObjectTags
    
    /**
    * Return unique tag names used on project objects
    *
    * @access public
    * @param Project $project
    * @return array
    */
    function getProjectTagNames(Project $project, $exclude_private = false) {
      if ($exclude_private) {
        $rows = DB::executeAll("SELECT DISTINCT `tag` FROM " . self::instance()->getTableName(true) . ' WHERE `project_id` = ? AND `is_private` = ? ORDER BY `tag`', $project->getId(), 0);
      } else {
        $rows = DB::executeAll("SELECT DISTINCT `tag` FROM " . self::instance()->getTableName(true) . ' WHERE `project_id` = ? ORDER BY `tag`', $project->getId());
      } // if
      if (!is_array($rows) || !count($rows)) {
        return null;
      }
      
      $tags = array();
      foreach ($rows as $row) {
        $tags[] = $row['tag'];
      } // foreach
      
      return $tags;
    } // getProjectTagNames
    
    /**
    * Return unique tag names used for certain classes of objects
    *
    * @access public
    * @param array $class_name
    * @return array
    */
    function getClassTagNames($class_names, $exclude_private = false) {
      if ($exclude_private) {
        $rows = DB::executeAll("SELECT DISTINCT `tag` FROM " . self::instance()->getTableName(true) . ' WHERE `rel_object_manager` IN (?) AND `is_private` = ? ORDER BY `tag`', $class_names, 0);
      } else {
        $rows = DB::executeAll("SELECT DISTINCT `tag` FROM " . self::instance()->getTableName(true) . ' WHERE `rel_object_manager` IN (?) ORDER BY `tag`', $class_names);
      } // if
      if (!is_array($rows) || !count($rows)) {
        return null;
      } // if
      
      $tags = array();
      foreach ($rows as $row) {
        $tags[] = $row['tag'];
      } // foreach
      
      return $tags;
    } // getClassTagNames
    
    /**
    * Return array of project objects. Optional filters are by project, by tag and / or by object class
    *
    * @access public
    * @param Project $project
    * @param string $tag Return objects that are tagged with specific tag
    * @param string $class Return only object that match specific class (manager class name)
    * @param boolean $exclude_private Exclude private objects from listing
    * @return array
    */
    function getTaggedObjects(Project $project = null, $tag = null, $class = null, $exclude_private = false) {
      $conditions_array = array();
      if ($project instanceof Project) {
        $conditions_array[] = '`project_id` = ' . DB::escape($project->getId());
      } // if
      if (trim($tag) <> '') {
        $conditions_array[] = '`tag` = ' . DB::escape($tag);
      } // if 
      if (trim($class) <> '') {
        $conditions_array[] = '`rel_object_manager` = ' .  DB::escape($class);
      } // if
      if ($exclude_private) {
        $conditions_array[] = '`is_private` = ' . DB::escape(0);
      } // if
      
      $conditions = implode(" AND ", $conditions_array);
      
      $tags = self::findAll(array(
        'conditions' => $conditions,
        'order_by' => '`created_on`'
      )); // findById
      
      if (!is_array($tags)) {
        return null;
      } // if
      
      $objects = array();
      foreach ($tags as $tag_object) {
        $object = $tag_object->getObject();
        if ($object instanceof ApplicationDataObject) {
          $objects[] = $object;
        } // if
      } // foreach
      
      return count($objects) ? $objects : null;
      
    } // getTaggedObjects
    
    /**
    * Returns number of objects tagged with specific tag
    *
    * @access public
    * @param string $tag Tag name
    * @param Project $project Only objects that belong to this project
    * @param boolean $exclude_private Exclude private objects from listing
    * @return integer
    */
    function countProjectObjectsByTag($tag, Project $project, $exclude_private = false) {
      if ($exclude_private) {
        $row = DB::executeOne("SELECT COUNT(`id`) AS 'row_count' FROM " . self::instance()->getTableName(true) . " WHERE `tag` = ? AND `project_id` = ? AND `is_private` = ?", $tag, $project->getId(), 0);
      } else {
        $row = DB::executeOne("SELECT COUNT(`id`) AS 'row_count' FROM " . self::instance()->getTableName(true) . " WHERE `tag` = ? AND `project_id` = ?", $tag, $project->getId());
      } // if
      return array_var($row, 'row_count', 0);
    } // countProjectObjectsByTag
    
  } // Tags 

?>
