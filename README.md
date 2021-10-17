Backend API, ktoré beži na frameworku Lumen (8.2.4) (Laravel Components ^8.0).
Spúšťa sa príkazom "php -S localhost:{ľubovoľný dostupný port napr. 8000} public/index.php" v koreňovom priečinku.
Keďže nebolo dovoléne použiť databázu, backend API využíva suborový systém na uloženie potrebných dát v priečinku storage.

Dokumentácia k API:
-vylistovanie všetkých galérii (plus práve jeden obrázok, ak galéria má aspoň jeden obrázok) - GET http://localhost:8000/api/gallery

-vytvorenie novej galérii - POST http://localhost:8000/api/gallery

-vylistovanie všetkých obrázkov danej galérie - GET http://localhost:8000/api/gallery/{path}

-nahratie obrázka - POST http://localhost:8000/api/gallery/{path} ... vo form data je potrebné nahrať daný obrázok s key - "image"

-vymazanie galérii - DELETE http://localhost:8000/api/gallery/{path} ... vymaže galériu a všetky jej obrázky

-vymazanie obrázka - DELETE http://localhost:8000/api/gallery/{fullPathOfImage} ... vymazanie konkrétneho obrázka, nutné použiť plnú cestu (čiže aj galériu)

-generovanie daného obrázka - GET http://localhost:8000/api/images/{width}x{height}/{fullPathOfImage}
