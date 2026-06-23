<style>
.sdk-login-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  height: 100vh;
  padding: 40px 20px;
  box-sizing: border-box;
  background: 
    radial-gradient(ellipse at 20% 50%, rgba(88, 101, 242, 0.08) 0%, transparent 50%),
    radial-gradient(ellipse at 80% 20%, rgba(129, 140, 248, 0.06) 0%, transparent 50%),
    radial-gradient(ellipse at 50% 80%, rgba(99, 102, 241, 0.05) 0%, transparent 50%),
    var(--bg-color, #f7f7f7);
  position: relative;
  overflow: hidden;
}
.sdk-login-page::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: repeating-conic-gradient(
    from 0deg at 50% 50%,
    transparent 0deg 88deg,
    rgba(88, 101, 242, 0.015) 88deg 90deg
  );
  animation: sdk-rotate-slow 120s linear infinite;
  pointer-events: none;
}
@keyframes sdk-rotate-slow {
  to { transform: rotate(360deg); }
}
.sdk-login-card {
  background: var(--bg-color-card, #fff);
  border: 1px solid var(--border-color, #e5e7eb);
  border-radius: 20px;
  padding: 52px 44px 48px;
  text-align: center;
  max-width: 550px;
  width: 100%;
  box-sizing: border-box;
  box-shadow:
    0 1px 3px rgba(0,0,0,0.04),
    0 8px 32px rgba(0,0,0,0.06);
  position: relative;
  z-index: 1;
  animation: sdk-card-in 0.5s ease-out;
}
@keyframes sdk-card-in {
  from { opacity: 0; transform: translateY(24px) scale(0.97); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}
.sdk-login-logo {
  width: 72px;
  height: 72px;
  margin: 0 auto 20px;
  background: linear-gradient(135deg, #5865F2 0%, #818cf8 100%);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 16px rgba(88, 101, 242, 0.3);
}
.sdk-login-logo i {
  font-size: 2em;
  color: #fff;
}
.sdk-login-card h2 {
  font-size: 1.45rem;
  font-weight: 700;
  margin-bottom: 8px;
  color: var(--text-color-headings, #333);
  letter-spacing: -0.01em;
}
.sdk-login-card .sdk-login-subtitle {
  color: var(--text-color-secondary, #666);
  margin-bottom: 32px;
  font-size: 0.92em;
  line-height: 1.6;
}
.sdk-discord-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 15px 36px;
  background: #5865F2;
  color: #fff;
  border: none;
  border-radius: 12px;
  font-size: 1em;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.25s ease;
  text-decoration: none;
  width: 100%;
  box-sizing: border-box;
  box-shadow: 0 2px 8px rgba(88, 101, 242, 0.25);
  position: relative;
  overflow: hidden;
}
.sdk-discord-btn::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 50%);
  pointer-events: none;
}
.sdk-discord-btn:hover {
  background: #4752C4;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(88, 101, 242, 0.35);
}
.sdk-discord-btn:active {
  transform: translateY(0);
  box-shadow: 0 2px 8px rgba(88, 101, 242, 0.25);
}
.sdk-login-waiting {
  display: none;
  margin-top: 28px;
  padding: 20px;
  background: var(--bg-color-offset, #f8f9fa);
  border-radius: 12px;
  border: 1px solid var(--border-color, #e5e7eb);
}
.sdk-login-waiting.active {
  display: block;
  animation: sdk-fade-in 0.3s ease-out;
}
.sdk-login-success {
  display: none;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  margin-top: 28px;
  padding: 24px 20px;
  background: rgba(16, 185, 129, 0.06);
  border-radius: 12px;
  border: 1px solid rgba(16, 185, 129, 0.15);
}
.sdk-login-success.active {
  display: flex;
  animation: sdk-fade-in 0.3s ease-out;
}
.sdk-login-success .sdk-success-icon {
  font-size: 2.4em;
  color: #10b981;
  animation: sdk-pop-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
@keyframes sdk-pop-in {
  from { transform: scale(0); }
  to   { transform: scale(1); }
}
.sdk-login-success p {
  margin: 0;
  color: var(--text-color-secondary, #666);
  font-size: 0.92em;
}
@keyframes sdk-fade-in {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
.sdk-login-footer {
  margin-top: 24px;
  font-size: 0.78em;
  color: var(--text-color-secondary, #999);
}
.sdk-login-footer a {
  color: #5865F2;
  text-decoration: none;
}
.sdk-login-footer a:hover {
  text-decoration: underline;
}
.sdk-login-brand {
  position: fixed;
  top: 28px;
  left: 32px;
  z-index: 10;
}
.sdk-login-brand img {
  height: 40px;
  opacity: 0.85;
  transition: opacity 0.2s;
}
.sdk-login-brand img:hover {
  opacity: 1;
}
</style>

<div class="sdk-login-brand">
  <img src="/assets/images/logo<?= $theme === 'dark' ? 'White' : '' ?>.svg" alt="GMI Scores">
</div>

<div class="sdk-login-page">
  <div class="sdk-login-card">
    <div class="sdk-login-logo">
      <i class="fab fa-discord"></i>
    </div>
    <h2><?= __('player_sdk_login_title') ?></h2>
    <p class="sdk-login-subtitle"><?= __('player_sdk_login_desc') ?></p>

    <a href="<?= $loginRedirectUrl ?>" class="sdk-discord-btn" id="btn-discord-login">
      <i class="fab fa-discord"></i> <?= __('player_sdk_login_button') ?>
    </a>

    <div class="sdk-login-waiting" id="login-waiting">
      <?= ui_spinner_block(__('player_sdk_login_waiting'), 'xl') ?>
    </div>

    <div class="sdk-login-success" id="login-success">
      <i class="fas fa-check-circle sdk-success-icon"></i>
      <p><?= __('player_sdk_login_success') ?></p>
    </div>

    <div class="sdk-login-footer">
      <?= __('player_sdk_login_footer', [
        'terms' => '<a href="/terms.php" target="_blank">' . __('footer_terms') . '</a>',
        'privacy' => '<a href="/privacy.php" target="_blank">' . __('footer_privacy') . '</a>',
        'cookie' => '<a href="/cookie.php" target="_blank">' . __('footer_cookie') . '</a>'
      ]) ?>
    </div>
  </div>
</div>

<script>
<?php if (isset($_GET["done"]) && $_GET["done"] === "1") { ?>
document.addEventListener("DOMContentLoaded", function() {
  document.getElementById("btn-discord-login").style.display = "none";
  document.getElementById("login-success").classList.add("active");
});
<?php } else { ?>
var sessionToken = "<?= htmlspecialchars($session) ?>";
var checkInterval = null;

document.getElementById("btn-discord-login").addEventListener("click", function() {
  document.getElementById("login-waiting").classList.add("active");
  document.getElementById("btn-discord-login").style.display = "none";

  checkInterval = setInterval(function() {
    fetch("/api/v1/player-login-session.php?session=" + sessionToken)
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.logged) {
          clearInterval(checkInterval);
          document.getElementById("login-waiting").classList.remove("active");
          document.getElementById("login-success").classList.add("active");
        }
      });
  }, 2000);
});
<?php } ?>
</script>
