           		
~~~~~~~~~~~~~~~~~~~
System requirements
~~~~~~~~~~~~~~~~~~~
	
ProjectPier requires a web server, PHP (5.0.2 or greater with MySQL, GD, and 
SimpleXML extensions) and MySQL with InnoDB support. The recommended web 
server is Apache, but IIS 5 and above have been reported to work also.
	
ProjectPier is not PHP4 compatible and it will not run on PHP versions prior
to PHP 5.0.2.
	
Recommended configuration:
	
: PHP 5.1 or greater
: MySQL 4.1 or greater with InnoDB support (see notes below)
: Apache 2.0 or greater
        
If you do not have these installed on a server or your personal computer, 
you can visit the sites below to learn more about how to download and install
them.  They are all licensed under various compatible Open Source licenses.	
: PHP    : http://www.php.net/
: MySQL  : http://www.mysql.com/
: Apache : http://www.apache.org/
	
~~~~~~~~~~~~ 
Installation
~~~~~~~~~~~~
	
1. Make sure your server meets the requirements, see notes below regarding
   enabling InnoDB support.
2. Download ProjectPier from the project website - http://www.projectpier.org/.
3. Unpack and upload the files to your web server.
4. Direct your browser to the /public/install directory and follow the installation
   procedure.
	   
~~~~~~~~~~~~~~~~~~~~~~~~
Enabling InnoDB Support
~~~~~~~~~~~~~~~~~~~~~~~~

Some installations of MySQL don't support InnoDB by default.  The ProjectPier installer 
will tell you if your server is not configured to support InnoDB. This is easy to fix: 

1. Open your MySQL options file, the file name is my.cnf (Linux) - usually at /etc/my.cnf 
   or my.ini (Windows) - usually at c:/windows/my.ini.
2. Comment the skip-innodb line by adding # in front of it (like #skip-innodb). 
3. It would also be good to increase max_allowed_packet to ensure that 
   you'll be able to upload files larger than 1MB. Just add this 
   line bellow #skip-innodb line: 
   set-variable = max_allowed_packet=64M

~~~~~~~~~~~~~~~~~
About ProjectPier
~~~~~~~~~~~~~~~~~
	
ProjectPier is an Open Source project management and collaboration
tool that you can install on your own server. It is released under the 
terms of the Honest Public License - HPL (see license.txt for details).  
It is built upon the source code of the activeCollab software version 0.7.1 
which is also licensed under the HPL
	
: http://www.projectpier.org