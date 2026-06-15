<style>
  .HomeBanner {
    background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), url(assets/images/banner.webp);
    background-size: cover;
    background-position: bottom center;
    width: 100%;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: white;
    padding: 30px;
    position: relative;
    overflow: hidden;
  }

  .HomeBanner h1 {
    font-size: clamp(2.2rem, 5vw, 4rem);
    font-weight: 800;
    margin-bottom: 0.5em;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
    position: relative;
    z-index: 2;
  }

  .HomeBanner .hero-subtitle {
    font-size: clamp(1rem, 2vw, 1.3rem);
    margin-bottom: 2em;
    max-width: 600px;
    line-height: 1.7;
    position: relative;
    z-index: 2;
    opacity: 0.9;
  }

  .CtaButton {
    font-size: 1.15em;
    padding: 16px 36px;
    border-radius: 12px;
    font-weight: 600;
    letter-spacing: 0.3px;
    cursor: pointer;
  }

  .theme-toggle-home {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1.3rem;
    color: white;
    transition: background 0.2s ease, transform 0.2s ease;
    z-index: 10000;
  }
  .theme-toggle-home:hover {
    background: rgba(255,255,255,0.25);
    transform: scale(1.1);
  }

  .scroll-down-arrow {
    position: absolute;
    bottom: 36px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 2em;
    color: white;
    animation: bounceUpDown 3s ease-in-out infinite;
    opacity: 0.7;
    transition: opacity 0.3s ease;
    z-index: 10;
  }
  .scroll-down-arrow:hover { opacity: 1; }

  @keyframes bounceUpDown {
    0%, 100% { transform: translateX(-50%) translateY(0); }
    50% { transform: translateX(-50%) translateY(-12px); }
  }

  .StatsSection {
    padding: 80px 20px;
    background-color: var(--bg-color-offset, #f4f7f6);
  }

  .StatCardContainer {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
  }

  .StatCard {
    background: var(--glass-bg, rgba(255,255,255,0.08));
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--glass-border, rgba(255,255,255,0.12));
    border-radius: 16px;
    padding: 28px 20px;
    text-align: center;
    transition: transform 0.3s cubic-bezier(0.16,1,0.3,1), box-shadow 0.3s cubic-bezier(0.16,1,0.3,1);
  }

  .StatCard:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 32px rgba(0,0,0,0.1);
  }

  .StatCard__Icon {
    font-size: 2.2em;
    color: var(--primary-color, #6366f1);
    margin-bottom: 12px;
  }

  .StatCard__Count {
    font-size: 2.4em;
    font-weight: 800;
    color: var(--text-color-headings, #333);
    margin-bottom: 0.15em;
    font-variant-numeric: tabular-nums;
  }

  .StatCard__Label {
    font-size: 0.95em;
    color: var(--text-color-secondary, #555);
    line-height: 1.4;
  }

  .HowItWorksSection {
    padding: 80px 20px;
  }

  .HowItWorksContainer {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
    margin-top: 40px;
  }

  .HowItWorksStep {
    background: var(--glass-bg, rgba(255,255,255,0.08));
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--glass-border, rgba(255,255,255,0.12));
    border-radius: 16px;
    padding: 32px 24px;
    text-align: center;
    transition: transform 0.3s cubic-bezier(0.16,1,0.3,1), box-shadow 0.3s cubic-bezier(0.16,1,0.3,1);
  }

  .HowItWorksStep:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 32px rgba(0,0,0,0.1);
  }

  .HowItWorksStep__Icon {
    font-size: 2.6em;
    color: var(--gradient-end, #ec4899);
    margin-bottom: 16px;
  }

  .HowItWorksStep h5 {
    font-size: 1.3em;
    font-weight: 700;
    color: var(--text-color-headings, #333);
    margin-bottom: 10px;
  }

  .HowItWorksStep p {
    font-size: 0.95em;
    color: var(--text-color-secondary, #555);
    line-height: 1.6;
  }

  .SectionTitle {
    text-align: center;
    margin-bottom: 40px;
    font-size: clamp(1.6rem, 3.5vw, 2.5rem);
    font-weight: 800;
    color: var(--text-color-headings, #333);
    position: relative;
    padding-bottom: 16px;
  }

  .SectionTitle::after {
    content: '';
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    bottom: 0;
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, var(--gradient-start, #6366f1), var(--gradient-end, #ec4899));
    border-radius: 2px;
  }

  .FeaturesSection {
    padding: 80px 20px;
    background-color: var(--bg-color, white);
  }

  .FeaturesGrid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 40px;
  }

  .FeatureCard {
    background: var(--glass-bg, rgba(255,255,255,0.08));
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--glass-border, rgba(255,255,255,0.12));
    border-radius: 16px;
    padding: 28px 24px;
    text-align: center;
    transition: transform 0.3s cubic-bezier(0.16,1,0.3,1), box-shadow 0.3s cubic-bezier(0.16,1,0.3,1);
  }

  .FeatureCard:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 32px rgba(0,0,0,0.1);
  }

  .FeatureCard__Icon {
    font-size: 2.4em;
    color: var(--primary-color, #6366f1);
    margin-bottom: 16px;
  }

  .FeatureCard h5 {
    font-size: 1.2em;
    font-weight: 700;
    color: var(--text-color-headings, #333);
    margin-bottom: 8px;
  }

  .FeatureCard p {
    font-size: 0.9em;
    color: var(--text-color-secondary, #555);
    line-height: 1.6;
  }

  .TestimonialsSection {
    padding: 80px 20px;
    background-color: var(--bg-color-offset, #f4f7f6);
  }

  .TestimonialsGrid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-top: 40px;
  }

  .TestimonialCard {
    background: var(--glass-bg, rgba(255,255,255,0.08));
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--glass-border, rgba(255,255,255,0.12));
    border-radius: 16px;
    padding: 28px 24px;
    text-align: center;
    transition: transform 0.3s cubic-bezier(0.16,1,0.3,1), box-shadow 0.3s cubic-bezier(0.16,1,0.3,1);
  }

  .TestimonialCard:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 32px rgba(0,0,0,0.1);
  }

  .TestimonialCard__Avatar {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    margin-bottom: 16px;
    object-fit: cover;
    border: 3px solid var(--primary-color-light, rgba(99,102,241,0.3));
  }

  .TestimonialCard__Quote {
    font-style: italic;
    color: var(--text-color-secondary, #555);
    margin-bottom: 12px;
    font-size: 0.95em;
    line-height: 1.6;
  }

  .TestimonialCard__Quote::before { content: '\201C'; font-size: 1.4em; color: var(--primary-color, #6366f1); margin-right: 4px; line-height: 0; }
  .TestimonialCard__Quote::after { content: '\201D'; font-size: 1.4em; color: var(--primary-color, #6366f1); margin-left: 4px; line-height: 0; }

  .TestimonialCard__Author {
    font-weight: 600;
    color: var(--text-color-headings, #333);
    font-size: 0.85em;
  }

  .FinalCtaSection {
    padding: 100px 30px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .FinalCtaSection::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--gradient-start, #6366f1), var(--gradient-end, #ec4899));
    opacity: 0.05;
    pointer-events: none;
  }

  .FinalCtaSection h4 {
    font-size: clamp(1.5rem, 3vw, 2.4rem);
    font-weight: 800;
    color: var(--text-color-headings, #333);
    margin-bottom: 20px;
    position: relative;
  }

  .FinalCtaSection p {
    font-size: 1.1em;
    color: var(--text-color-secondary, #555);
    max-width: 650px;
    margin: 0 auto 36px;
    line-height: 1.7;
    position: relative;
  }

  .FinalCtaSection .CtaButton {
    background: linear-gradient(135deg, var(--gradient-start, #6366f1), var(--gradient-end, #ec4899)) !important;
    color: white !important;
    border: none;
    position: relative;
  }

  .FinalCtaSection .CtaButton:hover {
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 12px 28px rgba(var(--primary-color-rgb, 99,102,241), 0.35);
  }

  .VisualShowcaseSection {
    padding: 80px 20px;
    background-color: var(--bg-color, white);
  }

  .VisualShowcaseContainer {
    display: flex;
    align-items: center;
    gap: 50px;
    max-width: 1100px;
    margin: 0 auto;
  }

  .VisualShowcase__Text { flex: 1; }

  .VisualShowcase__Text h3 {
    font-size: clamp(1.3rem, 2.5vw, 1.8rem);
    font-weight: 700;
    color: var(--text-color-headings, #333);
    margin-bottom: 16px;
  }

  .VisualShowcase__Text p {
    font-size: 1.05em;
    color: var(--text-color-secondary, #555);
    line-height: 1.7;
    margin-bottom: 24px;
  }

  .VisualShowcase__Image {
    flex: 1;
    text-align: center;
  }

  .VisualShowcase__Image img {
    max-width: 100%;
    height: auto;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
  }

  @media (max-width: 768px) {
    .HomeBanner { padding: 80px 20px; }
    .scroll-down-arrow { bottom: 50px; }
    .StatsSection, .HowItWorksSection, .FeaturesSection,
    .TestimonialsSection, .VisualShowcaseSection { padding: 50px 16px; }
    .FinalCtaSection { padding: 60px 20px; }
    .VisualShowcaseContainer { flex-direction: column; text-align: center; }
    .VisualShowcase__Image { margin-top: 30px; }
    .StatCardContainer { gap: 16px; }
  }
</style>

<!-- ===== STICKY HEADER ===== -->
<header class="landing-header" role="banner">
  <a href="./index.php" class="header-logo">
    <img src="assets/images/logoSmall.png" alt="Logo">
    <span><?= htmlspecialchars($config["platformTitle"]) ?></span>
  </a>
  <nav class="header-nav">
    <a href="#come-funziona">Come funziona</a>
    <a href="#caratteristiche">Caratteristiche</a>
    <a href="#numeri">Numeri</a>
    <a href="#contatti">Contatti</a>
    <a href="./add-game.php" class="header-cta">Inizia subito</a>
  </nav>
</header>

<!-- ===== HERO SECTION ===== -->
<div class="HomeBanner">
  <div id="hero-particles"></div>

  <div class="hero-floating-shape"></div>
  <div class="hero-floating-shape"></div>
  <div class="hero-floating-shape"></div>

  <div class="theme-toggle-home" onclick="switchThemeHome()" title="Cambia tema">
    <i class="fas <?= $theme === 'dark' ? 'fa-sun' : 'fa-moon' ?>"></i>
  </div>

  <h1 class="anim-fade-up"><?= htmlspecialchars($config["platformTitle"]) ?></h1>
  <p class="hero-subtitle anim-fade-up anim-delay-200">
    Integra classifiche online nei tuoi giochi GameMaker in modo semplice, veloce e gratuito. Dai una marcia in più alle tue creazioni!
  </p>
  <a href="./add-game.php" class="anim-fade-up anim-delay-400">
    <button type="submit" class="w3-button w3-padding-large CtaButton btn-glow" style="background:white;color:black;">
      <i class="fas fa-rocket w3-margin-right"></i> Inizia subito
    </button>
  </a>

  <div class="scroll-down-arrow anim-fade-up anim-delay-600">
    <a href="#come-funziona"><i class="fas fa-chevron-down"></i></a>
  </div>
</div>

<!-- ===== HOW IT WORKS ===== -->
<div id="come-funziona" class="w3-container HowItWorksSection">
  <h2 class="SectionTitle fade-in-up-on-scroll">Come funziona? <span class="gradient-text">È semplice!</span></h2>
  <div class="HowItWorksContainer stagger-grid">
    <div class="HowItWorksStep stagger-item">
      <div class="HowItWorksStep__Icon"><i class="fas fa-gamepad"></i></div>
      <h5>1. Registra il tuo gioco</h5>
      <p>Aggiungi il tuo gioco sulla nostra piattaforma. Riceverai un ID univoco e una chiave segreta.</p>
    </div>
    <div class="HowItWorksStep stagger-item">
      <div class="HowItWorksStep__Icon"><i class="fas fa-code-branch"></i></div>
      <h5>2. Integra l'API</h5>
      <p>Utilizza le nostre semplici chiamate HTTP per inviare e recuperare i punteggi direttamente da GameMaker.</p>
    </div>
    <div class="HowItWorksStep stagger-item">
      <div class="HowItWorksStep__Icon"><i class="fas fa-trophy"></i></div>
      <h5>3. Classifiche globali</h5>
      <p>I punteggi dei tuoi giocatori saranno visibili in classifiche globali, pronti per la competizione!</p>
    </div>
    <div class="HowItWorksStep stagger-item">
      <div class="HowItWorksStep__Icon"><i class="fas fa-shield-alt"></i></div>
      <h5>4. Sicurezza e gestione</h5>
      <p>Gestisci i tuoi giochi, visualizza statistiche e banna giocatori scorretti dalla tua dashboard.</p>
    </div>
  </div>
</div>

<!-- ===== VISUAL SHOWCASE ===== -->
<div class="w3-container VisualShowcaseSection">
  <div class="VisualShowcaseContainer">
    <div class="VisualShowcase__Text anim-fade-left">
      <h2 class="SectionTitle" style="text-align:left;margin-bottom:24px;padding-bottom:12px;">
        Dai vita alle <span class="gradient-text">tue sfide</span>
      </h2>
      <p>Immagina i tuoi giocatori scalare le vette delle classifiche, condividere i loro trionfi e sentirsi parte di una community globale. Con la nostra piattaforma, trasformi ogni partita in un'epica competizione.</p>
      <a href="./add-game.php" class="CtaButton w3-button btn-glow" style="margin-top:12px;background:var(--primary-color);color:white">
        Aggiungi il tuo gioco
      </a>
    </div>
    <div class="VisualShowcase__Image anim-fade-right">
      <img src="/assets/images/landing_leaderboard.avif" alt="Visualizzazione Classifiche" loading="lazy">
    </div>
  </div>
</div>

<!-- ===== FEATURES ===== -->
<div id="caratteristiche" class="w3-container FeaturesSection">
  <h2 class="SectionTitle fade-in-up-on-scroll">Perché <span class="gradient-text">Sceglierci?</span></h2>
  <div class="FeaturesGrid stagger-grid">
    <div class="FeatureCard stagger-item">
      <div class="FeatureCard__Icon"><i class="fas fa-cogs"></i></div>
      <h5>Integrazione semplice</h5>
      <p>API facili da usare per un'integrazione rapida nei tuoi giochi GameMaker.</p>
    </div>
    <div class="FeatureCard stagger-item">
      <div class="FeatureCard__Icon"><i class="fas fa-gift"></i></div>
      <h5>Gratuito per sempre</h5>
      <p>Nessun costo nascosto. Offri classifiche online senza pensieri.</p>
    </div>
    <div class="FeatureCard stagger-item">
      <div class="FeatureCard__Icon"><i class="fas fa-shield-alt"></i></div>
      <h5>Sicuro e affidabile</h5>
      <p>Piattaforma stabile con protezione anti-cheat di base.</p>
    </div>
    <div class="FeatureCard stagger-item">
      <div class="FeatureCard__Icon"><i class="fas fa-server"></i></div>
      <h5>Nessun server richiesto</h5>
      <p>Gestiamo noi l'hosting delle classifiche, tu concentrati sul gioco.</p>
    </div>
    <div class="FeatureCard stagger-item">
      <div class="FeatureCard__Icon"><i class="fas fa-users-cog"></i></div>
      <h5>Gestione facilitata</h5>
      <p>Dashboard intuitiva per gestire giochi, punteggi e ban.</p>
    </div>
    <div class="FeatureCard stagger-item">
      <div class="FeatureCard__Icon"><i class="fas fa-users"></i></div>
      <h5>Supporto comunitario</h5>
      <p>Entra nella nostra community Discord per supporto, idee e collaborazione.</p>
    </div>
    <div class="FeatureCard stagger-item">
      <div class="FeatureCard__Icon"><i class="fab fa-github"></i></div>
      <h5>Open source e trasparente</h5>
      <p>Il codice sorgente è disponibile su <a href="https://github.com/manuel-di-iorio/gmiscores">GitHub</a>. Contribuisci o adatta la piattaforma.</p>
    </div>
    <div class="FeatureCard stagger-item">
      <div class="FeatureCard__Icon"><i class="fas fa-rocket"></i></div>
      <h5>Evoluzione continua</h5>
      <p>Siamo costantemente al lavoro per aggiungere nuove funzionalità e migliorare la piattaforma.</p>
    </div>
  </div>
</div>

<!-- ===== STATS ===== -->
<div id="numeri" class="w3-container StatsSection">
  <h2 class="SectionTitle fade-in-up-on-scroll">La Nostra Community in <span class="gradient-text">Numeri</span></h2>
  <div class="StatCardContainer stagger-grid">
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
    <div class="StatCard stagger-item">
      <div class="StatCard__Icon"><i class="<?= $statIcons[$key] ?? 'fas fa-chart-bar' ?>"></i></div>
      <div class="StatCard__Count">
        <span class="stat-number" data-target="<?= is_numeric($stat["count"]) ? intval($stat["count"]) : 0 ?>">
          <?= is_numeric($stat["count"]) ? '0' : htmlspecialchars($stat["count"]) ?>
        </span>
        <?= !is_numeric($stat["count"]) ? htmlspecialchars($stat["count"]) : '' ?>
      </div>
      <div class="StatCard__Label"><?= htmlspecialchars($stat["label"]) ?></div>
    </div>
    <?php } ?>
  </div>
</div>

<!-- ===== TESTIMONIALS ===== -->
<!-- <div class="w3-container TestimonialsSection">
  <h2 class="SectionTitle fade-in-up-on-scroll">Dicono di <span class="gradient-text">noi</span></h2>
  <div class="TestimonialsGrid stagger-grid">
    <div class="TestimonialCard stagger-item">
      <img src="https://i.pravatar.cc/100?u=dev1" alt="Avatar sviluppatore" class="TestimonialCard__Avatar" loading="lazy">
      <p class="TestimonialCard__Quote">Implementare le classifiche è stato un gioco da ragazzi! I miei giocatori adorano la competizione.</p>
      <p class="TestimonialCard__Author">— Alex Rossi, Sviluppatore di 'Space Raiders'</p>
    </div>
    <div class="TestimonialCard stagger-item">
      <img src="https://i.pravatar.cc/100?u=dev2" alt="Avatar sviluppatore" class="TestimonialCard__Avatar" loading="lazy">
      <p class="TestimonialCard__Quote">Finalmente una soluzione semplice e gratuita per le leaderboard. Consigliatissimo!</p>
      <p class="TestimonialCard__Author">— Giulia Bianchi, Sviluppatrice di 'Pixel Jumper'</p>
    </div>
    <div class="TestimonialCard stagger-item">
      <img src="https://i.pravatar.cc/100?u=dev3" alt="Avatar sviluppatore" class="TestimonialCard__Avatar" loading="lazy">
      <p class="TestimonialCard__Quote">La dashboard è intuitiva e la gestione dei punteggi è ottima. Un servizio eccellente.</p>
      <p class="TestimonialCard__Author">— Marco Verdi, Sviluppatore di 'Fantasy Quest RPG'</p>
    </div>
  </div>
</div> -->

<!-- ===== FINAL CTA ===== -->
<div id="contatti" class="w3-container w3-center FinalCtaSection">
  <h4 class="fade-in-up-on-scroll"><strong>Pronto a portare i tuoi giochi <span class="gradient-text">al livello successivo?</span></strong></h4>
  <p class="fade-in-up-on-scroll anim-delay-200">Non aspettare! Unisciti alla nostra community di sviluppatori e offri ai tuoi giocatori un'esperienza competitiva e coinvolgente.</p>
  <a href="./add-game.php" class="fade-in-up-on-scroll anim-delay-400">
    <button type="submit" class="w3-button w3-padding-large CtaButton btn-glow">
      <i class="fas fa-rocket w3-margin-right"></i> Aggiungi il tuo gioco
    </button>
  </a>
</div>

<script>
function switchThemeHome() {
  const theme = "<?= $theme === 'dark' ? 'light' : 'dark' ?>";
  location.href = "switch-theme.php?theme=" + theme + "&go=" + encodeURIComponent("<?= $_SERVER["REQUEST_URI"] ?>");
}
</script>
