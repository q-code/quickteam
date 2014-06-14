==============================
UPGRADE vers QuickTeam v3.0
==============================

Pour passer de la version 2.x à 3.0, vous pouvez procéder à une installation standard (voir ci-après).

  Remarque #1
  Il est recommandé de faire une sauvegarde du fichier /bin/config.php
  (au cas où vous ne vous souvenez plus des paramètres de connexion vers votre base de donnée).
  
  Remarque #2
  Si votre application autorisait les photos (avatars) et les documents (upload),
  il est recommandé de préserver les répertoires /picture/ et /document/ et leur sous-répertoires.
  Les autres fichiers et répertoires peuvent être remplacé.

==============================
INSTALLATION de QuickTeam v3.0
==============================

AVANT de commencer l'installation, assurez-vous que vous connaissez :
- Le type de base de donnée que vous utilisez (MySQL, SQLserver, PostgreSQL, Oracle, Firebird, SQLite, DB2.
- Le nom de l'hote de votre base de donnée (le nom du serveur de base de donnée, souvent "localhost").
- Le nom de votre base de donnée (où QuickTeam peut installer ses tables).
- Le nom d'utilisateur pour cette base de donnée (ayant le droit de créer des tables).
- Le mot de passe de celui-ci.


1. Envoyez l'application sur votre espace web
---------------------------------------------
Vous devez simplement envoyer (ftp) tous les fichiers et répertoires sur votre espace web (par exemple dans un répertoire /quickteam/).
Si vous aviez une version précédente, veillez à ne PAS effacer les répertoires /picture/ et /document/ (et leur sous-répertoires).


2. Définir les permissions
--------------------------
Cette étape est très importante !
Sans elle, le programme d'installation ne pourra pas s'exécuter et votre base de donnée ne pourra être configurée.

Changer les permissions sur des répertoires et sous-répertoires /picture/ et /document/ afin qu'ils soient inscriptible (chmod 777)
Changer les permissions sur le fichier /bin/config.php afin qu'il soit inscriptible (chmod 777)


3. Lancer l'installation
------------------------
Depuis votre navigateur internet, entrez l'adresse d'installation de votre application.
(ex: Tappez l'url http://www.votresiteweb.com/quickteam/install)


4. Nettoyage
------------
Lorsque les étapes précédentes sont terminées, vous pouvez effacer le répertoire /install/ et changer les permissions de /bin/config.php en lecture seule. Les autres fichiers de configuration doivent rester en écriture.