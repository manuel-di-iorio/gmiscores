<style>
  .HomeBanner {
    background: url(assets/images/banner.webp);
    background-size: cover;
    background-position: bottom center;
    width: 100%;
    height: 100vh;
    display: flex;
    align-items: center;
    color: white;
    padding: 40px;
    position: relative;
    overflow: hidden;
    box-sizing: border-box;
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

  .hero-logo {
    position: absolute;
    top: 28px;
    left: 40px;
    height: 36px;
    width: auto;
    z-index: 3;
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
    box-sizing: border-box;
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

  .hero-float-bar {
    position: fixed;
    top: 20px;
    right: 20px;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px;
    border-radius: 14px;
    background: rgba(15, 17, 23, 0.6);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    z-index: 10000;
    transition: opacity 0.3s;
  }
  .hero-float-bar.hero-float-hidden {
    opacity: 0;
    pointer-events: none;
  }
  .hero-icon-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: transparent;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    font-size: 1.1rem;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
  }
  .hero-icon-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
  }
  .hero-user-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 4px 12px 4px 4px;
    border-radius: 10px;
    text-decoration: none;
    color: white;
    transition: background 0.2s;
  }
  .hero-user-pill:hover {
    background: rgba(255, 255, 255, 0.08);
  }
  .hero-user-avatar {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--gradient-start, #6366f1), var(--gradient-end, #ec4899));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8em;
    font-weight: 700;
    color: white;
  }
  .hero-user-name {
    font-size: 0.82em;
    font-weight: 500;
    white-space: nowrap;
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
    /* max-width: 600px; */
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
    position: relative;
    transition: box-shadow 0.3s, border-color 0.3s;
  }

  .StatCard:hover {
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
    .HomeBanner { padding: 60px 24px 60px; }
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
    .hero-logo { display: none; }
  }

  @media (max-width: 480px) {
    .HomeBanner { padding: 40px 16px; height: auto; min-height: 100vh; }
    .CtaButton { padding: 14px 20px; font-size: 0.9em; }
  }
</style>

<div id="scroll-progress"></div>

<!-- ===== STICKY HEADER ===== -->
<header class="landing-header" role="banner">
  <a href="./index.php" class="header-logo">
    <img src="/assets/images/logo<?= $theme === 'dark' ? 'White' : '' ?>.svg" alt="Logo">
    <!-- <span><?= __("site_name") ?></span> -->
  </a>
  <nav class="header-nav">
    <a href="#come-funziona" class="nav-link-underline"><?= __('index_nav_how') ?></a>
    <a href="#servizi" class="nav-link-underline"><?= __('index_nav_services') ?></a>
    <a href="#caratteristiche" class="nav-link-underline"><?= __('index_nav_features') ?></a>
    <a href="#numeri" class="nav-link-underline"><?= __('index_nav_numbers') ?></a>
    <a href="#faq" class="nav-link-underline"><?= __('index_nav_faq') ?></a>
    <?php if (isset($user)) { ?>
      <a href="./home.php" class="header-user-pill">
        <span class="header-user-avatar"><?= strtoupper(mb_substr($user["username"], 0, 1)) ?></span>
        <span class="header-user-name"><?= htmlspecialchars($user["username"]) ?></span>
      </a>
    <?php } else { ?>
      <a href="login.php" class="header-icon-btn" title="<?= __('nav_login') ?>"><i class="fas fa-sign-in-alt"></i></a>
    <?php } ?>
    <a href="<?= isset($user) ? './home.php' : './add-game.php' ?>" class="header-cta"><?= __('index_nav_start') ?></a>
  </nav>
</header>

