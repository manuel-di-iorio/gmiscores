<?php
require_once(__DIR__ . "/../lib/csrf.php");
$pageURI = $_SERVER["REQUEST_URI"];
$isIndexPage = false;
$layoutPageTitle = $pageName ?? $config["platformTitle"];
header("Cache-Control: private, must-revalidate");
require_once __DIR__ . '/../assets/ui-kit/kit.php';
?>
<!DOCTYPE html>
<html lang="<?= __("html_lang") ?>">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/assets/images/favicon.ico">
    <title><?= $config["platformTitle"] ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="/assets/css/variables.css?v=<?= asset_version('assets/css/variables.css') ?>">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= asset_version('assets/css/style.css') ?>">
    <link rel="stylesheet" href="/assets/css/layout.css?v=<?= asset_version('assets/css/layout.css') ?>">
    <link rel="stylesheet" href="/assets/css/internal-pages.css?v=<?= asset_version('assets/css/internal-pages.css') ?>">
    <link rel="stylesheet" href="/assets/css/cookie-banner.css?v=<?= asset_version('assets/css/cookie-banner.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/assets/ui-kit/Button/button.css?v=<?= asset_version('assets/ui-kit/Button/button.css') ?>">
    <link rel="stylesheet" href="/assets/ui-kit/Spinner/spinner.css?v=<?= asset_version('assets/ui-kit/Spinner/spinner.css') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        corePlugins: { preflight: false },
        darkMode: 'class',
        theme: {
          extend: {
            colors: {
              primary: { 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' },
            },
          },
        },
      }
    </script>
  </head>
  <body class="minimal-layout<?= $theme === 'dark' ? ' dark' : '' ?>">
    <?= ui_toast_container() ?>

    <?php
      require_once(__DIR__ . "/../pages/$view/$view.view.php");
    ?>

    <?php require_once(__DIR__ . "/footer.php"); ?>
  </body>
</html>
