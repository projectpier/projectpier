<?php

  /**
  * TicketChangesets
  *
  * @http://www.projectpier.org/
  */
  class TicketChangesets extends BaseTicketChangesets {
    
    /**
    * Return array of ticket's changesets
    *
    * @param ProjectTicket $ticket
    * @return array
    */
    static function getChangesetsByTicket(ProjectTicket $ticket) {
      return self::findAll(array(
        'conditions' => array('`ticket_id` = ?', $ticket->getId()),
        'order' => '`created_on`'
      )); // array
    } // getChangesetsByTicket
    
  } // TicketChangesets

?>