<!-- ===== HERO ===== -->
<div class="HomeBanner dot-pattern">
  <img src="/assets/images/logoWhite.svg" class="hero-logo" alt="Logo">
  <div id="hero-particles"></div>
  <div class="hero-floating-shape"></div>
  <div class="hero-floating-shape"></div>
  <div class="hero-floating-shape"></div>

  <div class="hero-inner">
    <div class="hero-content">
      <h1 class="anim-fade-up">
        <?= __('index_hero_title1') ?><br>
        <span style="background:linear-gradient(135deg,#a78bfa,#f9a8d4);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;text-shadow:0 0 30px rgba(167,139,250,0.3);"><?= __('index_hero_title2') ?></span>
      </h1>

      <p class="hero-subtitle anim-fade-up anim-delay-200">
        <?= __('index_hero_subtitle') ?>
      </p>

      <div class="hero-actions anim-fade-up anim-delay-300">
        <a href="<?= isset($user) ? './home.php' : './add-game.php' ?>" class="CtaButton CtaButton--primary ripple-btn" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
          <i class="fas fa-rocket"></i> <?= __('index_hero_cta') ?>
        </a>
        <a href="./documentation.php" class="CtaButton CtaButton--secondary ripple-btn" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
          <i class="fas fa-book"></i> <?= __('index_hero_docs') ?>
        </a>
      </div>
    </div>

    <div class="hero-visual anim-scale-up anim-delay-200">
      <div class="hero-mockup">
        <div class="hero-mockup__frame">
          <div class="hero-mockup__header">
            <span class="hero-mockup__title"><?= __('index_mockup_title') ?></span>
            <div class="hero-mockup__dots">
              <span class="hero-mockup__dot"></span>
              <span class="hero-mockup__dot"></span>
              <span class="hero-mockup__dot"></span>
            </div>
          </div>
          <div class="hero-mockup__row">
            <span class="hero-mockup__rank">#1</span>
            <span class="hero-mockup__avatar"></span>
            <span class="hero-mockup__name"><?= __('index_mockup_player1') ?></span>
            <span class="hero-mockup__score">12,450</span>
            <div class="hero-mockup__bar"><div class="hero-mockup__bar-fill" style="width:85%"></div></div>
          </div>
          <div class="hero-mockup__row">
            <span class="hero-mockup__rank">#2</span>
            <span class="hero-mockup__avatar"></span>
            <span class="hero-mockup__name"><?= __('index_mockup_player2') ?></span>
            <span class="hero-mockup__score">10,230</span>
            <div class="hero-mockup__bar"><div class="hero-mockup__bar-fill" style="width:72%"></div></div>
          </div>
          <div class="hero-mockup__row">
            <span class="hero-mockup__rank">#3</span>
            <span class="hero-mockup__avatar"></span>
            <span class="hero-mockup__name"><?= __('index_mockup_player3') ?></span>
            <span class="hero-mockup__score">8,915</span>
            <div class="hero-mockup__bar"><div class="hero-mockup__bar-fill" style="width:64%"></div></div>
          </div>
          <div class="hero-mockup__row">
            <span class="hero-mockup__rank">#4</span>
            <span class="hero-mockup__avatar"></span>
            <span class="hero-mockup__name"><?= __('index_mockup_player4') ?></span>
            <span class="hero-mockup__score">6,700</span>
            <div class="hero-mockup__bar"><div class="hero-mockup__bar-fill" style="width:55%"></div></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="scroll-indicator">
    <span><?= __('index_scroll') ?></span>
    <div class="scroll-line"></div>
  </div>
</div>

<div class="hero-float-bar">
  <?php if (isset($user)) { ?>
    <a href="./home.php" class="hero-user-pill">
      <span class="hero-user-avatar"><?= strtoupper(mb_substr($user["username"], 0, 1)) ?></span>
      <span class="hero-user-name"><?= htmlspecialchars($user["username"]) ?></span>
    </a>
  <?php } else { ?>
    <a href="login.php" class="hero-icon-btn" title="<?= __('nav_login') ?>"><i class="fas fa-sign-in-alt"></i></a>
  <?php } ?>
  <div class="hero-icon-btn" onclick="switchThemeHome()" title="<?= __('index_theme_toggle') ?>">
    <i class="fas <?= $theme === 'dark' ? 'fa-sun' : 'fa-moon' ?>"></i>
  </div>
</div>

<!-- ===== HOW IT WORKS ===== -->
<div id="come-funziona" class="section-container HowItWorksSection">
  <h2 class="SectionTitle fade-in-up-on-scroll"><?= __('index_how_title') ?></h2>
  <div class="process-line" style="max-width:700px;margin:0 auto;">
    <div class="process-step fade-in-up-on-scroll">
      <div class="process-step__number">1</div>
      <h5 style="font-size:1.15em;font-weight:700;margin-bottom:6px;color:var(--text-color-headings);"><?= __('index_step1_title') ?></h5>
      <p style="color:var(--text-color-secondary);line-height:1.6;font-size:0.95em;"><?= __('index_step1_desc') ?></p>
    </div>
    <div class="process-step fade-in-up-on-scroll">
      <div class="process-step__number">2</div>
      <h5 style="font-size:1.15em;font-weight:700;margin-bottom:6px;color:var(--text-color-headings);"><?= __('index_step2_title') ?></h5>
      <p style="color:var(--text-color-secondary);line-height:1.6;font-size:0.95em;"><?= __('index_step2_desc') ?></p>
    </div>
    <div class="process-step fade-in-up-on-scroll">
      <div class="process-step__number">3</div>
      <h5 style="font-size:1.15em;font-weight:700;margin-bottom:6px;color:var(--text-color-headings);"><?= __('index_step3_title') ?></h5>
      <p style="color:var(--text-color-secondary);line-height:1.6;font-size:0.95em;"><?= __('index_step3_desc') ?></p>
    </div>
    <div class="process-step fade-in-up-on-scroll">
      <div class="process-step__number">4</div>
      <h5 style="font-size:1.15em;font-weight:700;margin-bottom:6px;color:var(--text-color-headings);"><?= __('index_step4_title') ?></h5>
      <p style="color:var(--text-color-secondary);line-height:1.6;font-size:0.95em;"><?= __('index_step4_desc') ?></p>
    </div>
  </div>
