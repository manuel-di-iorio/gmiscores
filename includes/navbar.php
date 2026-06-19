<?php 
$navbarLogoColor = $theme === "dark" ? "White" : ""; 
$navbarThemeReversed = $theme === "dark" ? "light" : "dark";

$navbarItems = [
  ["label" => __("nav_dashboard"), "url" => isset($user) ? "/home.php" : "/", "icon" => "home", "showOnlyLogged" => false],
  ["label" => __("nav_your_games"), "url" => "/games.php", "icon" => "gamepad", "showOnlyLogged" => true],
  ["label" => __("nav_your_teams"), "url" => "/teams.php", "icon" => "users", "showOnlyLogged" => true],
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
  $navbarItems[] = ["label" => "Performance", "url" => "/admin-performance.php", "icon" => "tachometer-alt", "showOnlyLogged" => true];
}

$userTeamsList = [];
if (isset($user)) {
  require_once __DIR__ . '/../models/Team.php';
  $utResult = Team::listByUser($user["id"]);
  while ($utRow = $utResult->fetch_assoc()) {
    $userTeamsList[] = $utRow;
  }
}

$selectedTeamId = isset($_COOKIE['selected_team_id']) && $_COOKIE['selected_team_id'] !== '' ? (int)$_COOKIE['selected_team_id'] : null;
if ($selectedTeamId !== null && isset($user)) {
  $found = false;
  foreach ($userTeamsList as $ut) {
    if ((int)$ut['team_id'] === (int)$selectedTeamId) { $found = true; break; }
  }
  if (!$found) $selectedTeamId = null;
}
?>

<div class="NavbarOverlay" id="overlay" onclick="w3_close()"></div>

<nav id="navbar">
  <div class="LogoContainer">
    <a href="./index.php" class="navbar-logo-link">
      <img src="assets/images/logo<?= $navbarLogoColor ?>.svg" class="round Logo" alt="Logo Piattaforma">
    </a>
  </div>
  
  <div class="navbar-menu">
    <?php if (isset($user)) { ?>
      <div style="padding:8px 16px;margin-top:8px;margin-bottom:8px">
        <label style="font-size:0.72em;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-color-secondary,#6b7280);font-weight:600;display:block;margin-bottom:8px"><?= __('team_selector_label') ?></label>
          <select onchange="document.cookie='selected_team_id='+(this.value==='0'?'':this.value)+';path=/;max-age=31536000;SameSite=Lax';location.reload();" class="w-full px-3 py-2 border border-solid border-[var(--border-color)] rounded-lg text-[0.85rem] leading-normal bg-input-bg text-input-text transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none cursor-pointer">
            <option value="0" <?= $selectedTeamId === null ? 'selected' : '' ?>><?= __('team_selector_personal') ?></option>
            <?php foreach ($userTeamsList as $ut) { ?>
              <option value="<?= $ut['team_id'] ?>" <?= (int)$selectedTeamId === (int)$ut['team_id'] ? 'selected' : '' ?>><?= htmlspecialchars($ut['name']) ?></option>
            <?php } ?>
          </select>
        </div>
    <?php } ?>

    <?php foreach ($navbarItems as $navbarItem) {

      if (!$navbarItem["showOnlyLogged"] || isset($user)) { ?>
        <a href="<?= $navbarItem["url"] ?>"
        class="navbar-item <?php if ($navbarItem["url"] === $pageURI) { echo "active-link"; } ?>">
          <i class="fas fa-<?= $navbarItem["icon"] ?> fa-fw"></i>
          <span class="navbar-item-label"><?= isset($navbarItem["allowHtml"]) && $navbarItem["allowHtml"] ? $navbarItem["label"] : htmlspecialchars($navbarItem["label"]) ?></span>
        </a>
      <?php } ?>

    <?php } ?>
  </div>

  <div class="UserBox">
  
  <?php if (isset($user)) { ?>      
    <div class="user-info">
      <span class="username"><?= htmlspecialchars($user["username"]) ?></span>
    </div>
    <a href="logout.php" class="navbar-logout-icon" title="<?= __('nav_logout') ?>"><i class="fas fa-sign-out-alt"></i></a>
  <?php } else { ?>
    <a href="login.php" class="navbar-login-btn"><?= __('nav_login') ?></a>
  <?php } ?>

  </div>
</nav>
