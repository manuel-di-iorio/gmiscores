<style>
  .HomeBanner {
    background: url(assets/images/banner.webp);
    background-size: cover;
    background-position: bottom center;
    width: 100%;
    min-height: 100vh;
    display: flex;
    align-items: center;
    color: white;
    padding: 40px;
    position: relative;
    overflow: hidden;
  }

  .HomeBanner::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 1;
    pointer-events: none;
  }

  .HomeBanner::after {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 80% 60% at 50% 40%, transparent 0%, rgba(0,0,0,0.3) 100%);
    z-index: 1;
    pointer-events: none;
  }

  .hero-inner {
    display: flex;
    align-items: center;
    gap: 60px;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
    position: relative;
    z-index: 2;
  }

  .hero-content {
    flex: 1.3;
    min-width: 0;
  }

  .hero-visual {
    flex: 1;
    min-width: 0;
  }

  .HomeBanner h1 {
    font-size: clamp(2rem, 4.5vw, 3.6rem);
    font-weight: 800;
    margin-bottom: 0.5em;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.4);
    line-height: 1.15;
  }

  .HomeBanner .hero-subtitle {
    font-size: clamp(0.95rem, 1.5vw, 1.15rem);
    margin-bottom: 2em;
    max-width: 520px;
    line-height: 1.7;
    opacity: 0.85;
    text-shadow: 0 1px 8px rgba(0,0,0,0.4);
  }

  .hero-actions {
    display: flex;
    gap: 16px;
    align-items: center;
    flex-wrap: wrap;
  }

  .CtaButton {
    padding: 16px 36px;
    border-radius: 12px;
    font-weight: 600;
    letter-spacing: 0.3px;
    cursor: pointer;
    border: none;
    font-size: 1em;
    transition: transform 0.3s cubic-bezier(0.16,1,0.3,1), box-shadow 0.3s;
  }

  .CtaButton--primary {
    background: linear-gradient(135deg, var(--gradient-start, #6366f1), var(--gradient-end, #ec4899)) !important;
    color: white !important;
    box-shadow: 0 4px 20px rgba(var(--primary-color-rgb, 99,102,241), 0.3);
  }

  .CtaButton--primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(var(--primary-color-rgb, 99,102,241), 0.45);
  }

  .CtaButton--secondary {
    background: rgba(255,255,255,0.08) !important;
    color: white !important;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.15) !important;
  }

  .CtaButton--secondary:hover {
    background: rgba(255,255,255,0.15) !important;
    transform: translateY(-2px);
  }

  .theme-toggle-home {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1.3rem;
    color: white;
    transition: background 0.2s, transform 0.2s;
    z-index: 10000;
  }

  .theme-toggle-home:hover {
    background: rgba(255,255,255,0.2);
    transform: scale(1.1);
  }

  .scroll-indicator {
    position: absolute;
    bottom: 28px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    color: white;
    font-size: 0.75em;
    opacity: 0.5;
    letter-spacing: 1px;
    text-transform: uppercase;
    z-index: 5;
    animation: scrollHint 2.5s ease-in-out infinite;
  }

  .scroll-indicator .scroll-line {
    width: 1px;
    height: 32px;
    background: linear-gradient(to bottom, white, transparent);
  }

  @keyframes scrollHint {
    0%, 100% { transform: translateX(-50%) translateY(0); opacity: 0.5; }
    50% { transform: translateX(-50%) translateY(6px); opacity: 0.8; }
  }

  .SectionTitle {
    text-align: center;
    margin-bottom: 48px;
    font-size: clamp(1.4rem, 3vw, 2.2rem);
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
    width: 48px;
    height: 3px;
    background: linear-gradient(90deg, var(--gradient-start, #6366f1), var(--gradient-end, #ec4899));
    border-radius: 2px;
  }

  .SectionTitle--left {
    text-align: left;
  }

  .SectionTitle--left::after {
    left: 0;
    transform: none;
  }

  .HowItWorksSection {
    padding: 80px 20px;
  }

  .process-step {
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
  }

  .process-step:nth-child(even) {
    margin-left: auto;
  }

  .VisualShowcaseSection {
    padding: 80px 20px;
    background-color: var(--bg-color, white);
  }

  .VisualShowcaseContainer {
    display: flex;
    align-items: center;
    gap: 60px;
    max-width: 1100px;
    margin: 0 auto;
  }

  .VisualShowcase__Text { flex: 1; }

  .VisualShowcase__Text h3 {
    font-size: clamp(1.2rem, 2.5vw, 1.8rem);
    font-weight: 700;
    margin-bottom: 16px;
    color: var(--text-color-headings, #333);
  }

  .VisualShowcase__Text p {
    font-size: 1em;
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
    box-shadow: 0 24px 48px rgba(0,0,0,0.12);
  }

  .FeaturesSection {
    padding: 80px 20px;
    background-color: var(--bg-color-offset, #f4f7f6);
  }

  .FeaturesGrid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
    max-width: 1100px;
    margin: 0 auto;
  }

  .FeatureCard {
    background: var(--glass-bg, rgba(255,255,255,0.08));
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--glass-border, rgba(255,255,255,0.12));
    border-radius: 16px;
    padding: 28px 24px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
  }

  .FeatureCard:hover {
    border-color: var(--glass-border-hover, rgba(99,102,241,0.3));
    box-shadow: 0 16px 40px rgba(0,0,0,0.08);
  }

  .FeatureCard__Icon {
    font-size: 2.2em;
    color: var(--primary-color, #6366f1);
    margin-bottom: 16px;
  }

  .FeatureCard h5 {
    font-size: 1.1em;
    font-weight: 700;
    color: var(--text-color-headings, #333);
    margin-bottom: 8px;
  }

  .FeatureCard p {
    font-size: 0.9em;
    color: var(--text-color-secondary, #555);
    line-height: 1.6;
  }

  .StatsSection {
    padding: 80px 20px;
    position: relative;
    overflow: hidden;
  }

  .StatsSection::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
      radial-gradient(ellipse 60% 50% at 20% 50%, rgba(var(--primary-color-rgb, 99,102,241), 0.05), transparent),
      radial-gradient(ellipse 60% 50% at 80% 50%, rgba(236, 72, 153, 0.05), transparent);
    pointer-events: none;
  }

  .StatCardContainer {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    max-width: 1100px;
    margin: 0 auto;
    position: relative;
  }

  .StatCard {
    background: var(--card-bg, rgba(255,255,255,0.04));
    border: 1px solid var(--glass-border, rgba(255,255,255,0.1));
    border-radius: 16px;
    padding: 28px 20px;
    text-align: center;
    transition: transform 0.3s cubic-bezier(0.16,1,0.3,1), box-shadow 0.3s, border-color 0.3s;
  }

  .StatCard:hover {
    transform: translateY(-4px);
    border-color: var(--glass-border-hover, rgba(99,102,241,0.3));
    box-shadow: 0 12px 32px rgba(0,0,0,0.06);
  }

  .StatCard__Icon {
    font-size: 2em;
    color: var(--primary-color, #6366f1);
    margin-bottom: 12px;
  }

  .StatCard__Count {
    font-size: 2.2em;
    font-weight: 800;
    color: var(--text-color-headings, #333);
    margin-bottom: 0.15em;
    font-variant-numeric: tabular-nums;
  }

  .StatCard__Label {
    font-size: 0.9em;
    color: var(--text-color-secondary, #555);
    line-height: 1.4;
  }

  .FAQsSection {
    padding: 80px 20px;
  }

  .FAQsContainer {
    max-width: 700px;
    margin: 0 auto;
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
    opacity: 0.04;
    pointer-events: none;
  }

  .FinalCtaSection h4 {
    font-size: clamp(1.4rem, 2.8vw, 2.2rem);
    font-weight: 800;
    color: var(--text-color-headings, #333);
    margin-bottom: 16px;
    position: relative;
  }

  .FinalCtaSection p {
    font-size: 1.05em;
    color: var(--text-color-secondary, #555);
    max-width: 550px;
    margin: 0 auto 32px;
    line-height: 1.7;
    position: relative;
  }

  .FinalCtaSection .CtaButton--primary {
    position: relative;
    font-size: 1.1em;
    padding: 18px 44px;
  }

  @media (max-width: 900px) {
    .HomeBanner { padding: 100px 24px 60px; }
    .hero-inner { flex-direction: column; gap: 40px; text-align: center; }
    .hero-subtitle { margin-left: auto; margin-right: auto; }
    .hero-actions { justify-content: center; }
    .hero-social-proof { justify-content: center; }
    .hero-visual { max-width: 400px; }
    .scroll-indicator { display: none; }
    .VisualShowcaseContainer { flex-direction: column; text-align: center; gap: 32px; }
    .VisualShowcase__Image { margin-top: 0; }
  }

  @media (max-width: 768px) {
    .HowItWorksSection, .VisualShowcaseSection, .FeaturesSection,
    .StatsSection, .TestimonialsSection, .FAQsSection { padding: 50px 16px; }
    .FinalCtaSection { padding: 60px 20px; }
    .StatsSection { padding: 50px 16px; }
    .hero-actions { flex-direction: column; width: 100%; }
    .hero-actions .CtaButton { width: 100%; text-align: center; justify-content: center; }
  }
</style>

<div id="scroll-progress"></div>

<!-- ===== STICKY HEADER ===== -->
<header class="landing-header" role="banner">
  <a href="./index.php" class="header-logo">
    <img src="assets/images/logoSmall.png" alt="Logo">
    <span><?= htmlspecialchars($config["platformTitle"]) ?></span>
  </a>
  <nav class="header-nav">
    <a href="#come-funziona" class="nav-link-underline">Come funziona</a>
    <a href="#caratteristiche" class="nav-link-underline">Caratteristiche</a>
    <a href="#numeri" class="nav-link-underline">Numeri</a>
    <a href="#faq" class="nav-link-underline">FAQ</a>
    <a href="./add-game.php" class="header-cta">Inizia subito</a>
  </nav>
</header>

<!-- ===== HERO ===== -->
<div class="HomeBanner dot-pattern">
  <div id="hero-particles"></div>
  <div class="hero-floating-shape"></div>
  <div class="hero-floating-shape"></div>
  <div class="hero-floating-shape"></div>

  <div class="theme-toggle-home" onclick="switchThemeHome()" title="Cambia tema">
    <i class="fas <?= $theme === 'dark' ? 'fa-sun' : 'fa-moon' ?>"></i>
  </div>

  <div class="hero-inner">
    <div class="hero-content">
      <h1 class="anim-fade-up">
        Porta le tue classifiche<br>
        <span style="background:linear-gradient(135deg,#a78bfa,#f9a8d4);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;text-shadow:0 0 30px rgba(167,139,250,0.3);">al prossimo livello</span>
      </h1>

      <p class="hero-subtitle anim-fade-up anim-delay-200">
        Integra leaderboard online nei tuoi giochi GameMaker in pochi minuti. Gratuito, sicuro e senza server.
      </p>

      <div class="hero-actions anim-fade-up anim-delay-300">
        <a href="./add-game.php" class="CtaButton CtaButton--primary ripple-btn" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
          <i class="fas fa-rocket"></i> Inizia subito
        </a>
        <a href="./documentation.php" class="CtaButton CtaButton--secondary ripple-btn" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
          <i class="fas fa-book"></i> Documentazione
        </a>
      </div>
    </div>

    <div class="hero-visual anim-scale-up anim-delay-200">
      <div class="hero-mockup">
        <div class="hero-mockup__frame">
          <div class="hero-mockup__header">
            <span class="hero-mockup__title">CLASSIFICA GLOBALE</span>
            <div class="hero-mockup__dots">
              <span class="hero-mockup__dot"></span>
              <span class="hero-mockup__dot"></span>
              <span class="hero-mockup__dot"></span>
            </div>
          </div>
          <div class="hero-mockup__row">
            <span class="hero-mockup__rank">#1</span>
            <span class="hero-mockup__avatar"></span>
            <span class="hero-mockup__name">PlayerOne</span>
            <span class="hero-mockup__score">12,450</span>
            <div class="hero-mockup__bar"><div class="hero-mockup__bar-fill" style="width:85%"></div></div>
          </div>
          <div class="hero-mockup__row">
            <span class="hero-mockup__rank">#2</span>
            <span class="hero-mockup__avatar"></span>
            <span class="hero-mockup__name">GameMaster</span>
            <span class="hero-mockup__score">10,230</span>
            <div class="hero-mockup__bar"><div class="hero-mockup__bar-fill" style="width:72%"></div></div>
          </div>
          <div class="hero-mockup__row">
            <span class="hero-mockup__rank">#3</span>
            <span class="hero-mockup__avatar"></span>
            <span class="hero-mockup__name">PixelWarrior</span>
            <span class="hero-mockup__score">8,915</span>
            <div class="hero-mockup__bar"><div class="hero-mockup__bar-fill" style="width:64%"></div></div>
          </div>
          <div class="hero-mockup__row">
            <span class="hero-mockup__rank">#4</span>
            <span class="hero-mockup__avatar"></span>
            <span class="hero-mockup__name">SpeedRunner</span>
            <span class="hero-mockup__score">6,700</span>
            <div class="hero-mockup__bar"><div class="hero-mockup__bar-fill" style="width:55%"></div></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="scroll-indicator">
    <span>Scopri</span>
    <div class="scroll-line"></div>
  </div>
</div>

<!-- ===== HOW IT WORKS ===== -->
<div id="come-funziona" class="w3-container HowItWorksSection">
  <h2 class="SectionTitle fade-in-up-on-scroll">Come funziona?</h2>
  <div class="process-line" style="max-width:700px;margin:0 auto;">
    <div class="process-step fade-in-up-on-scroll">
      <div class="process-step__number">1</div>
      <h5 style="font-size:1.15em;font-weight:700;margin-bottom:6px;color:var(--text-color-headings);">Registra il tuo gioco</h5>
      <p style="color:var(--text-color-secondary);line-height:1.6;font-size:0.95em;">Aggiungi il tuo gioco sulla piattaforma e ricevi ID univoco e chiave segreta in pochi secondi.</p>
    </div>
    <div class="process-step fade-in-up-on-scroll">
      <div class="process-step__number">2</div>
      <h5 style="font-size:1.15em;font-weight:700;margin-bottom:6px;color:var(--text-color-headings);">Integra l'API</h5>
      <p style="color:var(--text-color-secondary);line-height:1.6;font-size:0.95em;">Usa le nostre semplici chiamate HTTP per inviare e recuperare punteggi direttamente da GameMaker.</p>
    </div>
    <div class="process-step fade-in-up-on-scroll">
      <div class="process-step__number">3</div>
      <h5 style="font-size:1.15em;font-weight:700;margin-bottom:6px;color:var(--text-color-headings);">Classifiche globali</h5>
      <p style="color:var(--text-color-secondary);line-height:1.6;font-size:0.95em;">I punteggi dei tuoi giocatori saranno visibili in classifiche globali, pronti per la competizione.</p>
    </div>
    <div class="process-step fade-in-up-on-scroll">
      <div class="process-step__number">4</div>
      <h5 style="font-size:1.15em;font-weight:700;margin-bottom:6px;color:var(--text-color-headings);">Sicurezza e gestione</h5>
      <p style="color:var(--text-color-secondary);line-height:1.6;font-size:0.95em;">Gestisci giochi, monitora statistiche e banna giocatori scorretti dalla tua dashboard.</p>
    </div>
  </div>
</div>

<!-- ===== VISUAL SHOWCASE ===== -->
<div class="w3-container VisualShowcaseSection">
  <div class="VisualShowcaseContainer">
    <div class="VisualShowcase__Text anim-fade-left">
      <h2 class="SectionTitle SectionTitle--left" style="margin-bottom:20px;">
        Dai vita alle <span class="gradient-text">tue sfide</span>
      </h2>
      <p>Trasforma ogni partita in una competizione epica. I tuoi giocatori scaleranno le classifiche, condivideranno i loro risultati e si sentiranno parte di una community globale.</p>
      <div class="ripple-btn" style="display:inline-block;margin-top:8px;">
        <a href="./add-game.php" class="CtaButton CtaButton--primary" style="display:inline-flex;align-items:center;gap:8px;padding:14px 32px;font-size:0.95em;text-decoration:none;">
          <i class="fas fa-arrow-right"></i> Aggiungi il tuo gioco
        </a>
      </div>
    </div>
    <div class="VisualShowcase__Image anim-fade-right">
      <img src="/assets/images/landing_leaderboard.avif" alt="Visualizzazione Classifiche" loading="lazy">
    </div>
  </div>
</div>

<!-- ===== FEATURES ===== -->
<div id="caratteristiche" class="w3-container FeaturesSection">
  <h2 class="SectionTitle fade-in-up-on-scroll">Perchè <span class="gradient-text">sceglierci?</span></h2>
  <div class="FeaturesGrid">
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-cogs"></i></div>
      <h5>Integrazione semplice</h5>
      <p>API facili da usare per un'integrazione rapida nei tuoi giochi GameMaker.</p>
      <a href="./documentation.php" class="card-arrow" style="text-decoration:none;">Scopri di più <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-gift"></i></div>
      <h5>Gratuito per sempre</h5>
      <p>Nessun costo nascosto. Offri classifiche online senza pensieri, sempre.</p>
      <a href="./add-game.php" class="card-arrow" style="text-decoration:none;">Scopri di più <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-shield-alt"></i></div>
      <h5>Sicuro e affidabile</h5>
      <p>Piattaforma stabile con protezione anti-cheat di base e backup regolari.</p>
      <a href="./game.php" class="card-arrow" style="text-decoration:none;">Scopri di più <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-server"></i></div>
      <h5>Nessun server richiesto</h5>
      <p>Gestiamo noi l'hosting. Tu scrivi il gioco, noi gestiamo le classifiche.</p>
      <a href="./documentation.php" class="card-arrow" style="text-decoration:none;">Scopri di più <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-users-cog"></i></div>
      <h5>Dashboard completa</h5>
      <p>Interfaccia intuitiva per gestire giochi, punteggi, ban e statistiche.</p>
      <a href="./games.php" class="card-arrow" style="text-decoration:none;">Scopri di più <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-users"></i></div>
      <h5>Community attiva</h5>
      <p>Entra nel nostro Discord per supporto, idee e collaborazione con altri dev.</p>
      <a href="https://discord.gg/85RCMD9VQD" class="card-arrow" style="text-decoration:none;" target="_blank">Scopri di più <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fab fa-github"></i></div>
      <h5>Open source</h5>
      <p>Codice su <a href="https://github.com/manuel-di-iorio/gmiscores" style="color:var(--primary-color);">GitHub</a>. Contribuisci o personalizza.</p>
      <a href="https://github.com/manuel-di-iorio/gmiscores" class="card-arrow" style="text-decoration:none;" target="_blank">Scopri di più <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-rocket"></i></div>
      <h5>Evoluzione continua</h5>
      <p>Nuove funzionalità e miglioramenti costanti per la piattaforma.</p>
      <a href="./documentation.php" class="card-arrow" style="text-decoration:none;">Scopri di più <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</div>

<!-- ===== STATS ===== -->
<div id="numeri" class="w3-container StatsSection stats-gradient-section">
  <h2 class="SectionTitle fade-in-up-on-scroll">La community in <span class="gradient-text">numeri</span></h2>
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
        <?php if (is_numeric($stat["count"])) { ?>
          <span class="stat-number" data-target="<?= intval($stat["count"]) ?>">0</span>
        <?php } else { ?>
          <span style="font-size:0.7em;display:block;line-height:1.3;word-break:break-word;"><?= htmlspecialchars($stat["count"]) ?></span>
        <?php } ?>
      </div>
      <div class="StatCard__Label"><?= htmlspecialchars($stat["label"]) ?></div>
    </div>
    <?php } ?>
  </div>
</div>

<!-- ===== FAQ ===== -->
<div id="faq" class="w3-container FAQsSection">
  <h2 class="SectionTitle fade-in-up-on-scroll">Domande <span class="gradient-text">frequenti</span></h2>
  <div class="FAQsContainer">
    <div class="faq-item fade-in-up-on-scroll">
      <button class="faq-question">
        <span>Costa qualcosa utilizzare la piattaforma?</span>
        <span class="faq-icon"><i class="fas fa-plus"></i></span>
      </button>
      <div class="faq-answer">No, la piattaforma è completamente gratuita. Puoi registrare un numero illimitato di giochi e gestire le tue classifiche senza alcun costo.</div>
    </div>
    <div class="faq-item fade-in-up-on-scroll anim-delay-100">
      <button class="faq-question">
        <span>Come integro le API nel mio gioco GameMaker?</span>
        <span class="faq-icon"><i class="fas fa-plus"></i></span>
      </button>
      <div class="faq-answer">Abbiamo una documentazione dettagliata con esempi pronti all'uso per GameMaker. Basta copiare il codice, inserire la tua chiave API e sei pronto.</div>
    </div>
    <div class="faq-item fade-in-up-on-scroll anim-delay-200">
      <button class="faq-question">
        <span>Posso proteggere le mie classifiche dagli hacker?</span>
        <span class="faq-icon"><i class="fas fa-plus"></i></span>
      </button>
      <div class="faq-answer">Sì, offriamo strumenti di moderazione tra cui ban players, cancellazione punteggi sospetti e validazione lato server per prevenire abusi.</div>
    </div>
    <div class="faq-item fade-in-up-on-scroll anim-delay-300">
      <button class="faq-question">
        <span>Quali linguaggi/engine sono supportati?</span>
        <span class="faq-icon"><i class="fas fa-plus"></i></span>
      </button>
      <div class="faq-answer">Le API sono basate su semplici chiamate HTTP, quindi supportano qualsiasi engine o linguaggio che possa fare richieste web. La documentazione copre GameMaker in dettaglio.</div>
    </div>
  </div>
</div>

<!-- ===== FINAL CTA ===== -->
<div id="contatti" class="w3-container w3-center FinalCtaSection">
  <h4 class="fade-in-up-on-scroll">
    <strong>Pronto a portare i tuoi giochi <span class="gradient-text">al livello successivo?</span></strong>
  </h4>
  <p class="fade-in-up-on-scroll anim-delay-100">
    Unisciti alla community di sviluppatori e offri ai tuoi giocatori un'esperienza competitiva indimenticabile.
  </p>
  <div class="fade-in-up-on-scroll anim-delay-200 ripple-btn" style="display:inline-block;">
    <a href="./add-game.php" class="CtaButton CtaButton--primary" style="display:inline-flex;align-items:center;gap:10px;padding:18px 44px;font-size:1.1em;text-decoration:none;">
      <i class="fas fa-rocket"></i> Aggiungi il tuo gioco
    </a>
  </div>
</div>

<script>
function switchThemeHome() {
  const theme = "<?= $theme === 'dark' ? 'light' : 'dark' ?>";
  location.href = "switch-theme.php?theme=" + theme + "&go=" + encodeURIComponent("<?= $_SERVER["REQUEST_URI"] ?>");
}
</script>
