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

    /**
    * Returns ticket's last changeset
    *
    * @param ProjectTicket $ticket
    * @return TicketChangeset
    */
    static function getLastChangesetByTicket(ProjectTicket $ticket) {
      return self::findOne(array(
        'conditions' => array('`ticket_id` = ?', $ticket->getId()),
        'order' => '`created_on` DESC',
        'limit' => '1'));
    } // getLastChangesetByTicket
    
  } // TicketChangesets

?>