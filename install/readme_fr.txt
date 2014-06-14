==============================
UPGRADE vers QuickTeam v3.0
==============================

Pour passer de la version 2.x � 3.0, vous pouvez proc�der � une installation standard (voir ci-apr�s).

  Remarque #1
  Il est recommand� de faire une sauvegarde du fichier /bin/config.php
  (au cas o� vous ne vous souvenez plus des param�tres de connexion vers votre base de donn�e).
  
  Remarque #2
  Si votre application autorisait les photos (avatars) et les documents (upload),
  il est recommand� de pr�server les r�pertoires /picture/ et /document/ et leur sous-r�pertoires.
  Les autres fichiers et r�pertoires peuvent �tre remplac�.

==============================
INSTALLATION de QuickTeam v3.0
==============================

AVANT de commencer l'installation, assurez-vous que vous connaissez :
- Le type de base de donn�e que vous utilisez (MySQL, SQLserver, PostgreSQL, Oracle, Firebird, SQLite, DB2.
- Le nom de l'hote de votre base de donn�e (le nom du serveur de base de donn�e, souvent "localhost").
- Le nom de votre base de donn�e (o� QuickTeam peut installer ses tables).
- Le nom d'utilisateur pour cette base de donn�e (ayant le droit de cr�er des tables).
- Le mot de passe de celui-ci.


1. Envoyez l'application sur votre espace web
---------------------------------------------
Vous devez simplement envoyer (ftp) tous les fichiers et r�pertoires sur votre espace web (par exemple dans un r�pertoire /quickteam/).
Si vous aviez une version pr�c�dente, veillez � ne PAS effacer les r�pertoires /picture/ et /document/ (et leur sous-r�pertoires).


2. D�finir les permissions
--------------------------
Cette �tape est tr�s importante !
Sans elle, le programme d'installation ne pourra pas s'ex�cuter et votre base de donn�e ne pourra �tre configur�e.

Changer les permissions sur des r�pertoires et sous-r�pertoires /picture/ et /document/ afin qu'ils soient inscriptible (chmod 777)
Changer les permissions sur le fichier /bin/config.php afin qu'il soit inscriptible (chmod 777)


3. Lancer l'installation
------------------------
Depuis votre navigateur internet, entrez l'adresse d'installation de votre application.
(ex: Tappez l'url http://www.votresiteweb.com/quickteam/install)


4. Nettoyage
------------
Lorsque les �tapes pr�c�dentes sont termin�es, vous pouvez effacer le r�pertoire /install/ et changer les permissions de /bin/config.php en lecture seule. Les autres fichiers de configuration doivent rester en �criture.