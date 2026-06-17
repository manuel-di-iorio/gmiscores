<?php
$pageURI = $_SERVER["REQUEST_URI"];
$isIndexPage = basename($pageURI) === 'index.php' || $pageURI === '/'; // Check for index.php or root
$gameNameShowBackIcon = strpos($pageURI, "/game-scores.php") === 0 || strpos($pageURI, "/game-bans.php") === 0 || 
  strpos($pageURI, "/game.php") === 0 || strpos($pageURI, "/leaderboards.php") === 0;
$backUrl = $backUrl ?? "games.php";
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
    <meta property="og:site_name" content="<?= __("site_name") ?>">

    <!-- Style -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="assets/css/toggle.css?v=<?= asset_version('assets/css/toggle.css') ?>">
    <link rel="stylesheet" href="assets/css/variables-<?= $theme ?>.css?v=<?= asset_version('assets/css/variables-' . $theme . '.css') ?>">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= asset_version('assets/css/style.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/ui-kit/Button/button.css?v=<?= asset_version('assets/ui-kit/Button/button.css') ?>">
    <link rel="stylesheet" href="assets/ui-kit/Card/card.css?v=<?= asset_version('assets/ui-kit/Card/card.css') ?>">
    <link rel="stylesheet" href="assets/ui-kit/Input/input.css?v=<?= asset_version('assets/ui-kit/Input/input.css') ?>">
    <link rel="stylesheet" href="assets/ui-kit/Modal/modal.css?v=<?= asset_version('assets/ui-kit/Modal/modal.css') ?>">
    <link rel="stylesheet" href="assets/ui-kit/Table/table.css?v=<?= asset_version('assets/ui-kit/Table/table.css') ?>">
    <link rel="stylesheet" href="assets/ui-kit/Icon/icon.css?v=<?= asset_version('assets/ui-kit/Icon/icon.css') ?>">
    <link rel="stylesheet" href="assets/ui-kit/Tabs/tabs.css?v=<?= asset_version('assets/ui-kit/Tabs/tabs.css') ?>">
    <link rel="stylesheet" href="assets/ui-kit/Badge/badge.css?v=<?= asset_version('assets/ui-kit/Badge/badge.css') ?>">
    <link rel="stylesheet" href="assets/ui-kit/Toggle/toggle.css?v=<?= asset_version('assets/ui-kit/Toggle/toggle.css') ?>">
    <link rel="stylesheet" href="assets/ui-kit/Skeleton/skeleton.css?v=<?= asset_version('assets/ui-kit/Skeleton/skeleton.css') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
      tailwind.config = {
        corePlugins: { preflight: false },
        theme: {
          extend: {
            colors: {
              primary: {
                50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd',
                400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
                800: '#1e40af', 900: '#1e3a8a',
              }
            }
          }
        }
      }
    </script>
  </head>

  <body>
    <div id="cookie-banner" style="display: none;">
        <div class="cookie-banner-content">
          <div>
            <p><?= __("cookie_banner_text") ?> <a href="cookie.php"><?= __("cookie_banner_link") ?></a></p>
          </div>
        </div>
        <?= ui_button( __('cookie_banner_accept'), 'primary', 'sm', ['attrs' => ['id' => 'accept-cookie-banner']]) ?>
    </div>
    <?php if ($config["maintenance"]) { ?>
      <div style="background:#f59e0b;color:#000;text-align:center;padding:8px 16px;margin:0;border-radius:0;">
        <i class="fas fa-tools" style="margin-right:8px"></i><?= htmlspecialchars($config["maintenanceMessage"]) ?>
      </div>
    <?php } ?>
    <?php if (!$isIndexPage) { // Conditionally include navbar
      require_once("includes/navbar.php"); 
    } ?>

    <div class="PageContent" <?php if ($isIndexPage) { echo 'style="margin-left: 0 !important; padding: 0 !important;"'; } ?>>
      <!-- Header -->
      <?php if (!$isIndexPage) { ?>
        <header id="portfolio" style="padding-bottom:0">
        <!-- Small logo shown on small screens -->
        <a href="./index.php"><img src="assets/images/logoSmall.png" class="shape-circle LogoSmall" style="float:right;margin:16px;display:none" id="logo-small"></a>

        <!-- Close sidebar button -->
        <span id="btn-sidebar-open" style="display:none;font-size:32px;cursor:pointer;padding:8px 16px" onclick="w3_open()"><i class="fas fa-bars"></i></span>

        <!-- Page title -->
        <div style="padding:0.01em 0;padding-bottom:0">
        <h1>
          <?php if ($pageName !== $config["platformTitle"] && !$isIndexPage) { ?>
            <div class="page-title">
              <?php if ($gameNameShowBackIcon) { ?>
                <a href="<?= $backUrl ?>" data-tippy-content="<?= __("back_tooltip") ?>"><i class="fas fa-arrow-circle-left GameNameBackIcon"></i></a>
              <?php } ?>
              <?= htmlspecialchars($pageName) ?>
            </div>
          <?php } ?>
        </h1>
        </div>
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
