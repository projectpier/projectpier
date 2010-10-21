<?php

  /**
  * Contacts
  *
  * @http://www.projectpier.org/
  */
  class Contacts extends BaseContacts {
    
    /**
    * Return all contacts
    *
    * @param void
    * @return array
    */
    function getAll() {
      return self::findAll();
    } // getAll
    
    /**
    * Return contacts grouped by company
    *
    * @param void
    * @return array
    */
    static function getGroupedByCompany() {
      $companies = Companies::getAll();
      if (!is_array($companies) || !count($companies)) {
        return null;
      } // if
      
      $result = array();
      foreach ($companies as $company) {
        $contacts = $company->getContacts();
        if (is_array($contacts) && count($contacts)) {
          $result[$company->getName()] = array(
            'details' => $company,
            'contacts' => $contacts,
          ); // array
        } // if
      } // foreach
      
      return count($result) ? $result : null;
    } // getGroupedByCompany
    
  } // Contacts 

?>
