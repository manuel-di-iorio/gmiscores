<?php
$pageURI = $_SERVER["REQUEST_URI"];
$isIndexPage = basename($pageURI) === 'index.php' || $pageURI === '/'; // Check for index.php or root
$gameNameShowBackIcon = strpos($pageURI, "/game-scores.php") === 0 || strpos($pageURI, "/game-bans.php") === 0 || 
  strpos($pageURI, "/game.php") === 0;
?>
<!DOCTYPE html>
<html lang="it">
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
    <meta property="og:site_name" content="GameMaker Italia">

    <!-- Style -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="assets/css/w3.css">
    <link rel="stylesheet" href="assets/css/toggle.css">
    <link rel="stylesheet" href="assets/css/variables-<?= $theme ?>.css?v=<?= $version ?>">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= $version ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  </head>

  <body class="w3-content">
    <?php if (!$isIndexPage) { // Conditionally include navbar
      require_once("includes/navbar.php"); 
    } ?>

    <div class="w3-main PageContent" <?php if ($isIndexPage) { echo 'style="margin-left: 0 !important;"'; } ?>>
      <!-- Header -->
      <?php if (!$isIndexPage) { ?>
        <header id="portfolio">
        <!-- Small logo shown on small screens -->
        <a href="./index.php"><img src="assets/images/logoSmall.png" class="w3-circle w3-right w3-margin w3-hide-large w3-hover-opacity LogoSmall"></a>

        <!-- Close sidebar button -->
        
        <span class="w3-button w3-hide-large w3-xxlarge w3-hover-text-grey" onclick="w3_open()"><i class="fas fa-bars"></i></span>

        <!-- Page title -->
        <div class="w3-container">
        <h1>
          <?php if ($gameNameShowBackIcon && $pageName !== $config["platformTitle"]) { // Non mostrare back icon e titolo se siamo sulla homepage ?>
            <a href="games.php" data-tippy-content="Torna alla lista giochi"><i class="fas fa-arrow-circle-left GameNameBackIcon"></i></a>
            <strong><?= htmlspecialchars($pageName) ?></strong>
          <?php } elseif ($pageName !== $config["platformTitle"] && !$isIndexPage) { // Mostra solo il titolo se non è la homepage e non siamo su index ?>
            <div class="page-title"><?= htmlspecialchars($pageName) ?></div>
          <?php } // Altrimenti (sulla homepage o se $isIndexPage è true) non mostrare nulla qui, sarà gestito da index.view.php ?>
        </h1>
        </div>
      </header>
      <?php } ?>

      <!-- Page content -->
      <?php require_once("pages/$view/$view.view.php"); ?>
    </div>

    <!-- Footer -->
    <footer class="modern-footer PageContentFooter" <?php if ($isIndexPage) { echo 'style="margin-left: 0 !important;"'; } ?>>
      <div class="footer-content w3-container">
        <div class="footer-section about">
          <h5 class="footer-heading">Classifica Online</h5>
          <p>Una piattaforma per leaderboard di giochi creata dalla community di GameMaker Italia.</p>
          <p>&copy; <?= date("Y") ?> GameMaker Italia. Tutti i diritti riservati.</p>
        </div>
        <div class="footer-section links">
          <h5 class="footer-heading">Link Utili</h5>
          <ul>
            <li><a href="/" class="footer-link">Home</a></li>
            <li><a href="/games.php" class="footer-link">I tuoi giochi</a></li>
            <li><a href="/documentation.php" class="footer-link">Documentazione API</a></li>
            <!-- Aggiungi altri link se necessario -->
          </ul>
        </div>
        <div class="footer-section social">
          <h5 class="footer-heading">Seguici</h5>
          <a href="https://discord.gg/XfMfpNA" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Discord"><i class="fab fa-discord"></i></a>
          <a href="https://www.facebook.com/gmitalia" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="https://twitter.com/gamemakerita" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
      <div class="footer-bottom">
        <p>Realizzato con <i class="fas fa-heart" style="color: red;"></i> da GMI</p>
      </div>
    </footer>
  </body>

  <!-- JS -->
  <script src="https://unpkg.com/@popperjs/core@2"></script>
  <script src="https://unpkg.com/tippy.js@6"></script>

  <script src="assets/js/main.js?v=<?= $version ?>" async></script>
  <script>
    // Initialize the tooltips
    tippy('[data-tippy-content]', { delay: [300, 200] });
  </script>
</html>
