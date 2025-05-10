===============
TESTS CHECKLIST
===============

- HOME
  1) Si vede sia da utente anonimo che da loggato
  2) Il link "Documentazione" porta alla pagina corrispondente
  3) Il pulsante "Aggiungi il tuo gioco" porta alla pagina corrispondente

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
  5) Paginazione funziona correttamente [@TODO]
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

- SCORES
  1) Non si vede da utente anonimo
  2) Esce un messaggio se non ci sono giochi
  3) Pulsante "cancella punteggio" mostra modal e la cancellazione funziona
  4) Pulsante "ban giocatore" mostra modal e il ban funziona
  5) Tooltips sui pulsanti-icone della tabella si vedono
  6) Pulsante Esporta scarica i dati completi della tabella
  7) Pulsante Reimporta un file esportato
  8) Pulsante Cancella tutti i punteggi funziona
  9) Il pulsante "Back" vicino al nome del gioco porta alla pagina corrispondente

- BANS
  1) Non si vede da utente anonimo
  3) Se non ci sono ban, esce un messaggio invece che la tabella
  4) Tooltips sui pulsanti-icone della tabella si vedono
  5) Paginazione funziona correttamente [@TODO]
  6) Pulsante "rimuovi ban" mostra modal e la rimozione funziona
  7) Il pulsante "Back" vicino al nome del gioco porta alla pagina corrispondente
