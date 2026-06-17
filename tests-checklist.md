===============
TESTS CHECKLIST
===============

- HOME (Landing)
  1) Si vede sia da utente anonimo che da loggato
  2) Il link "Documentazione" porta alla pagina corrispondente
  3) Il pulsante "Aggiungi il tuo gioco" porta alla pagina corrispondente
  4) Se loggato, "Inizia subito" porta a /home.php (dashboard)
  5) Se anonimo, "Inizia subito" porta a /add-game.php

- DASHBOARD (/home.php)
  1) Non si vede da utente anonimo (redirect a index)
  2) Mostra 4 stat card: Punteggi, Players, Giochi, Paesi
  3) Mostra grafico linee punteggi ultimi 30gg
  4) Mostra grafico barre punteggi per gioco
  5) Mostra grafico doughnut paesi
  6) Le stat card si aggiornano se ci sono nuovi punteggi
  7) I grafici hanno altezza uniforme (360px)
  8) Il redirect post-login arriva a /home.php

- DOCUMENTAZIONE
  1) Si vede sia da utente anonimo che da loggato
  2) Il link di creazione gioco nella nota porta alla pagina corrispondente
  3) Tutte le accordion si possono aprire e chiudere

- SIDEBAR
  1) Le voci di menu portano alle pagine corrispondenti
  2) Le voci di menu si colorano se corrispondono alla pagina attuale
  3) Se loggato, si vede icona e nome utente sotto le voci di menu 
  4) Logout funziona
  5) Il pulsante Dark theme si può disattivare/attivare

- LOGIN AND APPROVAL
  1) Login funziona e ti porta alla pagina precedente
  2) Per nuovi utenti, si viene reindirizzati alla pagina di Attesa approvazione

- GAMES
  1) Non si vede da utente anonimo
  2) Pulsante "Aggiungi gioco" porta alla pagina corrispondente
  3) Se non ci sono giochi, la tabella non esce
  4) Tooltips sui pulsanti-icone della tabella si vedono
  5) Paginazione funziona correttamente
  6) Pulsante "Mostra giocatori bannati" nella tabella porta alla pagina corrispondente
  7) Pulsante "cancella gioco" mostra modal e la cancellazione funziona

- ADD GAME
  1) Non si vede da utente anonimo
  2) Validazione su input funziona
  3) ReCaptcha è visibile
  4) Creazione gioco funziona

- GAME PAGE
  1) Non si vede da utente anonimo
  2) Il secret si può vedere e il tooltip sull'icona è presente
  3) Il pulsante "Documentazione" porta alla pagina corrispondente
  4) Negli esempi di integrazione l'ID del gioco viene stampato correttamente
  5) I tab "Configurazione" e "Analytics" sono presenti
  6) Il tab "Analytics" mostra stat card, line chart (30gg), bar chart, doughnut
  7) Cambiando tab, il bordo colorato si sposta sul tab attivo

- LEADERBOARD (ADD)
  1) Non si vede da utente anonimo
  2) La checkbox "Classifica privata" è spuntata di default (is_private = true)
  3) Creazione leaderboard funziona

- LEADERBOARD (EDIT)
  1) Non si vede da utente anonimo
  2) La checkbox "Classifica privata" è spuntata di default se nessun valore preesistente
  3) Se la leaderboard era pubblica (is_private = 0), la checkbox NON è spuntata
  4) Salvataggio modifiche funziona

- SCORES
  1) Non si vede da utente anonimo
  2) Esce un messaggio se non ci sono punteggi
  3) Il filtro "Ambiente" è impostato su "Produzione" di default (non più "Tutti")
  4) La chip dell'ambiente ha padding e bordi arrotondati
  5) Checkbox e chip ambiente sono allineati verticalmente al centro
  6) Il pulsante "Elimina selezionati" ha lo stesso stile degli altri pulsanti (bordi arrotondati)
  7) Selezionando "Seleziona tutto" compare il pulsante "Elimina selezionati"
  8) Elimina selezionati elimina solo i punteggi dei giochi di proprietà (INNER JOIN)
  9) Pulsante "cancella punteggio" mostra modal e la cancellazione funziona
  10) Pulsante "ban giocatore" mostra modal e il ban funziona
  11) Tooltips sui pulsanti-icone della tabella si vedono
  12) Pulsante Esporta scarica i dati completi della tabella
  13) Pulsante Importa un file esportato funziona
  14) Pulsante Cancella tutti i punteggi funziona
  15) Il pulsante "Back" vicino al nome del gioco porta alla pagina corrispondente
  16) Il pulsante "Azzera" filtri mantiene leaderboard_id (non rimanda alla lista classifiche)
  17) Nella modale inserimento, il campo "Ambiente" ha margine superiore
  18) Doppio click sul submit della modale non invia richieste duplicate

- MIGRATION SYSTEM
  1) `/migrate.php` è accessibile solo a utenti admin
  2) La tabella `migrations` viene creata automaticamente
  3) Le migrazioni pendenti vengono elencate con stato "In attesa"
  4) Cliccando "Esegui" si applicano le migrazioni in ordine
  5) Le migrazioni applicate mostrano stato "Applicata" e data
  6) In caso di errore su una migration, le successive non vengono bloccate

- 404 PAGE
  1) Visitando una URL inesistente si vede la pagina 404 personalizzata
  2) La pagina 404 mostra il codice e un link "Torna alla home"
  3) Viene restituito HTTP 404

- BANS
  1) Non si vede da utente anonimo
  3) Se non ci sono ban, esce un messaggio invece che la tabella
  4) Tooltips sui pulsanti-icone della tabella si vedono
  5) Paginazione funziona correttamente [@TODO]
  6) Pulsante "rimuovi ban" mostra modal e la rimozione funziona
  7) Il pulsante "Back" vicino al nome del gioco porta alla pagina corrispondente