</div>

<!-- ===== VISUAL SHOWCASE ===== -->
<div class="section-container VisualShowcaseSection">
  <div class="VisualShowcaseContainer">
    <div class="VisualShowcase__Text anim-fade-left">
      <h2 class="SectionTitle SectionTitle--left" style="margin-bottom:20px;">
        <?= __('index_showcase_title1') ?> <span class="gradient-text"><?= __('index_showcase_title2') ?></span>
      </h2>
      <p><?= __('index_showcase_desc') ?></p>
      <div class="ripple-btn" style="display:inline-block;margin-top:8px;">
        <a href="./add-game.php" class="CtaButton CtaButton--primary" style="display:inline-flex;align-items:center;gap:8px;padding:14px 32px;font-size:0.95em;text-decoration:none;">
          <i class="fas fa-arrow-right"></i> <?= __('index_showcase_cta') ?>
        </a>
      </div>
    </div>
    <div class="VisualShowcase__Image anim-fade-right">
      <img src="/assets/images/landing_leaderboard.jpg" alt="<?= __('index_showcase_img_alt') ?>" loading="lazy">
    </div>
  </div>
</div>

<!-- ===== FEATURES ===== -->
<div id="caratteristiche" class="section-container FeaturesSection">
  <h2 class="SectionTitle fade-in-up-on-scroll"><?= __('index_features_title1') ?> <span class="gradient-text"><?= __('index_features_title2') ?></span></h2>
  <div class="FeaturesGrid">
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-cogs"></i></div>
      <h5><?= __('index_feature1_title') ?></h5>
      <p><?= __('index_feature1_desc') ?></p>
      <a href="./documentation.php" class="card-arrow" style="text-decoration:none;"><?= __('index_feature_link') ?> <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-gift"></i></div>
      <h5><?= __('index_feature2_title') ?></h5>
      <p><?= __('index_feature2_desc') ?></p>
      <a href="./add-game.php" class="card-arrow" style="text-decoration:none;"><?= __('index_feature_link') ?> <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-shield-alt"></i></div>
      <h5><?= __('index_feature3_title') ?></h5>
      <p><?= __('index_feature3_desc') ?></p>
      <a href="./game.php" class="card-arrow" style="text-decoration:none;"><?= __('index_feature_link') ?> <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-server"></i></div>
      <h5><?= __('index_feature4_title') ?></h5>
      <p><?= __('index_feature4_desc') ?></p>
      <a href="./documentation.php" class="card-arrow" style="text-decoration:none;"><?= __('index_feature_link') ?> <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-users-cog"></i></div>
      <h5><?= __('index_feature5_title') ?></h5>
      <p><?= __('index_feature5_desc') ?></p>
      <a href="./games.php" class="card-arrow" style="text-decoration:none;"><?= __('index_feature_link') ?> <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-users"></i></div>
      <h5><?= __('index_feature6_title') ?></h5>
      <p><?= __('index_feature6_desc') ?></p>
      <a href="https://discord.gg/85RCMD9VQD" class="card-arrow" style="text-decoration:none;" target="_blank"><?= __('index_feature_link') ?> <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fab fa-github"></i></div>
      <h5><?= __('index_feature7_title') ?></h5>
      <p><?= __('index_feature7_desc1') ?> <a href="https://github.com/manuel-di-iorio/gmiscores" style="color:var(--primary-color);"><?= __('index_feature7_github') ?></a>. <?= __('index_feature7_desc2') ?></p>
      <a href="https://github.com/manuel-di-iorio/gmiscores" class="card-arrow" style="text-decoration:none;" target="_blank"><?= __('index_feature_link') ?> <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-rocket"></i></div>
      <h5><?= __('index_feature8_title') ?></h5>
      <p><?= __('index_feature8_desc') ?></p>
      <a href="./documentation.php" class="card-arrow" style="text-decoration:none;"><?= __('index_feature_link') ?> <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</div>

