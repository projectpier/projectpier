<?php

  /**
  * TicketSubscriptions, generated on Mon, 29 May 2006 03:51:15 +0200 by 
  * DataObject generation tool
  *
  * @http://www.projectpier.org/
  */
  class TicketSubscriptions extends BaseTicketSubscriptions {
  
    /**
    * Return array of users that are subscribed to this specific ticket
    *
    * @param ProjectTicket $ticket
    * @return array
    */
    static function getUsersByTicket(ProjectTicket $ticket) {
      $users = array();
      $subscriptions = TicketSubscriptions::findAll(array(
        'conditions' => '`ticket_id` = ' . DB::escape($ticket->getId())
      )); // findAll
      if(is_array($subscriptions)) {
        foreach($subscriptions as $subscription) {
          $user = $subscription->getUser();
          if($user instanceof User) $users[] = $user;
        } // foreach
      } // if
      return count($users) ? $users : null;
    } // getUsersByTicket
    
    /**
    * Return array of tickets that $user is subscribed to
    *
    * @param User $user
    * @return array
    */
    static function getTicketsByUser(User $user) {
      $tickets = array();
      $subscriptions = TicketSubscriptions::findAll(array(
        'conditions' => '`user_id` = ' . DB::escape($user->getId())
      )); // findAll
      if(is_array($subscriptions)) {
        foreach($subscriptions as $subscription) {
          $ticket = $subscription->getTicket();
          if($tickets instanceof ProjectTicket) $tickets[] = $ticket;
        } // foreach
      } // if
      return count($tickets) ? $tickets : null;
    } // getTicketsByUser
    
    /**
    * Clear subscriptions by ticket
    *
    * @param ProjectTicket $ticket
    * @return boolean
    */
    static function clearByTicket(ProjectTicket $ticket) {
      return TicketSubscriptions::delete('`ticket_id` = ' . DB::escape($ticket->getId()));
    } // clearByTicket
    
    /**
    * Clear subscriptions by user
    *
    * @param User $user
    * @return boolean
    */
    static function clearByUser(User $user) {
      return TicketSubscriptions::delete('`user_id` = ' . DB::escape($user->getId()));
    } // clearByUser
    
  } // TicketSubscriptions 

?>