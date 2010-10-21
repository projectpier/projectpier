<?php

  /**
  * TicketChange class
  * Generated on Sat, 04 Mar 2006 12:50:11 +0100 by DataObject generation tool
  *
  * @http://www.projectpier.org/
  */
  class TicketChange extends BaseTicketChange {
    
    /**
    * Ticket
    *
    * @var ProjectTicket
    */
    private $ticket;
    
    /**
    * Return ticket object
    *
    * @param void
    * @return ProjectTicket
    */
    function getTicket() {
      if(is_null($this->ticket)) $this->ticket = ProjectTickets::findById($this->getTicketId());
      return $this->ticket;
    } // getTicket
    
    /**
    * Return if data needs translation
    *
    * @param void
    * @return ProjectTicket
    */
    function dataNeedsTranslation() {
      return ($this->getType() == 'priority') || ($this->getType() == 'type') || ($this->getType() == 'status') || ($this->getType() == 'private');
    } // dataNeedsTranslation
  
  } // TicketChanges 

?>