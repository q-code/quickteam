quickteam
=========

Quickteam 3.0 is the last stable version

==============================
UPGRADE to QuickTeam v3.0
==============================

To upgrade from version 2.x to 3.0, you can proceed with a normal installation (see here after).

  Remarque #1
  It's recommended to backup your config.php file in the /bin/ directory
  (in case you don't remember the connection parameters of your database)
  
  Remarque #2
  If your board allows user photo (avatar) or document upload,
  it's recommended to NOT delete the /picture/ and /document/ directories and subdirectories.
  Other files and folders can be deleted before installing the new release.

==============================
INSTALLATION of QuickTeam v3.0
==============================

BEFORE starting the installation procedure, make sure you know:
- The type of database you will use (MySQL, SQLserver, PostgreSQL, Oracle, Firebird, SQLite, DB2).
- Your database host (the name of your database server, often "localhost")
- The name of your database (where the QuickTeam can install the tables).
- The user name for this database (having the right to create table).
- The user password for this database.


1. Upload the application on your web server
--------------------------------------------
Just send (ftp) all the files and folders on your webserver (for example in a folder /quickteam/).
If you are making an upgrade, do NOT overwrite the /picture/ nor /document/ directories and subdirectories.


2. Configure the permissions
----------------------------
This step is very important !
Without this configuration, the installation programme will not work and the database will not be configured.

Change the permission of the folder and subfolders /picture/ and /document/ to make them writable (chmod 777).
Change the permission of the file /bin/config.php to make it writable (chmod 777).


3. Start the installation
-------------------------
From your web browser, type the install url of the application.
(i.e. Type the url http://www.yourwebsite.com/quickteam/install)

4. Clean up
-----------
When previous steps are completed, you can delete the /install/ folder on your website and set the permission for /bin/config.php to readonly. The other configuration file must remain writable.


