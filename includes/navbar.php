<?php 
$navbarLogoColor = $theme === "dark" ? "White" : ""; 
$navbarThemeReversed = $theme === "dark" ? "white" : "dark";

$navbarItems = [
  ["label" => "Home", "url" => "/", "icon" => "home", "showOnlyLogged" => false],
  ["label" => "I tuoi giochi", "url" => "/games.php", "icon" => "gamepad", "showOnlyLogged" => false],
  ["label" => "Documentazione", "url" => "/documentation.php", "icon" => "book", "showOnlyLogged" => false]
];
?>

<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large w3-animate-opacity NavbarOverlay" onclick="w3_close()" id="overlay"></div>

<nav class="w3-sidebar w3-collapse" id="navbar">
  <div class="w3-container w3-margin-bottom LogoContainer">
    <!-- Logo -->
    <a href="./index.php" class="navbar-logo-link">
      <img src="assets/images/logo<?= $navbarLogoColor ?>.png" class="w3-round Logo" alt="Logo Piattaforma">
    </a>
    <!-- Brand title -->
    <h4 class="BrandTitle">Classifica online</h4>
  </div>
  
  <!-- Menu items -->
  <div class="w3-bar-block">
    <?php foreach ($navbarItems as $navbarItem) {

      if (!$navbarItem["showOnlyLogged"] || isset($user)) { ?>
        <a href="<?= $navbarItem["url"] ?>"
        class="w3-bar-item w3-button w3-padding navbar-item <?php if ($navbarItem["url"] === $pageURI) { echo "active-link"; } ?>">
          <i class="fas fa-<?= $navbarItem["icon"] ?> fa-fw w3-margin-right"></i>
          <span class="navbar-item-label"><?= $navbarItem["label"] ?></span>
        </a>
      <?php } ?>

    <?php } ?>
    
    <!-- User box -->
    <hr class="navbar-divider"/>
    <div class="w3-bar-item UserBox">
    
    <?php if (isset($user)) { ?>      
      <div class="user-info">
        <!-- <?php if (isset($user["_avatarUrl"])) { ?>
        <img src="<?= $user["_avatarUrl"] ?>" class="w3-circle NavbarUserAvatar" alt="User avatar">
        <?php } ?> -->
        Ciao&nbsp;<span class="username"><?= $user["username"] ?></span>
      </div>
      <a href="logout.php" class="w3-button w3-block logout-button"><i class="fas fa-sign-out-alt fa-fw w3-margin-right"></i>Esci</a>
    <?php } else { ?>
      <a href="login.php" class="w3-button w3-block login-button"><i class="fas fa-sign-in-alt fa-fw w3-margin-right"></i> Accedi</a>
    <?php } ?>

    </div>
  </div>

  <!-- Theme switcher -->
  <?php /*
  <div class="navbar__theme-switcher w3-center" onclick="switchTheme()">
    <span class="navbar__theme-switcher__label">Dark theme</span>
    <label class="switch">
      <input id="input-switch-theme" type="checkbox" <?php if ($theme === "dark") { echo "checked"; } ?> disabled>
      <span class="slider round"></span>
    </label>
  </div>
  */ ?>
</nav>

<script>
  // let switchingTheme = false;

  // /** Switch the website theme */
  // function switchTheme() {
  //   if (switchingTheme) return;
  //   switchingTheme = true;
  //   const switcher = document.getElementById("input-switch-theme");
  //   switcher.checked = !switcher.checked;
  //   setTimeout(() => {
  //     location.href = "switch-theme.php?theme=<?= $navbarThemeReversed ?>&go=<?= $_SERVER["REQUEST_URI"] ?>";
  //   }, 200);
  // }
</script>
