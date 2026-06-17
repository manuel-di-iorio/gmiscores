<?php 
$navbarLogoColor = $theme === "dark" ? "White" : ""; 
$navbarThemeReversed = $theme === "dark" ? "light" : "dark";

$navbarItems = [
  ["label" => __("nav_dashboard"), "url" => isset($user) ? "/home.php" : "/", "icon" => "home", "showOnlyLogged" => false],
  ["label" => __("nav_your_games"), "url" => "/games.php", "icon" => "gamepad", "showOnlyLogged" => true],
  ["label" => __("nav_documentation"), "url" => "/documentation.php", "icon" => "book", "showOnlyLogged" => false]
];

$isAdminUser = isset($user) && isset($user["admin"]) && (int)$user["admin"] === 1;
if ($isAdminUser) {
  $adminLabel = __("nav_admin");
  $pendingCount = User::countUnapproved();
  if ($pendingCount > 0) {
    $adminLabel .= ' <span class="nav-badge">' . $pendingCount . '</span>';
  }
  $navbarItems[] = ["label" => $adminLabel, "url" => "/admin.php", "icon" => "cogs", "showOnlyLogged" => true, "allowHtml" => true];
}
?>

<!-- Overlay effect when opening sidebar on small screens -->
<div class="NavbarOverlay" id="overlay" onclick="w3_close()"></div>

<nav id="navbar">
  <div class="LogoContainer">
    <!-- Logo -->
    <a href="./index.php" class="navbar-logo-link">
      <img src="assets/images/logo<?= $navbarLogoColor ?>.svg" class="round Logo" alt="Logo Piattaforma">
    </a>
  </div>
  
  <!-- Menu items -->
  <div class="navbar-menu">
    <?php foreach ($navbarItems as $navbarItem) {

      if (!$navbarItem["showOnlyLogged"] || isset($user)) { ?>
        <a href="<?= $navbarItem["url"] ?>"
        class="navbar-item <?php if ($navbarItem["url"] === $pageURI) { echo "active-link"; } ?>">
          <i class="fas fa-<?= $navbarItem["icon"] ?> fa-fw" style="margin-right:16px"></i>
          <span class="navbar-item-label"><?= isset($navbarItem["allowHtml"]) && $navbarItem["allowHtml"] ? $navbarItem["label"] : htmlspecialchars($navbarItem["label"]) ?></span>
        </a>
      <?php } ?>

    <?php } ?>
  </div>

  <!-- User box -->
  <hr class="navbar-divider"/>
  <div class="UserBox">
  
  <?php if (isset($user)) { ?>      
    <div class="user-info">
      <span class="username"><?= $user["username"] ?></span>
    </div>
    <a href="logout.php" class="navbar-logout-icon" title="<?= __('nav_logout') ?>"><i class="fas fa-sign-out-alt"></i></a>
  <?php } else { ?>
    <a href="login.php" class="navbar-login-btn"><?= __('nav_login') ?></a>
  <?php } ?>

  </div>
</nav>


