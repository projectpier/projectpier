<?php

  /**
  * TicketChangeset class
  *
  * @http://www.projectpier.org/
  */
  class TicketChangeset extends BaseTicketChangeset {
    
    /**
    * Ticket
    *
    * @var ProjectTicket
    */
    private $ticket;
    
    /**
    * Cached changes array
    *
    * @var array
    */
    private $changes;
    
    /**
    * Return ticket object
    *
    * @param void
    * @return ProjectTicket
    */
    function getTicket() {
      if (is_null($this->ticket)) {
        $this->ticket = ProjectTickets::findById($this->getTicketId());
      }
      return $this->ticket;
    } // getTicket
    
    /**
    * Return changes array for this changeset
    *
    * @access public
    * @param void
    * @return TicketChanges
    */
    function getChanges() {
      if (is_null($this->changes)) {
        $this->changes = TicketChanges::findAll(array(
          'conditions' => '`changeset_id` = ' . DB::escape($this->getId()),
          'order' => '`id`'));
      }
      return $this->changes;
    } // getTicket
    
    /**
    * Checks if change set is empty (i.e. no comments, no changes)
    * 
    * @param void
    * @return boolean
    */
    function isEmpty($value='') {
      return $this->getComment() == "" && count($this->getChanges()) == 0;
    } // isEmpty
    
  } // TicketChangeset

?>