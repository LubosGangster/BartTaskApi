Backend API, ktoré beži na frameworku Lumen (8.2.4) (Laravel Components ^8.0). Pred spustením je potrebné nainštalovať všetky package, príkazom "composer install".
Spúšťa sa príkazom "php -S localhost:{ľubovoľný dostupný port napr. 8000} public/index.php" v koreňovom priečinku.
Keďže nebolo dovoléne použiť databázu, backend API využíva suborový systém na uloženie potrebných dát v priečinku storage.

Dokumentácia k API:
- vylistovanie všetkých galérii (plus práve jeden obrázok, ak galéria má aspoň jeden obrázok) - GET http://localhost:8000/api/gallery

- vytvorenie novej galérii - POST http://localhost:8000/api/gallery

- vylistovanie všetkých obrázkov danej galérie - GET http://localhost:8000/api/gallery/{path}

- nahratie obrázka - POST http://localhost:8000/api/gallery/{path} ... vo form data je potrebné nahrať daný obrázok s key - "image", 
  taktiež je potrebné pridať autorizačnú hlavičku s tokenom

- vymazanie galérii - DELETE http://localhost:8000/api/gallery/{path} ... vymaže galériu a všetky jej obrázky

- vymazanie obrázka - DELETE http://localhost:8000/api/gallery/{fullPathOfImage} ... vymazanie konkrétneho obrázka, nutné použiť plnú cestu (čiže aj galériu)

- generovanie daného obrázka - GET http://localhost:8000/api/images/{width}x{height}/{fullPathOfImage}

BONUSOVÁ ÚLOHA

- ak používateľ nepridal autorizačnú hlavičku s tokenom, API neuploadne fotku a vráti error status 401

- fotky sa ukladajú na disk, kde k názvu daného obrázka sa pridá používateľove ID napr. obrazok-{ID}.jpg

NIEČO NAVIAC

- get request na vylistovanie galerii som vylepšil o filter pomocou limitu, čiže ak chceme filtrovať galerie potrebný parameter je limit,
 voliteľný parameter je page (defaultne 1, taktiež hodnotu 1 má ak na vstupe API dostane nevalidnú hodnotu page). 
 
 - Napr. ak použijeme GET http://localhost:8000/api/gallery?limit=3&page=1 ..API nám vráti 3 galérie na prvej strane.
