<?php

  /**
  * BaseTicketChangeset class
  *
  * @http://www.projectpier.org/
  */
  abstract class BaseTicketChangeset extends ApplicationDataObject {
  
    // -------------------------------------------------------
    //  Access methods
    // -------------------------------------------------------
  
    /**
    * Return value of 'id' field
    *
    * @access public
    * @param void
    * @return integer 
    */
    function getId() {
      return $this->getColumnValue('id');
    } // getId()

    /**
    * Return value of 'ticket_id' field
    *
    * @access public
    * @param void
    * @return integer 
    */
    function getTicketId() {
      return $this->getColumnValue('ticket_id');
    } // getTicketId()
    
    /**
    * Set value of 'ticket_id' field
    *
    * @access public   
    * @param integer $value
    * @return boolean
    */
    function setTicketId($value) {
      return $this->setColumnValue('ticket_id', $value);
    } // setTicketId()
    
    /**
    * Return value of 'comment' field
    *
    * @access public
    * @param void
    * @return string 
    */
    function getComment() {
      return $this->getColumnValue('comment');
    } // getComment()
    
    /**
    * Set value of 'comment' field
    *
    * @access public   
    * @param string $value
    * @return boolean
    */
    function setComment($value) {
      return $this->setColumnValue('comment', $value);
    } // setComment() 
    
    /**
    * Return value of 'created_on' field
    *
    * @access public
    * @param void
    * @return DateTimeValue 
    */
    function getCreatedOn() {
      return $this->getColumnValue('created_on');
    } // getCreatedOn()
    
    /**
    * Set value of 'created_on' field
    *
    * @access public   
    * @param DateTimeValue $value
    * @return boolean
    */
    function setCreatedOn($value) {
      return $this->setColumnValue('created_on', $value);
    } // setCreatedOn() 
    
    /**
    * Return value of 'created_by_id' field
    *
    * @access public
    * @param void
    * @return integer 
    */
    function getCreatedById() {
      return $this->getColumnValue('created_by_id');
    } // getCreatedById()
    
    /**
    * Set value of 'created_by_id' field
    *
    * @access public   
    * @param integer $value
    * @return boolean
    */
    function setCreatedById($value) {
      return $this->setColumnValue('created_by_id', $value);
    } // setCreatedById() 
    
    
    /**
    * Return manager instance
    *
    * @access protected
    * @param void
    * @return TicketChangesets
    */
    function manager() {
      if (!($this->manager instanceof TicketChangesets)) {
        $this->manager = TicketChangesets::instance();
      }
      return $this->manager;
    } // manager
  
  } // BaseTicketChangeset

?>