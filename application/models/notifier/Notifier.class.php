<?php

  /**
  * Notifier class has purpose of sending various notification to users. Primary
  * notification method is email
  *
  * @version 1.0
  * @http://www.projectpier.org/
  */
  class Notifier {
    
    /** Supported transports **/
    const MAIL_TRANSPORT_MAIL = 'mail()';
    const MAIL_TRANSPORT_SMTP = 'smtp';
    
    /** Secure connection values **/
    const SMTP_SECURE_CONNECTION_NO  = 'no';
    const SMTP_SECURE_CONNECTION_SSL = 'ssl';
    const SMTP_SECURE_CONNECTION_TLS = 'tls';
    
    /**
    * Cached value of exchange compatible config option
    *
    * @var boolean
    */
    static public $exchange_compatible = null;
    
    /**
    * Reset password and send forgot password email to the user
    *
    * @param User $user
    * @return boolean
    * @throws NotifierConnectionError
    */
    static function forgotPassword(User $user) {
      $administrator = owner_company()->getCreatedBy();
      
      $new_password = $user->resetPassword(true);
      tpl_assign('user', $user);
      tpl_assign('new_password', $new_password);
      
      return self::sendEmail(
        self::prepareEmailAddress($user->getEmail(), $user->getDisplayName()),
        self::prepareEmailAddress($administrator->getEmail(), $administrator->getDisplayName()),
        lang('your password'),
        tpl_fetch(get_template_path('forgot_password', 'notifier'))
      ); // send
    } // forgotPassword
    
    /**
    * Send new account notification email to the user whose account has been created 
    * (welcome message)
    *
    * @param User $user
    * @param string $raw_password
    * @return boolean
    * @throws NotifierConnectionError
    */
    static function newUserAccount(User $user, $raw_password) {
      tpl_assign('new_account', $user);
      tpl_assign('raw_password', $raw_password);
      
      return self::sendEmail(
        self::prepareEmailAddress($user->getEmail(), $user->getDisplayName()),
        self::prepareEmailAddress($user->getCreatedBy()->getEmail(), $user->getCreatedByDisplayName()),
        lang('your account created'),
        tpl_fetch(get_template_path('new_account', 'notifier'))
      ); // send
    } // newUserAccount
  
    /**
    * Send account update notification email to the user whose account has been updated
    *
    * @param User $user
    * @param string $raw_password
    * @return boolean
    * @throws NotifierConnectionError
    */
    static function updatedUserAccount(User $user, $raw_password) {
      tpl_assign('updated_account', $user);
      tpl_assign('raw_password', $raw_password);
      
      return self::sendEmail(
        self::prepareEmailAddress($user->getEmail(), $user->getDisplayName()),
        self::prepareEmailAddress($user->getUpdatedBy()->getEmail(), $user->getUpdatedByDisplayName()),
        lang('your account updated'),
        tpl_fetch(get_template_path('updated_account', 'notifier'))
      ); // send
    } // updatedUserAccount
  
    /**
    * Send new message notification to the list of users ($people)
    *
    * @param ProjectMessage $message New message
    * @param array $people
    * @return boolean
    * @throws NotifierConnectionError
    */
    static function newMessage(ProjectMessage $message, $people) {
      if (!is_array($people) || !count($people)) {
        return; // nothing here...
      } // if
      
      tpl_assign('new_message', $message);
      
      $recipients = array();
      foreach ($people as $user) {
        $recipients[] = self::prepareEmailAddress($user->getEmail(), $user->getDisplayName());
      } // foreach
      
      return self::sendEmail(
        $recipients,
        self::prepareEmailAddress($message->getCreatedBy()->getEmail(), $message->getCreatedByDisplayName()),
        $message->getProject()->getName() . ' - ' . $message->getTitle(),
        tpl_fetch(get_template_path('new_message', 'notifier'))
      ); // send
    } // newMessage

    /**
    * Send ticket notification to the list of users ($people)
    *
    * @param ProjectTicket $ticket New ticket
    * @param array $people
    * @param string $template template to send notification
    * @param User $user user who send the notification
    * @return boolean
    * @throws NotifierConnectionError
    */
    static function ticket(ProjectTicket $ticket, $people, $template, $user) {
      if (!is_array($people) || !count($people)) {
        return; // nothing here...
      } // if

      $recipients = array();
      foreach ($people as $subscriber) {
        if ($subscriber->getId() == $user->getId()) {
          continue; // skip comment author
        } // if

        $recipients[] = self::prepareEmailAddress($subscriber->getEmail(), $subscriber->getDisplayName());
      } // foreach

      if (!count($recipients)) {
        return true; // no recipients
      } // if

      tpl_assign('changeset', $ticket->getLastChangeset());
      tpl_assign('ticket', $ticket);

      return self::sendEmail(
        $recipients,
        self::prepareEmailAddress($user->getEmail(), $user->getDisplayName()),
        $ticket->getProject()->getName() . ' - ' . $ticket->getSummary(),
        tpl_fetch(get_template_path($template, 'notifier'))
      ); // send
    } // ticket
    
    /**
    * Send ticket change notification to the list of users
    *
    * @param ProjectTicket $ticket
    * @param TicketChangeset $changeset
    * @param User $user
    * @return boolean
    * @throws NotifierConnectionError
    */
    static function ticketChange(ProjectTicket $ticket, TicketChangeset $changeset, User $user) {
      $all_subscribers = $ticket->getSubscribers();
      if (!is_array($all_subscribers)) {
        return true; // no subscribers
      } // if
      
      $recipients = array();
      foreach ($all_subscribers as $subscriber) {
        if ($subscriber->getId() == $user->getId()) {
          continue; // skip comment author
        } // if
        
        $recipients[] = self::prepareEmailAddress($subscriber->getEmail(), $subscriber->getDisplayName());
      } // foreach
      
      if (!count($recipients)) {
        return true; // no recipients
      } // if
      
      tpl_assign('ticket', $ticket);
      tpl_assign('changeset', $changeset);
      
      return self::sendEmail(
        $recipients,
        self::prepareEmailAddress($ticket->getUpdatedBy()->getEmail(), $ticket->getUpdatedBy()->getDisplayName()),
        $ticket->getProject()->getName() . ' - ' . $ticket->getSummary(),
        tpl_fetch(get_template_path('ticket_change', 'notifier'))
      ); // send
    } // ticketChange

    /**
    * Send some files attached to ticket notification to ticket subscribers
    *
    * @param ProjectTicket $ticket
    * @param array $attached_files Files attached to ticket
    * @return boolean
    * @throws NotifierConnectionError
    */
    static function attachFilesToTicket(ProjectTicket $ticket, $attached_files) {
      $all_subscribers = $ticket->getSubscribers();
      if (!is_array($all_subscribers)) {
        return true; // no subscribers
      } // if
      
      $recipients = array();
      foreach ($all_subscribers as $subscriber) {
        if ($subscriber->getId() == $ticket->getUpdatedById()) {
          continue; // skip comment author
        } // if
        
        $recipients[] = self::prepareEmailAddress($subscriber->getEmail(), $subscriber->getDisplayName());
      } // foreach
      
      if (!count($recipients)) {
        return true; // no recipients
      } // if
      
      tpl_assign('ticket', $ticket);
      tpl_assign('attached_files', $attached_files);
      
      return self::sendEmail(
        $recipients,
        self::prepareEmailAddress($ticket->getUpdatedBy()->getEmail(), $ticket->getUpdatedBy()->getDisplayName()),
        $ticket->getProject()->getName() . ' - ' . $ticket->getSummary(),
        tpl_fetch(get_template_path('attach_files_ticket', 'notifier'))
      ); // send
    } // attachFilesToTicket
    
    /**
    * Send some files detached from ticket notification to ticket subscribers
    *
    * @param ProjectTicket $ticket
    * @param array $detached_files Files detached from ticket
    * @return boolean
    * @throws NotifierConnectionError
    */
    static function detachFilesFromTicket(ProjectTicket $ticket, $detached_files) {
      $all_subscribers = $ticket->getSubscribers();
      if (!is_array($all_subscribers)) {
        return true; // no subscribers
      } // if
      
      $recipients = array();
      foreach ($all_subscribers as $subscriber) {
        if ($subscriber->getId() == $ticket->getUpdatedById()) {
          continue; // skip comment author
        } // if
        
        $recipients[] = self::prepareEmailAddress($subscriber->getEmail(), $subscriber->getDisplayName());
      } // foreach
      
      if (!count($recipients)) {
        return true; // no recipients
      } // if
      
      tpl_assign('ticket', $ticket);
      tpl_assign('detached_files', $detached_files);
      
      return self::sendEmail(
        $recipients,
        self::prepareEmailAddress($ticket->getUpdatedBy()->getEmail(), $ticket->getUpdatedBy()->getDisplayName()),
        $ticket->getProject()->getName() . ' - ' . $ticket->getSummary(),
        tpl_fetch(get_template_path('detach_files_ticket', 'notifier'))
      ); // send
    } // detachFilesFromTicket
    
    /**
    * Send new comment notification to message subscriber
    *
    * @param MessageComment $comment
    * @return boolean
    * @throws NotifierConnectionError
    */
    static function newMessageComment(Comment $comment) {
      $message = $comment->getObject();
      if (!($message instanceof ProjectMessage)) {
        throw new Error('Invalid comment object');
      } // if
      
      return self::newComment($comment, $message->getSubscribers());
    } // newMessageComment

    /**
    * Send new comment notification to ticket subscriber
    *
    * @param TicketComment $comment
    * @return boolean
    * @throws NotifierConnectionError
    */
    static function newTicketComment(Comment $comment) {
      $ticket = $comment->getObject();
      if (!($ticket instanceof ProjectTicket)) {
        throw new Error('Invalid comment object');
      } // if

      return self::newComment($comment, $ticket->getSubscribers());
    } // newTicketComment

    /**
    * Send new comment notification to subscribers
    *
    * @access private
    * @param Comment $comment
    * @param string $title title of object for subject
    * @param array $all_subscribers subscribers
    * @return boolean
    * @throws NotifierConnectionError
    */
    static function newComment(Comment $comment, $all_subscribers) {
      if (!is_array($all_subscribers)) {
        return true; // no subscribers
      } // if
      
      $recipients = array();
      foreach ($all_subscribers as $subscriber) {
        if ($subscriber->getId() == $comment->getCreatedById()) {
          continue; // skip comment author
        } // if
        
        if ($comment->isPrivate()) {
          if ($subscriber->isMemberOfOwnerCompany()) {
            $recipients[] = self::prepareEmailAddress($subscriber->getEmail(), $subscriber->getDisplayName());
          } // if
        } else {
          $recipients[] = self::prepareEmailAddress($subscriber->getEmail(), $subscriber->getDisplayName());
        } // of
      } // foreach
      
      if (!count($recipients)) {
        return true; // no recipients
      } // if
      
      tpl_assign('new_comment', $comment);
      
      return self::sendEmail(
        $recipients,
        self::prepareEmailAddress($comment->getCreatedBy()->getEmail(), $comment->getCreatedByDisplayName()),
        $comment->getProject()->getName() . ' - ' . $comment->getObject()->getTitle(),
        tpl_fetch(get_template_path('new_comment', 'notifier'))
      ); // send
    } // newComment
    
    // ---------------------------------------------------
    //  Milestone
    // ---------------------------------------------------
    
    /**
    * Milestone has been assigned to the user
    *
    * @param ProjectMilestone $milestone
    * @return boolean
    * @throws NotifierConnectionError
    */
    function milestoneAssigned(ProjectMilestone $milestone) {
      if ($milestone->isCompleted()) {
        return true; // milestone has been already completed...
      } // if
      if (!($milestone->getAssignedTo() instanceof User)) {
        return true; // not assigned to user
      } // if
      
      tpl_assign('milestone_assigned', $milestone);
      
      return self::sendEmail(
        self::prepareEmailAddress($milestone->getAssignedTo()->getEmail(), $milestone->getAssignedTo()->getDisplayName()),
        self::prepareEmailAddress($milestone->getCreatedBy()->getEmail(), $milestone->getCreatedByDisplayName()),
        $milestone->getProject()->getName() . ' - ' . lang('milestone assigned to you') . " - " . $milestone->getName(),
        tpl_fetch(get_template_path('milestone_assigned', 'notifier'))
      ); // send
    } // milestoneAssigned
    
    // ---------------------------------------------------
    //  Util functions
    // ---------------------------------------------------
    
    /**
    * This function will prepare email address. It will return $name <$email> if both 
    * params are presend and we are not in exchange compatibility mode. In other case 
    * it will just return email
    *
    * @param string $email
    * @param string $name
    * @return string
    */
    static function prepareEmailAddress($email, $name = null) {
      if (trim($name) && !self::getExchangeCompatible()) {
        return trim($name) . ' <' . trim($email) . '>';
      } else {
        return trim($email);
      } // if
    } // prepareEmailAddress
    
    /**
    * Returns true if exchange compatible config option is set to true
    *
    * @param void
    * @return boolean
    */
    static function getExchangeCompatible() {
      if (is_null(self::$exchange_compatible)) {
        self::$exchange_compatible = config_option('exchange_compatible', false);
      } // if
      return self::$exchange_compatible;
    } // getExchangeCompatible
    
    /**
    * Send an email using Swift (send commands)
    * 
    * @param string to_address
    * @param string from_address
    * @param string subject
    * @param string body, optional
    * @param string content-type,optional
    * @param string content-transfer-encoding,optional
    * @return bool successful
    */
    static function sendEmail($to, $from, $subject, $body = false, $type = 'text/plain', $encoding = '8bit') {
      Env::useLibrary('swift');
      
      $mailer = self::getMailer();
      if (!($mailer instanceof Swift)) {
        throw new NotifierConnectionError();
      } // if
      
      $result = $mailer->send($to, $from, $subject, $body, $type, $encoding);
      $mailer->close();
      
      return $result;
    } // sendEmail
    
    /**
    * This function will return SMTP connection. It will try to load options from 
    * config and if it fails it will use settings from php.ini
    *
    * @param void
    * @return Swift
    */
    static function getMailer() {
      $mail_transport_config = config_option('mail_transport', self::MAIL_TRANSPORT_MAIL);
      
      // Emulate mail() - use NativeMail
      if ($mail_transport_config == self::MAIL_TRANSPORT_MAIL) {
        $mailer = new Swift(new Swift_Connection_NativeMail());
        return $mailer->isConnected() ? $mailer : null;
        
      // Use SMTP server
      } elseif ($mail_transport_config == self::MAIL_TRANSPORT_SMTP) {
        
        // Load SMTP config
        $smtp_server = config_option('smtp_server');
        $smtp_port = config_option('smtp_port', 25);
        $smtp_secure_connection = config_option('smtp_secure_connection', self::SMTP_SECURE_CONNECTION_NO);
        $smtp_authenticate = config_option('smtp_authenticate', false);
        if ($smtp_authenticate) {
          $smtp_username = config_option('smtp_username');
          $smtp_password = config_option('smtp_password');
        } // if
        
        switch ($smtp_secure_connection) {
          case self::SMTP_SECURE_CONNECTION_SSL:
            $transport = SWIFT_SSL;
            break;
          case self::SMTP_SECURE_CONNECTION_TLS:
            $transport = SWIFT_TLS;
            break;
          default:
            $transport = SWIFT_OPEN;
        } // switch
        
        $mailer = new Swift(new Swift_Connection_SMTP($smtp_server, $smtp_port, $transport));
        if (!$mailer->isConnected()) {
          return null;
        } // if
        
        $mailer->setCharset('UTF-8');
        
        if ($smtp_authenticate) {
          if ($mailer->authenticate($smtp_username, $smtp_password)) {
            return $mailer;
          } else {
            return null;
          } // if
        } else {
          return $mailer;
        } // if
        
      // Somethings wrong here...
      } else {
        return null;
      } // if
    } // getMailer
  
  } // Notifier  
?>