<!-- ===== SERVICES ===== -->
<div id="servizi" class="section-container FeaturesSection">
  <h2 class="SectionTitle fade-in-up-on-scroll"><?= __('index_services_title') ?></h2>
  <div class="FeaturesGrid">
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-trophy"></i></div>
      <h5><?= __('index_service1_title') ?></h5>
      <p><?= __('index_service1_desc') ?></p>
      <a href="./add-game.php" class="card-arrow" style="text-decoration:none;"><?= __('index_service1_link') ?> <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-chart-pie"></i></div>
      <h5><?= __('index_service2_title') ?></h5>
      <p><?= __('index_service2_desc') ?></p>
      <a href="./games.php" class="card-arrow" style="text-decoration:none;"><?= __('index_service2_link') ?> <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-user-shield"></i></div>
      <h5><?= __('index_service3_title') ?></h5>
      <p><?= __('index_service3_desc') ?></p>
      <a href="./documentation.php" class="card-arrow" style="text-decoration:none;"><?= __('index_feature_link') ?> <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-cloud-upload-alt"></i></div>
      <h5><?= __('index_service4_title') ?></h5>
      <p><?= __('index_service4_desc') ?></p>
      <a href="./documentation.php" class="card-arrow" style="text-decoration:none;"><?= __('index_service4_link') ?> <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</div>

<!-- ===== STATS ===== -->
<div id="numeri" class="section-container StatsSection stats-gradient-section">
  <h2 class="SectionTitle fade-in-up-on-scroll"><?= __('index_stats_title1') ?> <span class="gradient-text"><?= __('index_stats_title2') ?></span></h2>
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
    <div class="StatCard stagger-item tilt-card">
      <div class="tilt-card__shine"></div>
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
<div id="faq" class="section-container FAQsSection">
  <h2 class="SectionTitle fade-in-up-on-scroll"><?= __('index_faq_title1') ?> <span class="gradient-text"><?= __('index_faq_title2') ?></span></h2>
  <div class="FAQsContainer">
    <div class="faq-item fade-in-up-on-scroll">
      <button class="faq-question">
        <span><?= __('index_faq1_q') ?></span>
        <span class="faq-icon"><i class="fas fa-plus"></i></span>
      </button>
      <div class="faq-answer"><?= __('index_faq1_a') ?></div>
    </div>
    <div class="faq-item fade-in-up-on-scroll anim-delay-100">
      <button class="faq-question">
        <span><?= __('index_faq2_q') ?></span>
        <span class="faq-icon"><i class="fas fa-plus"></i></span>
      </button>
      <div class="faq-answer"><?= __('index_faq2_a') ?></div>
    </div>
    <div class="faq-item fade-in-up-on-scroll anim-delay-200">
      <button class="faq-question">
        <span><?= __('index_faq3_q') ?></span>
        <span class="faq-icon"><i class="fas fa-plus"></i></span>
      </button>
      <div class="faq-answer"><?= __('index_faq3_a') ?></div>
    </div>
    <div class="faq-item fade-in-up-on-scroll anim-delay-300">
      <button class="faq-question">
        <span><?= __('index_faq4_q') ?></span>
        <span class="faq-icon"><i class="fas fa-plus"></i></span>
      </button>
      <div class="faq-answer"><?= __('index_faq4_a') ?></div>
    </div>
  </div>
</div>

<!-- ===== FINAL CTA ===== -->
<div id="contatti" class="section-container FinalCtaSection" style="text-align:center">
  <h4 class="fade-in-up-on-scroll">
    <strong><?= __('index_cta_title1') ?> <span class="gradient-text"><?= __('index_cta_title2') ?></span></strong>
  </h4>
  <p class="fade-in-up-on-scroll anim-delay-100">
    <?= __('index_cta_desc') ?>
  </p>
  <div class="fade-in-up-on-scroll anim-delay-200 ripple-btn" style="display:inline-block;">
    <a href="./add-game.php" class="CtaButton CtaButton--primary" style="display:inline-flex;align-items:center;gap:10px;padding:18px 44px;font-size:1.1em;text-decoration:none;">
      <i class="fas fa-rocket"></i> <?= __('index_cta_button') ?>
    </a>
  </div>
</div>

<script>
function switchThemeHome() {
  const theme = "<?= $theme === 'dark' ? 'light' : 'dark' ?>";
  location.href = "switch-theme.php?theme=" + theme + "&go=" + encodeURIComponent("<?= $_SERVER["REQUEST_URI"] ?>");
}
</script>
