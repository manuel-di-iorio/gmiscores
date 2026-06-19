<?php
$pageURI = $_SERVER["REQUEST_URI"];
$isIndexPage = basename($pageURI) === 'index.php' || $pageURI === '/'; // Check for index.php or root
$gameNameShowBackIcon = strpos($pageURI, "/game-scores.php") === 0 || strpos($pageURI, "/game-scores-export.php") === 0 || strpos($pageURI, "/game-scores-import.php") === 0 || strpos($pageURI, "/game-bans.php") === 0 || 
  strpos($pageURI, "/game.php") === 0 || strpos($pageURI, "/leaderboards.php") === 0 ||
  strpos($pageURI, "/add-team.php") === 0 || strpos($pageURI, "/team-move-game.php") === 0 ||
  strpos($pageURI, "/team.php") === 0;
$backUrl = $backUrl ?? "games.php";
$layoutPageTitle = $pageName ?? $pageTitle ?? $config["platformTitle"];
$layoutPageDesc = $pageDesc ?? '';
$layoutPageBackUrl = $gameNameShowBackIcon ? $backUrl : '';
header("Cache-Control: private, must-revalidate");
require_once __DIR__ . '/../assets/ui-kit/kit.php';
?>
<!DOCTYPE html>
<html lang="<?= __("html_lang") ?>">
  <head>
    <!-- Google Analytics -->
    <?php if ($config["analytics"]) { ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $config["analyticsId"] ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      <?php if (isset($user)) { ?>gtag('set', {'user_id': <?= $user["id"] ?>});<?php } ?>
      gtag('config', '<?= $config["analyticsId"] ?>');
    </script>
    <?php } ?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Social meta -->
    <title><?= $config["platformTitle"] ?></title>
    <meta name="description" content="<?= $config["platformDescription"] ?>">
    <meta property="og:title" content="<?= $config["platformTitle"]; ?>">
    <meta property="og:description" content="<?= $config["platformDescription"] ?>">
    <meta property="og:image" content="<?= $config["logo"] ?>">
    <meta property="og:image:width" content="<?= $config["logoWidth"] ?>">
    <meta property="og:image:height" content="<?= $config["logoHeight"] ?>">
    <meta property="og:site_name" content="<?= $config["platformTitle"] ?>">

    <!-- Style -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="assets/css/variables.css?v=<?= asset_version('assets/css/variables.css') ?>">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= asset_version('assets/css/style.css') ?>">
    <link rel="stylesheet" href="assets/css/w3codecolor.css?v=<?= asset_version('assets/css/w3codecolor.css') ?>">
    <?php if ($isIndexPage): ?>
    <link rel="stylesheet" href="assets/css/landing.css?v=<?= asset_version('assets/css/landing.css') ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/ui-kit/Button/button.css?v=<?= asset_version('assets/ui-kit/Button/button.css') ?>">
    <link rel="stylesheet" href="assets/ui-kit/Modal/modal.css?v=<?= asset_version('assets/ui-kit/Modal/modal.css') ?>">
    <link rel="stylesheet" href="assets/ui-kit/Tabs/tabs.css?v=<?= asset_version('assets/ui-kit/Tabs/tabs.css') ?>">
    <link rel="stylesheet" href="assets/ui-kit/Skeleton/skeleton.css?v=<?= asset_version('assets/ui-kit/Skeleton/skeleton.css') ?>">
    <link rel="stylesheet" href="assets/ui-kit/Table/table.css?v=<?= asset_version('assets/ui-kit/Table/table.css') ?>">
    <link rel="stylesheet" href="assets/ui-kit/Paginator/paginator.css?v=<?= asset_version('assets/ui-kit/Paginator/paginator.css') ?>">
    <link rel="stylesheet" href="assets/css/cookie-banner.css?v=<?= asset_version('assets/css/cookie-banner.css') ?>">
    <link rel="stylesheet" href="assets/css/navbar.css?v=<?= asset_version('assets/css/navbar.css') ?>">
    <link rel="stylesheet" href="assets/css/layout.css?v=<?= asset_version('assets/css/layout.css') ?>">
    <link rel="stylesheet" href="assets/css/internal-pages.css?v=<?= asset_version('assets/css/internal-pages.css') ?>">
    <link rel="stylesheet" href="assets/css/documentation.css?v=<?= asset_version('assets/css/documentation.css') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
      tailwind.config = {
        corePlugins: { preflight: false },
        darkMode: 'class',
        theme: {
          extend: {
            colors: {
              primary: {
                50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd',
                400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
                800: '#1e40af', 900: '#1e3a8a',
              },
              surface: {
                DEFAULT: 'var(--bg-color)',
                sidebar: 'var(--bg-color-sidebar)',
                reversed: 'var(--bg-color--reversed)',
                card: 'var(--bg-color-card)',
                offset: 'var(--bg-color-offset)',
                'offset-hover': 'var(--bg-color-offset-hover)',
                code: 'var(--bg-color-code)',
                'sidebar-footer': 'var(--bg-color-sidebar-footer)',
                'section-alt': 'var(--section-alt-bg)',
              },
              text: {
                DEFAULT: 'var(--text-color)',
                reversed: 'var(--text-color--reversed)',
                headings: 'var(--text-color-headings)',
                secondary: 'var(--text-color-secondary)',
                primary: 'var(--text-color-primary)',
                code: 'var(--text-color-code)',
                'sidebar-link': 'var(--text-color-sidebar-link)',
                'sidebar-link-hover': 'var(--text-color-sidebar-link-hover)',
              },
              input: {
                bg: 'var(--input-bg)',
                'bg-disabled': 'var(--input-bg--disabled)',
                text: 'var(--input-text)',
                'text-disabled': 'var(--input-text--disabled)',
              },
              table: {
                border: 'var(--table-border-color)',
                'header-bg': 'var(--table-header-bg)',
                'header-text': 'var(--table-header-text-color)',
                'row-even': 'var(--table-row-even-bg)',
                'row-hover': 'var(--table-row-hover-bg)',
                'cell-text': 'var(--table-cell-text-color)',
                'action-icon': 'var(--table-action-icon-color)',
                'action-icon-hover': 'var(--table-action-icon-hover-color)',
                'action-icon-hover-bg': 'var(--table-action-icon-hover-bg)',
                line: 'var(--table-line-color)',
              },
              button: {
                bg: 'var(--button-bg)',
                'bg-hover': 'var(--button-bg--hover)',
                text: 'var(--button-text-color)',
              },
              pagination: {
                'hover-bg': 'var(--pagination-hover-bg)',
                'hover-text': 'var(--pagination-hover-text)',
                'active-bg': 'var(--pagination-active-bg)',
                'active-text': 'var(--pagination-active-text)',
                'disabled-bg': 'var(--pagination-disabled-bg)',
                'disabled-text': 'var(--pagination-disabled-text)',
              },
              navbar: {
                bg: 'var(--navbar-bg)',
                border: 'var(--navbar-border-color)',
                text: 'var(--navbar-text-color)',
                'logo-border': 'var(--navbar-logo-border-color)',
                link: 'var(--navbar-link-color)',
                'link-hover-bg': 'var(--navbar-link-hover-bg)',
                'link-hover-border': 'var(--navbar-link-hover-border-color)',
                'link-hover': 'var(--navbar-link-hover-color)',
                'link-active-bg': 'var(--navbar-link-active-bg)',
                'link-active': 'var(--navbar-link-active-color)',
                'button-bg': 'var(--navbar-button-bg)',
                'button-text': 'var(--navbar-button-text-color)',
                'button-hover-bg': 'var(--navbar-button-hover-bg)',
                'button-hover-text': 'var(--navbar-button-hover-text-color)',
              },
              'border-color': {
                DEFAULT: 'var(--border-color)',
                sidebar: 'var(--border-color-sidebar)',
                soft: 'var(--border-color)',
              },
              'primary-color': {
                DEFAULT: 'var(--primary-color)',
                light: 'var(--primary-color-light)',
                dark: 'var(--primary-color-dark)',
                darker: 'var(--primary-color-darker)',
              },
              accent: {
                DEFAULT: 'var(--accent-color)',
                hover: 'var(--accent-color-hover)',
              },
              secondary: 'var(--accent-color)',
              info: {
                'panel-bg': 'var(--info-panel-bg)',
                'panel-text': 'var(--info-panel-text)',
                'panel-border': 'var(--info-panel-border)',
              },
              cookie: {
                'banner-bg': 'var(--cookie-banner-bg)',
                'banner-text': 'var(--cookie-banner-text-color)',
                'banner-link': 'var(--cookie-banner-link-color)',
                'banner-link-hover': 'var(--cookie-banner-link-hover-color)',
              },
              toggle: {
                bg: 'var(--toggle-bg)',
                'bg-checked': 'var(--toggle-bg--checked)',
                'knob-bg': 'var(--toggle-knob-bg)',
              },
              'code-syntax': {
                DEFAULT: 'var(--code-syntax-default)',
                keyword: 'var(--code-syntax-keyword)',
                string: 'var(--code-syntax-string)',
                number: 'var(--code-syntax-number)',
                property: 'var(--code-syntax-property)',
                comment: 'var(--code-syntax-comment)',
                regexp: 'var(--code-syntax-regexp)',
                stringtemp: 'var(--code-syntax-stringtemp)',
              },
              cta: {
                'button-bg': 'var(--cta-button-bg)',
                'button-text': 'var(--cta-button-text)',
              },
              footer: {
                bg: 'var(--footer-bg)',
                text: 'var(--footer-text-color)',
                border: 'var(--footer-border-color)',
                heading: 'var(--footer-heading-color)',
                link: 'var(--footer-link-color)',
                'link-hover': 'var(--footer-link-hover-color)',
                'social-icon': 'var(--footer-link-color)',
                'social-icon-hover': 'var(--footer-link-hover-color)',
              },
              gradient: {
                start: 'var(--gradient-start)',
                mid: 'var(--gradient-mid)',
                end: 'var(--gradient-end)',
              },
              glass: {
                bg: 'var(--glass-bg)',
                border: 'var(--glass-border)',
                'border-hover': 'var(--glass-border-hover)',
              },
              glow: 'var(--glow-color)',
              header: {
                bg: 'var(--header-bg)',
                border: 'var(--header-border)',
              },
              'nav-hover': 'var(--nav-hover-bg)',
              overlay: {
                1: 'rgb(var(--overlay-color-1))',
                2: 'rgb(var(--overlay-color-2))',
                3: 'rgb(var(--overlay-color-3))',
              },
              scrollbar: {
                thumb: 'var(--scrollbar-thumb)',
                'thumb-hover': 'var(--scrollbar-thumb-hover)',
              },
              divider: 'var(--divider-fill)',
              'progress-bar': 'var(--progress-bar-bg)',
              'mesh-dot': 'var(--mesh-dot)',
              'card-bg-hover': 'var(--card-bg-hover)',
              hr: 'var(--hr-color)',
            },
            boxShadow: {
              'card-right': 'var(--shadow-1--right)',
              'card-lg': 'var(--shadow-2)',
              card: 'var(--shadow-1)',
              navbar: 'var(--shadow-navbar)',
              'card-prominent': 'var(--shadow-2-prominent)',
              'card-subtle': 'var(--shadow-1-subtle)',
              footer: 'var(--shadow-footer)',
            },
            backgroundImage: {
              cta: 'var(--gradient-cta)',
            },
          },
        },
      }
    </script>
  </head>

  <body<?= $theme === 'dark' ? ' class="dark"' : '' ?>>
    <div id="cookie-banner" style="display: none;">
        <div class="cookie-banner-content">
          <div>
            <p><?= __("cookie_banner_text") ?> <a href="cookie.php"><?= __("cookie_banner_link") ?></a></p>
          </div>
        </div>
        <?= ui_button( __('cookie_banner_accept'), 'primary', 'sm', ['attrs' => ['id' => 'accept-cookie-banner']]) ?>
    </div>
    <?php if ($config["maintenance"]) { ?>
      <div class="bg-amber-500 text-black text-center px-4 py-2 m-0 rounded-none">
        <i class="fas fa-tools mr-2"></i><?= htmlspecialchars($config["maintenanceMessage"]) ?>
      </div>
    <?php } ?>
    <?php if (!$isIndexPage) { // Conditionally include navbar
      require_once("includes/navbar.php"); 
    } ?>

    <div class="PageContent" <?php if ($isIndexPage || !empty($hidePageHeader)) { echo 'style="margin-left: 0 !important; padding: 0 !important;"'; } ?>>
      <!-- Header -->
      <?php if (!$isIndexPage && empty($hidePageHeader)) { ?>
        <header id="portfolio" style="padding-bottom:0">
          <!-- Small logo shown on small screens -->
          <a href="./index.php"><img src="assets/images/logo.svg" class="shape-circle LogoSmall float-right m-4 hidden" id="logo-small"></a>

          <!-- Close sidebar button -->
          <span id="btn-sidebar-open" class="hidden text-[32px] cursor-pointer px-4 py-2" onclick="w3_open()"><i class="fas fa-bars"></i></span>

          <!-- Page header -->
          <?php if ($layoutPageTitle !== $config["platformTitle"] && !$isIndexPage) { ?>
            <?= ui_page_header(
              $layoutPageTitle,
              [
                'desc' => $layoutPageDesc,
                'back_url' => $layoutPageBackUrl,
                'back_label' => __("back_tooltip"),
              ]
            ) ?>
          <?php } ?>
      </header>
      <?php } ?>

      <!-- Page content -->
      <?php
        // Make table filters available to all views
        require_once("includes/table-filters.php");
        require_once("pages/$view/$view.view.php");
      ?>
    </div>

    <!-- Footer -->
    <footer class="modern-footer PageContentFooter" <?php if ($isIndexPage) { echo 'style="margin-left: 0 !important;"'; } ?>>
      <div class="footer-content">
        <div class="footer-section about">
          <img src="assets/images/logo<?= $theme === 'dark' ? 'White' : '' ?>.svg" class="footer-logo" alt="Logo">
          <p><?= __("footer_about") ?></p>
          <p>&copy; <?= date("Y") ?> GameMaker Italia. <?= __("footer_copyright") ?></p>
        </div>
        <div class="footer-section links">
          <h5 class="footer-heading"><?= __("footer_links_title") ?></h5>
          <ul>
            <li><a href="/documentation.php" class="footer-link"><?= __("footer_documentation") ?></a></li>
            <li><a href="/terms.php" class="footer-link"><?= __("footer_terms") ?></a></li>
            <li><a href="/privacy.php" class="footer-link"><?= __("footer_privacy") ?></a></li>
            <li><a href="/cookie.php" class="footer-link"><?= __("footer_cookie") ?></a></li>
          </ul>
        </div>
        <div class="footer-section social">
          <h5 class="footer-heading"><?= __("footer_follow_title") ?></h5>
          <a href="https://discord.gg/85RCMD9VQD" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Discord"><i class="fab fa-discord"></i></a>
          <a href="https://www.facebook.com/gmitalia" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="https://twitter.com/gamemakerita" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
      <div class="footer-bottom">
        <a href="switch-theme.php?theme=<?= $theme === 'dark' ? 'light' : 'dark' ?>&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="footer-theme-link">
          <i class="fas <?= $theme === 'dark' ? 'fa-sun' : 'fa-moon' ?>"></i> <?= __('index_theme_toggle') ?>
        </a>
        <div class="footer-lang">
          <a href="/switch-lang.php?lang=en&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="footer-lang-link<?php if ($currentLang === 'en') { echo ' footer-lang-link--active'; } ?>"><?= __("lang_en") ?></a>
          <span class="footer-lang-sep">|</span>
          <a href="/switch-lang.php?lang=it&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="footer-lang-link<?php if ($currentLang === 'it') { echo ' footer-lang-link--active'; } ?>"><?= __("lang_it") ?></a>
          <span class="footer-lang-sep">|</span>
          <a href="/switch-lang.php?lang=es&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="footer-lang-link<?php if ($currentLang === 'es') { echo ' footer-lang-link--active'; } ?>"><?= __("lang_es") ?></a>
          <span class="footer-lang-sep">|</span>
          <a href="/switch-lang.php?lang=fr&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="footer-lang-link<?php if ($currentLang === 'fr') { echo ' footer-lang-link--active'; } ?>"><?= __("lang_fr") ?></a>
          <span class="footer-lang-sep">|</span>
          <a href="/switch-lang.php?lang=de&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="footer-lang-link<?php if ($currentLang === 'de') { echo ' footer-lang-link--active'; } ?>"><?= __("lang_de") ?></a>
        </div>
      </div>
    </footer>
  </body>

  <style>
    .footer-logo { max-width: 120px; height: auto; margin-bottom: 12px; }
    .footer-bottom { display: flex; align-items: center; justify-content: center; gap: 50px; flex-wrap: wrap; }
    .footer-bottom p { margin: 0; }
    .footer-theme-link { color: var(--text-color-secondary, #9ca3af); text-decoration: none; font-size: 0.82em; transition: color 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .footer-theme-link:hover { color: var(--text-color, #e5e7eb); }
    .footer-lang { display: flex; align-items: center; gap: 8px; }
    .footer-lang-link { color: var(--text-color-secondary, #9ca3af); text-decoration: none; font-size: 0.82em; transition: color 0.2s; }
    .footer-lang-link:hover { color: var(--text-color, #e5e7eb); }
    .footer-lang-link--active { color: var(--primary-color, #6366f1); font-weight: 600; }
    .footer-lang-sep { color: var(--text-color-secondary, #9ca3af); font-size: 0.82em; opacity: 0.4; }
    .nav-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 18px;
      height: 18px;
      padding: 0 5px;
      border-radius: 999px;
      background: #ef4444;
      color: #fff;
      font-size: 0.7em;
      font-weight: 700;
      margin-left: 6px;
      vertical-align: middle;
      line-height: 1;
    }
  </style>

  <!-- JS -->
  <script src="https://unpkg.com/@popperjs/core@2"></script>
  <script src="https://unpkg.com/tippy.js@6"></script>

  <script src="assets/js/main.js?v=<?= asset_version('assets/js/main.js') ?>" async></script>
  <script>
    // Initialize the tooltips
    tippy('[data-tippy-content]', { delay: [300, 200] });
  </script>
</html>
