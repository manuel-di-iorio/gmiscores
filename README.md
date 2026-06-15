# GameMaker Italia - Platform Template

Template base per la creazione di nuove aree del sito

## Struttura files

- /includes/layout.php Template base delle pagine
- /lib: Componenti del server
- /pages: Pagine HTML renderizzate dal layout (strutturate in componenti .view e .ctrl)
- /api: API richiamali tramite HTTP Request
- /models: Classi entità del database
- /assets: Files statici (immagini, css)
- Gli altri file php nella root sono le pagine che verranno effettivamente chiamate dal browser

## .htaccess

Nega l'accesso diretto da browser ad alcune sottocartelle e setta il php engine di Altervista.

## Env

Il file env `lib/config.php` non è convidiso sulla repository GitHub, ma puoi trovare l'esempio default sul file `lib/config.example.php
