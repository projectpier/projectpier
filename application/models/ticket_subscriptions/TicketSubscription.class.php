<?php

  /**
  * TicketSubscription class
  * Generated on Mon, 29 May 2006 03:51:15 +0200 by DataObject generation tool
  *
  * @http://www.projectpier.org/
  */
  class TicketSubscription extends BaseTicketSubscription {
  
    /**
    * User who is subscribed to this ticket
    *
    * @var User
    */
    private $user;
    
    /**
    * Ticket
    *
    * @var ProjectTicket
    */
    private $ticket;
    
    /**
    * Return user object
    *
    * @param void
    * @return User
    */
    function getUser() {
      if(is_null($this->user)) $this->user = Users::findById($this->getUserId());
      return $this->user;
    } // getUser
    
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
    
  } // TicketSubscription 

?>