<?php

  /**
  * TicketChange class
  * Generated on Sat, 04 Mar 2006 12:50:11 +0100 by DataObject generation tool
  *
  * @http://www.projectpier.org/
  */
  class TicketChange extends BaseTicketChange {
    
    /**
    * Cached Changeset
    *
    * @var TicketChangeset
    */
    private $changeset;
    
    /**
    * Returns changeset object
    *
    * @param void
    * @return TicketChangeset
    */
    function getChangeset() {
      if (is_null($this->changeset)) {
        $this->changeset = TicketChangesets::findById($this->getChangesetId());
      }
      return $this->changeset;
    } // getChangeset
    
    /**
    * Returns if data needs translation
    *
    * @param void
    * @return ProjectTicket
    */
    function dataNeedsTranslation() {
      return ($this->getType() == 'priority') || ($this->getType() == 'type') || ($this->getType() == 'status') || ($this->getType() == 'private');
    } // dataNeedsTranslation
  
  } // TicketChange

?>