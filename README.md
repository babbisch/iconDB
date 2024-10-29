# iconDB

Ein  Datenbank f�r Mannschaftswappen. 

### Systemvoraussetzungen
- PHP 7.x
- MySQL

### Installation
Beim Erstaufruf erfolgt ein automatisierter Aufruf der Installation. Anzugeben sind hier die Credentials f�r die Datenbank als auch die zu nutzenden Grafiktypen.

### Administration
Die Administration kann �ber `/adminer/` aufgerufen werden, es gibt kein Passwort f�r den Administrationsbereich. Ggf. das Verzeichnus umbenennen.

### Massenimport bestehender Wappen
Mittels `/import/dir2basae.php` k�nnen im Verzeichnis `/icons/` bereits vorab abgelegte Wappen in die Datenbank importiert werden.


# iconDB

A database for teamlogos

### System requirements
- PHP 7.x
- MySQL

### Installation
On the first call the iinstallation starts automatically. You have to enter the credentials of the database as well as the image types to use.

### Administration
The administration ist accessable through `/adminer/`, no password needed. Might change the directory name.

### Massemport existing logos
By using `/import/dir2basae.php` the images in the directory `/icons/` are inserted into the database.