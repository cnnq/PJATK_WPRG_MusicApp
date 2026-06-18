Repozytorium zawiera pliki aplikacji webowej napisanej w PHP.
Aplikacja umożliwia publikowanie, ocenianie i słuchanie muzyki.

## Pobranie repozytorium
Repozytorium klonujemy.
- Używając PhpStorm klikamy "Project from verion control..." i wpisujemy adres repozytorium.
- Lub przy pomocy terminala:
```
$ git clone https://github.com/cnnq/PJATK_WPRG_MusicApp
```

### Import bazy danych
Aby aplikacja mogła działać należy ręcznie zaimportować zrzut bazy danych.
W folderu `database` znajdują się eksporty poszczególnych kolumn.

Projekt był robiony przy użyciu nażędzia [MySQL Workbench](https://www.mysql.com/products/workbench/).
Aby zaimportować pliki w MySQL Workbenchu należy:
- Stworzyć nowy model:
  Klikamy "File" -> "New model"
- Zaimportować poszczególne kolumny
  "File" -> "Import" -> "Reverse Engineer MySQL Create Script..."
  Wybieramy plik i importujemy. Robimy to dla każdego pliku
