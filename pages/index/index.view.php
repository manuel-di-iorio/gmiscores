<style>
  /* Hero Section */
  .HomeBanner {
    background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), url(assets/images/banner.webp);
    background-size: cover;
    background-position: bottom center;
    background-attachment: fixed; /* Effetto Parallax */
    width: 100%;
    min-height: 100vh; /* Altezza aumentata */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: white;
    padding: 30px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.4);
    position: relative; /* Per pseudo-elementi se necessari */
  }

  .HomeBanner h1 {
    font-size: 3.5em; /* Titolo ancora più grande */
    font-weight: 700; /* Bold */
    margin-bottom: 0.4em;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5); /* Ombra testo */
    animation: fadeInDown 1s ease-out; /* Animazione titolo */
  }

  .HomeBanner p {
    font-size: 1.3em;
    margin-bottom: 1.8em;
    max-width: 650px;
    line-height: 1.6;
    animation: fadeInUp 1s ease-out 0.3s; /* Animazione paragrafo */
    animation-fill-mode: backwards; /* Assicura che l'elemento sia invisibile prima dell'animazione */
  }

  .CtaButton {
    font-size: 1.3em;
    padding: 18px 35px;
    transition: transform 0.25s ease-out, box-shadow 0.25s ease-out, background-color 0.25s ease-out;
    border-radius: 8px; /* Angoli più arrotondati */
    animation: fadeInUp 1s ease-out 0.6s; /* Animazione pulsante */
    animation-fill-mode: backwards;
  }

  .CtaButton:hover {
    transform: translateY(-4px) scale(1.05); /* Effetto hover più marcato */
    box-shadow: 0 8px 16px rgba(0,0,0,0.3);
  }

  /* Stats Section */
  .StatsSection {
    padding: 60px 20px; /* Più padding */
    background-color: var(--bg-color-offset, #f4f7f6); /* Sfondo leggermente diverso per la sezione */
  }
  
  .StatCardContainer {
  	display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Card leggermente più grandi */
    gap: 25px;
  }
  
  .StatCard {  
    background: var(--bg-color-card, white);
    border-radius: 10px;
    padding: 25px;
    text-align: center;
    box-shadow: var(--shadow-1-subtle, 0 4px 10px rgba(0,0,0,0.08));
    transition: transform 0.25s ease-out, box-shadow 0.25s ease-out, opacity 0.5s ease-out; /* Aggiunta transizione opacità */
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .StatCard:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-2-prominent, 0 8px 20px rgba(0,0,0,0.12));
  }

  .StatCard__Icon {
    font-size: 2.5em;
    color: var(--primary-color, #007bff);
    margin-bottom: 15px;
  }

  .StatCard__Count {
    font-size: 2.6em;
    font-weight: 700;
    color: var(--text-color-headings, #333);
    margin-bottom: 0.2em;
  }

  .StatCard__Label {
    font-size: 1.1em;
    color: var(--text-color-secondary, #555);
    line-height: 1.4;
  }

  /* How It Works Section */
  .HowItWorksSection {
    padding: 60px 20px;
  }

  .HowItWorksContainer {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    margin-top: 40px;
  }

  .HowItWorksStep {
    background: var(--bg-color-card, white);
    border-radius: 10px;
    padding: 30px;
    text-align: center;
    box-shadow: var(--shadow-1-subtle, 0 4px 10px rgba(0,0,0,0.08));
    transition: transform 0.25s ease-out, box-shadow 0.25s ease-out, opacity 0.5s ease-out; /* Aggiunta transizione opacità */
  }
  
  .HowItWorksStep:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-2-prominent, 0 8px 20px rgba(0,0,0,0.12));
  }

  .HowItWorksStep__Icon {
    font-size: 3em;
    color: var(--secondary-color, #6c757d); /* Un colore diverso per questa sezione */
    margin-bottom: 20px;
  }

  .HowItWorksStep h5 {
    font-size: 1.5em;
    font-weight: 600;
    color: var(--text-color-headings, #333);
    margin-bottom: 10px;
  }

  .HowItWorksStep p {
    font-size: 1em;
    color: var(--text-color-secondary, #555);
    line-height: 1.6;
  }

  .SectionTitle {
    text-align: center;
    margin-bottom: 50px; /* Più spazio sotto il titolo */
    font-size: 2.5em; /* Titolo sezione più grande */
    font-weight: 700;
    color: var(--text-color-headings, #333);
    position: relative;
    padding-bottom: 15px; /* Spazio per la "sottolineatura" */
    transition: opacity 0.5s ease-out, transform 0.5s ease-out; /* Aggiunta transizione per animazione JS */
  }

  .SectionTitle::after { /* Sottolineatura decorativa */
    content: '';
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    bottom: 0;
    width: 80px;
    height: 4px;
    background-color: var(--primary-color, #007bff);
    border-radius: 2px;
  }
  
  hr {
    border-top: 1px solid var(--border-color-soft, #e0e0e0);
  }

  /* Animazioni base (da spostare in style.css se usate globalmente) */
  @keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-30px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  /* Features Section */
  .FeaturesSection {
    padding: 60px 20px;
    background-color: var(--bg-color, white); 
  }

  .FeaturesGrid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    margin-top: 40px;
  }

  .FeatureCard {
    background: var(--bg-color-card, #f8f9fa);
    border-radius: 10px;
    padding: 30px;
    text-align: center;
    box-shadow: var(--shadow-1-subtle, 0 4px 10px rgba(0,0,0,0.07));
    transition: transform 0.25s ease-out, box-shadow 0.25s ease-out, opacity 0.5s ease-out;
  }

  .FeatureCard:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-2-prominent, 0 8px 20px rgba(0,0,0,0.1));
  }

  .FeatureCard__Icon {
    font-size: 2.8em;
    color: var(--primary-color, #007bff);
    margin-bottom: 20px;
  }

  .FeatureCard h5 {
    font-size: 1.4em;
    font-weight: 600;
    color: var(--text-color-headings, #333);
    margin-bottom: 10px;
  }

  .FeatureCard p {
    font-size: 0.95em;
    color: var(--text-color-secondary, #555);
    line-height: 1.6;
  }

  /* Testimonials Section */
  .TestimonialsSection {
    padding: 60px 20px;
    background-color: var(--bg-color-offset, #f4f7f6); /* Sfondo diverso per alternanza */
  }

  .TestimonialsGrid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 40px;
  }

  .TestimonialCard {
    background: var(--bg-color-card, white);
    border-radius: 10px;
    padding: 25px;
    box-shadow: var(--shadow-1-subtle, 0 4px 10px rgba(0,0,0,0.08));
    transition: transform 0.25s ease-out, box-shadow 0.25s ease-out, opacity 0.5s ease-out;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .TestimonialCard:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-2-prominent, 0 8px 20px rgba(0,0,0,0.12));
  }

  .TestimonialCard__Avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin-bottom: 15px;
    object-fit: cover;
    border: 3px solid var(--primary-color-light, #e0f0ff);
  }

  .TestimonialCard__Quote {
    font-style: italic;
    color: var(--text-color-secondary, #555);
    margin-bottom: 15px;
    font-size: 1em;
    line-height: 1.5;
  }

  .TestimonialCard__Quote::before {
    content: '\201C'; /* Virgoletta sinistra */
    font-size: 1.5em;
    color: var(--primary-color, #007bff);
    margin-right: 5px;
    line-height: 0;
  }
  .TestimonialCard__Quote::after {
    content: '\201D'; /* Virgoletta destra */
    font-size: 1.5em;
    color: var(--primary-color, #007bff);
    margin-left: 5px;
    line-height: 0;
  }

  .TestimonialCard__Author {
    font-weight: 600;
    color: var(--text-color-headings, #333);
    font-size: 0.9em;
  }

  /* Final CTA Section */
  .FinalCtaSection {
    background-color: var(--bg-color-offset, #f4f7f6); /* Sfondo coordinato */
    padding: 80px 30px; /* Padding aumentato */
    text-align: center;
    border-top: 1px solid var(--border-color-soft, #e0e0e0); /* Separatore superiore */
    border-bottom: 1px solid var(--border-color-soft, #e0e0e0); /* Separatore inferiore per simmetria */
  }

  .FinalCtaSection h4 {
    font-size: 2.6em; /* Dimensione titolo ulteriormente aumentata */
    font-weight: 700;
    color: var(--text-color-headings, #333);
    margin-bottom: 25px; /* Margine inferiore aumentato */
  }

  .FinalCtaSection p {
    font-size: 1.3em; /* Dimensione paragrafo aumentata */
    color: var(--text-color-secondary, #555);
    max-width: 800px; /* Larghezza massima aumentata */
    margin-left: auto;
    margin-right: auto;
    margin-bottom: 40px; /* Spazio prima del pulsante aumentato */
    line-height: 1.7;
  }

  .FinalCtaSection .CtaButton {
    background-color: var(--primary-color, #007bff); /* Colore primario per il pulsante */
    color: white;
    font-size: 1.35em; /* Dimensione font pulsante aumentata */
    padding: 20px 45px; /* Padding pulsante aumentato */
    border: none; /* Rimuove bordo se presente da w3-button */
    border-radius: 8px; /* Coerenza con altri CtaButton */
    letter-spacing: 0.5px; /* Spaziatura lettere per rifinitura */
    /* Le transizioni e animazioni sono ereditate da .CtaButton, se necessario si possono specificare qui */
  }

  .FinalCtaSection .CtaButton:hover {
    background-color: var(--primary-color-dark, #0056b3); /* Scurisce al hover */
    transform: translateY(-5px) scale(1.05); /* Effetto hover più marcato */
    box-shadow: 0 10px 20px rgba(0,0,0,0.3); /* Ombra hover più marcata */
  }

  /* Scroll Down Arrow for HomeBanner */
  .scroll-down-arrow {
    position: absolute;
    bottom: 40px; /* Aumentato per più spazio dal fondo */
    left: 50%;
    transform: translateX(-50%);
    font-size: 2.5em; /* Leggermente più grande */
    color: white;
    animation: bounceUpDown 4s infinite ease-in-out; /* Animazione più fluida */
    cursor: pointer; /* Indica che è cliccabile, anche se non fa nulla */
    opacity: 0.8;
    transition: opacity 0.3s ease;
  }

  .scroll-down-arrow:hover {
    opacity: 1;
  }

  @keyframes bounceUpDown {
    0%, 100% {
      transform: translateX(-50%) translateY(0);
    }
    50% {
      transform: translateX(-50%) translateY(-15px); /* Escursione ridotta per un effetto più soft */
    }
  }

  /* Fixed Logo */
  .fixed-logo {
    position: absolute;
    top: 40px;
    left: 40px;
    width: 200px; /* Puoi aggiustare la dimensione come preferisci */
    height: auto;
    z-index: 1000; /* Assicura che sia sopra gli altri elementi */
    pointer-events: none;
  }

  /* Visual Showcase Section */
  .VisualShowcaseSection {
    padding: 70px 30px;
    background-color: var(--bg-color, white); /* O un colore leggermente diverso per staccare */
  }

  .VisualShowcaseContainer {
    display: flex;
    align-items: center;
    gap: 40px;
    max-width: 1200px;
    margin: 0 auto;
  }

  .VisualShowcase__Text {
    flex: 1;
  }

  .VisualShowcase__Text h3 {
    font-size: 2.2em;
    font-weight: 700;
    color: var(--text-color-headings, #333);
    margin-bottom: 20px;
  }

  .VisualShowcase__Text p {
    font-size: 1.15em;
    color: var(--text-color-secondary, #555);
    line-height: 1.7;
    margin-bottom: 25px;
  }

  .VisualShowcase__Image img {
    flex: 1;
    text-align: center;
    max-height: 600px; /* Limita l'altezza per evitare overflow */
  }

  .VisualShowcase__Image img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    box-shadow: var(--shadow-2-prominent, 0 8px 25px rgba(0,0,0,0.15));
  }

  /* Responsive adjustments for VisualShowcaseSection */
  @media (max-width: 768px) {
    .VisualShowcaseContainer {
      flex-direction: column;
      text-align: center;
    }
    .VisualShowcase__Text h3 {
      font-size: 1.8em;
    }
    .VisualShowcase__Text p {
      font-size: 1em;
    }
    .VisualShowcase__Image {
      margin-top: 30px;
    }
  }
</style>

<!-- <img src="assets/images/logo-transparent.webp" alt="Logo" class="fixed-logo"> -->

<div class="HomeBanner">
  <h1><?= htmlspecialchars($config["platformTitle"]) ?></h1>
  <p>Integra classifiche online nei tuoi giochi GameMaker in modo semplice, veloce e gratuito. Dai una marcia in più alle tue creazioni!</p>
  <a href="./add-game.php">
    <button type="submit" class="w3-button w3-white w3-padding-large w3-margin-top w3-margin-bottom CtaButton">
      <i class="fas fa-rocket w3-margin-right"></i> Inizia subito
    </button>
  </a>
  <div class="scroll-down-arrow">
    <a href="#how-it-works-section-anchor"><i class="fas fa-chevron-down"></i></a>
  </div>
</div>

<div id="how-it-works-section-anchor"></div>
<!-- How It Works Section -->
<hr class="fade-in-up-on-scroll"/>
<div class="w3-container HowItWorksSection">
  <h2 class="SectionTitle fade-in-up-on-scroll">Come Funziona? È Semplice!</h2>
  <divù class="HowItWorksContainer">
    <div class="HowItWorksStep fade-in-up-on-scroll">
      <div class="HowItWorksStep__Icon"><i class="fas fa-gamepad"></i></div>
      <h5>1. Registra il Tuo Gioco</h5>
      <p>Aggiungi il tuo gioco sulla nostra piattaforma. Riceverai un ID univoco e una chiave segreta.</p>
    </div>
    <div class="HowItWorksStep fade-in-up-on-scroll">
      <div class="HowItWorksStep__Icon"><i class="fas fa-code-branch"></i></div>
      <h5>2. Integra l'API</h5>
      <p>Utilizza le nostre semplici chiamate HTTP per inviare e recuperare i punteggi direttamente da GameMaker.</p>
    </div>
    <div class="HowItWorksStep fade-in-up-on-scroll">
      <div class="HowItWorksStep__Icon"><i class="fas fa-trophy"></i></div>
      <h5>3. Classifiche Globali</h5>
      <p>I punteggi dei tuoi giocatori saranno visibili in classifiche globali, pronti per la competizione!</p>
    </div>
     <div class="HowItWorksStep fade-in-up-on-scroll">
      <div class="HowItWorksStep__Icon"><i class="fas fa-shield-alt"></i></div>
      <h5>4. Sicurezza e Gestione</h5>
      <p>Gestisci i tuoi giochi, visualizza statistiche e banna giocatori scorretti dalla tua dashboard.</p>
    </div>
  </div>
</div>

<!-- Visual Showcase Section -->
<hr class="fade-in-up-on-scroll"/>
<div class="w3-container VisualShowcaseSection">
  <div class="VisualShowcaseContainer">
    <div class="VisualShowcase__Text fade-in-up-on-scroll">
      <h2 class="SectionTitle" style="text-align: left; margin-bottom: 30px; padding-bottom: 10px;">Dai Vita alle Tue Sfide</h2>
      <p>Immagina i tuoi giocatori scalare le vette delle classifiche, condividere i loro trionfi e sentirsi parte di una community globale. Con la nostra piattaforma, trasformi ogni partita in un'epica competizione. Offri un'esperienza che va oltre il gioco stesso, creando momenti indimenticabili e legami duraturi tra i tuoi utenti.</p>
      <a href="./add-game.php" class="CtaButton w3-button w3-black" style="margin-top: 20px">Aggiungi il tuo gioco</a>
    </div>
    <div class="VisualShowcase__Image fade-in-up-on-scroll">
      <img src="/assets/images/landing_leaderboard.avif" alt="Visualizzazione Classifiche">
    </div>
  </div>
</div>

<!-- Why Choose Us Section -->
<hr class="fade-in-up-on-scroll"/>
<div class="w3-container FeaturesSection">
  <h2 class="SectionTitle fade-in-up-on-scroll">Perché Sceglierci?</h2>
  <div class="FeaturesGrid">
    <div class="FeatureCard fade-in-up-on-scroll">
      <div class="FeatureCard__Icon"><i class="fas fa-cogs"></i></div>
      <h5>Integrazione Semplice</h5>
      <p>API facili da usare per un'integrazione rapida nei tuoi giochi GameMaker.</p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll">
      <div class="FeatureCard__Icon"><i class="fas fa-gift"></i></div>
      <h5>Gratuito per Sempre</h5>
      <p>Nessun costo nascosto. Offri classifiche online senza pensieri.</p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll">
      <div class="FeatureCard__Icon"><i class="fas fa-shield-alt"></i></div>
      <h5>Sicuro e Affidabile</h5>
      <p>Piattaforma stabile con protezione anti-cheat di base.</p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll">
      <div class="FeatureCard__Icon"><i class="fas fa-server"></i></div>
      <h5>Nessun Server Richiesto</h5>
      <p>Gestiamo noi l'hosting delle classifiche, tu concentrati sul gioco.</p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll">
      <div class="FeatureCard__Icon"><i class="fas fa-users-cog"></i></div>
      <h5>Gestione Facilitata</h5>
      <p>Dashboard intuitiva per gestire giochi, punteggi e ban.</p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll">
      <div class="FeatureCard__Icon"><i class="fas fa-users"></i></div>
      <h5>Supporto Comunitario</h5>
      <p>Entra nella nostra community Discord per supporto, idee e collaborazione.</p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll">
      <div class="FeatureCard__Icon"><i class="fab fa-github"></i></div>
      <h5>Open Source e Trasparente</h5>
      <p>Il codice sorgente è disponibile su <a href="https://github.com/manuel-di-iorio/gmiscores">GitHub</a>. Contribuisci o adatta la piattaforma.</p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll">
      <div class="FeatureCard__Icon"><i class="fas fa-rocket"></i></div>
      <h5>Evoluzione Continua</h5>
      <p>Siamo costantemente al lavoro per aggiungere nuove funzionalità e migliorare la piattaforma.</p>
    </div>
  </div>
</div>

<!-- Stats -->
<div class="w3-container StatsSection">
  <h2 class="SectionTitle fade-in-up-on-scroll">La Nostra Community in Numeri</h2>
  <div class="StatCardContainer">
    <?php 
      $statIcons = [
        "scores" => "fas fa-star",
        "players" => "fas fa-users",
        "games" => "fas fa-gamepad",
        "active-games" => "fas fa-bolt",
        "top-game" => "fas fa-trophy",
        "dev-with-more-games" => "fas fa-user-astronaut",
        "unique-scores-countries" => "fas fa-globe-americas",
        "users" => "fas fa-code"
      ];
    ?>
    <?php foreach ($stats as $key => $stat) { ?>
    <div class="StatCard fade-in-up-on-scroll">
      <div class="StatCard__Icon"><i class="<?= $statIcons[$key] ?? 'fas fa-chart-bar' ?>"></i></div>
      <div class="StatCard__Count"><?= htmlspecialchars($stat["count"]) ?></div>
      <div class="StatCard__Label"><?= htmlspecialchars($stat["label"]) ?></div>
    </div>
    <?php } ?>
  </div>
</div>

<!-- Testimonials Section -->
<hr class="fade-in-up-on-scroll"/>
<div class="w3-container TestimonialsSection">
  <h2 class="SectionTitle fade-in-up-on-scroll">Dicono di Noi</h2>
  <div class="TestimonialsGrid">
    <div class="TestimonialCard fade-in-up-on-scroll">
      <img src="https://i.pravatar.cc/100?u=dev1" alt="Avatar sviluppatore" class="TestimonialCard__Avatar">
      <p class="TestimonialCard__Quote">"Implementare le classifiche è stato un gioco da ragazzi! I miei giocatori adorano la competizione."</p>
      <p class="TestimonialCard__Author">- Alex Rossi, Sviluppatore di 'Space Raiders'</p>
    </div>
    <div class="TestimonialCard fade-in-up-on-scroll">
      <img src="https://i.pravatar.cc/100?u=dev2" alt="Avatar sviluppatore" class="TestimonialCard__Avatar">
      <p class="TestimonialCard__Quote">"Finalmente una soluzione semplice e gratuita per le leaderboard. Consigliatissimo!"</p>
      <p class="TestimonialCard__Author">- Giulia Bianchi, Sviluppatrice di 'Pixel Jumper'</p>
    </div>
    <div class="TestimonialCard fade-in-up-on-scroll">
      <img src="https://i.pravatar.cc/100?u=dev3" alt="Avatar sviluppatore" class="TestimonialCard__Avatar">
      <p class="TestimonialCard__Quote">"La dashboard è intuitiva e la gestione dei punteggi è ottima. Un servizio eccellente."</p>
      <p class="TestimonialCard__Author">- Marco Verdi, Sviluppatore di 'Fantasy Quest RPG'</p>
    </div>
  </div>
</div>
  
<hr class="fade-in-up-on-scroll"/>

<div class="w3-container w3-center fade-in-up-on-scroll FinalCtaSection">
  <h4 class="fade-in-up-on-scroll"><strong>Pronto a portare i tuoi giochi al livello successivo?</strong></h4>
  <p class="fade-in-up-on-scroll">Non aspettare! Unisciti alla nostra community di sviluppatori e offri ai tuoi giocatori un'esperienza competitiva e coinvolgente.</p>
  <a href="./add-game.php" class="fade-in-up-on-scroll">
    <button type="submit" class="w3-button w3-padding-large CtaButton w3-black">
      Aggiungi il tuo gioco
    </button>
  </a>
</div>
