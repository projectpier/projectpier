<?php

  /**
  * TicketChanges, generated on Wed, 08 Mar 2006 15:51:26 +0100 by 
  * DataObject generation tool
  *
  * @http://www.projectpier.org/
  */
  class TicketChanges extends BaseTicketChanges {
    
    /**
    * Return array of ticket's changes
    *
    * @param ProjectTicket $ticket
    * @return array
    */
    static function getChangesByTicket(ProjectTicket $ticket) {
      return self::findAll(array(
        'conditions' => array('`ticket_id` = ?', $ticket->getId()),
        'order' => '`created_on`'
      )); // array
    } // getChangesByTicket
    
  } // TicketChanges 

?>