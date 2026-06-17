<?php 
$navbarLogoColor = $theme === "dark" ? "White" : ""; 
$navbarThemeReversed = $theme === "dark" ? "light" : "dark";

$navbarItems = [
  ["label" => __("nav_dashboard"), "url" => isset($user) ? "/home.php" : "/", "icon" => "home", "showOnlyLogged" => false],
  ["label" => __("nav_your_games"), "url" => "/games.php", "icon" => "gamepad", "showOnlyLogged" => true],
  ["label" => __("nav_documentation"), "url" => "/documentation.php", "icon" => "book", "showOnlyLogged" => false]
];
?>

<!-- Overlay effect when opening sidebar on small screens -->
<div class="NavbarOverlay" id="overlay" onclick="w3_close()"></div>

<nav id="navbar">
  <div class="LogoContainer">
    <!-- Logo -->
    <a href="./index.php" class="navbar-logo-link">
      <img src="assets/images/logo<?= $navbarLogoColor ?>.png" class="round Logo" alt="Logo Piattaforma">
    </a>
    <!-- Brand title -->
    <h4 class="BrandTitle"><?= __("site_name") ?></h4>
  </div>
  
  <!-- Menu items -->
  <div class="navbar-menu">
    <?php foreach ($navbarItems as $navbarItem) {

      if (!$navbarItem["showOnlyLogged"] || isset($user)) { ?>
        <a href="<?= $navbarItem["url"] ?>"
        class="navbar-item <?php if ($navbarItem["url"] === $pageURI) { echo "active-link"; } ?>">
          <i class="fas fa-<?= $navbarItem["icon"] ?> fa-fw" style="margin-right:16px"></i>
          <span class="navbar-item-label"><?= $navbarItem["label"] ?></span>
        </a>
      <?php } ?>

    <?php } ?>
    
    <!-- User box -->
    <hr class="navbar-divider"/>
    <div class="UserBox">
    
    <?php if (isset($user)) { ?>      
      <div class="user-info">
        <!-- <?php if (isset($user["_avatarUrl"])) { ?>
        <img src="<?= $user["_avatarUrl"] ?>" class="shape-circle NavbarUserAvatar" alt="User avatar">
        <?php } ?> -->
        <?= __("nav_greeting") ?>&nbsp;<span class="username"><?= $user["username"] ?></span>
      </div>
      <?= ui_button( __('nav_logout'), 'ghost', 'sm', ['icon' => 'fas fa-sign-out-alt fa-fw', 'href' => 'logout.php', 'class' => 'full-width logout-button']) ?>
    <?php } else { ?>
      <?= ui_button( __('nav_login'), 'primary', 'sm', ['icon' => 'fas fa-sign-in-alt fa-fw', 'href' => 'login.php', 'class' => 'full-width login-button']) ?>
    <?php } ?>

    </div>
  </div>

  <!-- Theme switcher -->
  <div class="navbar__theme-switcher" style="text-align:center" onclick="switchTheme()">
    <span class="navbar__theme-switcher__label"><?= __("nav_dark_theme") ?></span>
    <label class="switch">
      <input id="input-switch-theme" type="checkbox" <?php if ($theme === "dark") { echo "checked"; } ?>>
      <span class="slider round"></span>
    </label>
  </div>
</nav>

<script>
  let switchingTheme = false;

  /** Switch the website theme */
  function switchTheme() {
    if (switchingTheme) return;
    switchingTheme = true;
    const switcher = document.getElementById("input-switch-theme");
    switcher.checked = !switcher.checked;
    setTimeout(() => {
      location.href = "switch-theme.php?theme=<?= $navbarThemeReversed ?>&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>";
    }, 200);
  }
</script>
