<?php

  /**
  * This error is thrown when script tries to write content into the file and fails
  *
  * @version 1.0
  * @http://www.projectpier.org/
  */
  class FailedToWriteFileError extends Error {
  
    /**
    * Path of the requested file
    *
    * @var string
    */
    private $file_path;
    
    /**
    * Construct the FailedToWriteFileError
    *
    * @access public
    * @param void
    * @return FailedToWriteFileError
    */
    function __construct($file_path, $message = null) {
      if(is_null($message)) $message = "Failed to write content into '$file_path'";
      parent::__construct($message);
      $this->setFilePath($file_path);
    } // __construct
    
    /**
    * Return errors specific params...
    *
    * @access public
    * @param void
    * @return array
    */
    function getAdditionalParams() {
      return array(
        'file path' => $this->getFilePath()
      ); // array
    } // getAdditionalParams
    
    // -------------------------------------------------------
    // Getters and setters
    // -------------------------------------------------------
    
    /**
    * Get file_path
    *
    * @access public
    * @param null
    * @return string
    */
    function getFilePath() {
      return $this->file_path;
    } // getFilePath
    
    /**
    * Set file_path value
    *
    * @access public
    * @param string $value
    * @return null
    */
    function setFilePath($value) {
      $this->file_path = $value;
    } // setFilePath
  
  } // FailedToWriteFileError

?>
