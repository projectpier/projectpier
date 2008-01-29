<?php

  /**
  * Localization class
  *
  * This class will set up PHP environment to mach locale settings (using 
  * setlocale() function) and import apropriate set of words from language
  * folder. Properties of this class are used by some other system classes
  * for outputing data in correct format (for instance DateTimeValueLib).
  * 
  * @version 1.0
  * @http://www.projectpier.org/
  */
  class Localization {
    
    /**
    * Path to directory where language settings are
    *
    * @var string
    */
    private $language_dir_path = null;
    
    /**
    * strftime() function format used for presenting date and time
    *
    * @var string
    */
    private $datetime_format = 'M d. Y H:i';
    
    /**
    * strftime() function format used for presenting date
    *
    * @var string
    */
    private $date_format = 'M d. Y';
    
    /**
    * Descriptive date format is string used in date() function that will autput date 
    * in such a way that it tells as much as it can: with day it is and when it is. 
    * This one is used for such things as milestones and tasks where you need to see 
    * as much info about due date as you can from a simple, short string
    *
    * @var string
    */
    private $descriptive_date_format = 'l, j F';
    
    /**
    * strftime() function format used for presenting time
    *
    * @var string
    */
    private $time_format = 'H:i';
    
    /**
    * Locale code
    *
    * @var string
    */
    private $locale;
    
    /**
    * Current locale settings, returned by setlocale() function
    *
    * @var string
    */
    private $current_locale;
    
    /**
    * Container of langs
    *
    * @var Container
    */
    private $langs;
  
    /**
    * Construct the Localization
    *
    * @access public
    * @param string $language_dir_path Path to the language dir
    * @param string $local
    * @return Localization
    */
    function __construct() {
      $this->langs = new Container();
    } // __construct
    
    /**
    * Return lang by name
    *
    * @param string $name
    * @param mixed $default Default value that will be returned if lang is not found
    * @return string
    */
    function lang($name, $default = null) {
      if (is_null($default)) {
        $default = "<span style=\"font-weight: bolder; color: red;\">Missing lang: $name</span>";
      } // if
      return $this->langs->get($name, $default);
    } // lang
    
    /**
    * Load language settings
    *
    * @access public
    * @param string $locale Locale code
    * @param string $language_dir Path to directory where we have all 
    *   languages defined
    * @return null
    * @throws DirDnxError If language dir does not exists
    * @throws FileDnxError If language settings file for this local settings
    *   does not exists in lanuage dir
    */
    function loadSettings($locale, $languages_dir) {
      
      $this->setLocale($locale);
      $this->setLanguageDirPath($languages_dir);
      
      return $this->loadLanguageSettings();
      
    } // loadSettings
    
    /**
    * Load language settings
    *
    * @access public
    * @param void
    * @throws DirDnxError If language dir does not exists
    * @throws FileDnxError If language settings file for this local settings
    *   does not exists in lanuage dir
    */
    private function loadLanguageSettings() {
      
      // Check dir...
      if (!is_dir($this->getLanguageDirPath())) {
        throw new DirDnxError($this->getLanguageDirPath());
      } // if
      
      // Get settings file path and include it
      $settings_file = $this->getLanguageDirPath() . '/' . $this->getLocale() . '.php';
      if (is_file($settings_file)) {
        include_once $settings_file;
      } else {
        throw new FileDnxError($settings_file, "Failed to find language settings file. Expected location: '$settings_file'.");
      } // if
      
      // Clear langs
      $this->langs->clear();
      
      // Get langs dir
      $langs_dir = $this->getLanguageDirPath() . '/' . $this->getLocale();
      if (is_dir($langs_dir)) {
        $files = get_files($langs_dir, 'php');
        
        // Loop through files and add langs
        if (is_array($files)) {
          foreach ($files as $file) {
            $langs = include_once $file;
            if (is_array($langs)) {
              $this->langs->append($langs);
            } // if
          } // foreach
        } // if
        
      } else {
        throw new DirDnxError($langs_dir);
      } // if
      
      // Done!
      return true;
      
    } // loadLanguageSettings
    
    /**
    * Return formated date
    *
    * @access public
    * @param DateTimeValue $date
    * @param float $timezone Timezone offset in hours
    * @return string
    */
    function formatDate(DateTimeValue $date, $timezone = 0) {
      return date($this->date_format, $date->getTimestamp() + ($timezone * 3600));
    } // formatDate
    
    /**
    * * Descriptive date format is string used in date() function that will autput date 
    * in such a way that it tells as much as it can: with day it is and when it is. 
    * This one is used for such things as milestones and tasks where you need to see 
    * as much info about due date as you can from a simple, short string
    *
    * @param DateTimeValue $date
    * @param float $timezone Timezone offset in hours
    * @return string
    */
    function formatDescriptiveDate(DateTimeValue $date, $timezone = 0) {
      return date($this->descriptive_date_format, $date->getTimestamp() + ($timezone * 3600));
    } // formatDescriptiveDate
    
    /**
    * Return formated datetime
    *
    * @access public
    * @param DateTimeValue $date
    * @param float $timezone Timezone offset in hours
    * @return string
    */
    function formatDateTime(DateTimeValue $date, $timezone = 0) {
      return date($this->datetime_format, $date->getTimestamp() + ($timezone * 3600));
    } // formatDateTime
    
    /**
    * Return fromated time
    *
    * @access public
    * @param DateTimeValue $date
    * @param float $timezone Timezone offset in hours
    * @return string
    */
    function formatTime(DateTimeValue $date, $timezone = 0) {
      return date($this->time_format, $date->getTimestamp() + ($timezone * 3600));
    } // formatTime
    
    // -------------------------------------------------------------
    //  Getters and setters
    // -------------------------------------------------------------
    
    /**
    * Get language_dir_path
    *
    * @access public
    * @param null
    * @return string
    */
    function getLanguageDirPath() {
      return $this->language_dir_path;
    } // getLanguageDirPath
    
    /**
    * Set language_dir_path value
    *
    * @access public
    * @param string $value
    * @return null
    */
    function setLanguageDirPath($value) {
      $this->language_dir_path = $value;
    } // setLanguageDirPath
    
    /**
    * Get datetime format
    *
    * @access public
    * @param null
    * @return string
    */
    function getDateTimeFormat() {
      return $this->datetime_format;
    } // getDateTimeFormat
    
    /**
    * Set datetime foramt value
    *
    * @access public
    * @param string $value
    * @return null
    */
    function setDateTimeFormat($value) {
      $this->datetime_format = (string) $value;
    } // setDateTimeFormat
    
    /**
    * Get date format
    *
    * @access public
    * @param null
    * @return string
    */
    function getDateFormat() {
      return $this->date_format;
    } // getDateFormat
    
    /**
    * Set date format value
    *
    * @access public
    * @param string $value
    * @return null
    */
    function setDateFormat($value) {
      $this->date_format = (string) $value;
    } // setDateFormat
    
    /**
    * Get time format
    *
    * @access public
    * @param null
    * @return string
    */
    function getTimeFormat() {
      return $this->time_format;
    } // getTimeFormat
    
    /**
    * Set time format value
    *
    * @access public
    * @param string $value
    * @return null
    */
    function setTimeFormat($value) {
      $this->time_format = (string) $value;
    } // setTimeFormat
    
    /**
    * Get locale
    *
    * @access public
    * @param null
    * @return string
    */
    function getLocale() {
      return $this->locale;
    } // getLocale
    
    /**
    * Set locale value
    *
    * @access public
    * @param string $value
    * @return boolean
    */
    function setLocale($value) {
      $this->locale = $value;
    } // setLocale
    
    /**
    * Return current locale settings
    *
    * @access public
    * @param void
    * @return string
    */
    //function getCurrentLocale() {
    //  if (trim($this->current_locale)) {
    //    return $this->current_locale;
    //  } else {
    //    return setlocale(LC_ALL, 0);
    //  } // if
    //} // getCurrentLocale
    
    /**
    * Interface to langs container
    *
    * @access public
    * @param void
    * @return Container
    */
    function langs() {
      return $this->langs;
    } // langs
    
    /**
    * Return localization instance
    *
    * @access public
    * @param string $locale Localization code
    * @return Localization
    */
    static function instance() {
      static $instance;
      
      // Prepare instance
      if (!($instance instanceof Localization)) {
        $instance = new Localization();
      } // if
      
      // Done...
      return $instance;
      
    } // instance
  
  } // Localization

?>
