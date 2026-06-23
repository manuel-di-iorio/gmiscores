<style>
.login-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: calc(100vh - 212px);
  /* margin: 0 -80px; */
  padding-top: 64px 20px 40px;
  background:
    radial-gradient(ellipse at 20% 50%, rgba(88, 101, 242, 0.06) 0%, transparent 50%),
    radial-gradient(ellipse at 80% 20%, rgba(129, 140, 248, 0.04) 0%, transparent 50%),
    var(--bg-color-offset, #f4f7f6);
}

.login-card {
  background: var(--bg-color-card, #fff);
  border: 1px solid var(--border-color, #e5e7eb);
  border-radius: 20px;
  padding: 52px 48px 44px;
  text-align: center;
  max-width: 500px;
  width: 100%;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 8px 32px rgba(0,0,0,0.06);
}
.login-discord-icon {
  width: 72px;
  height: 72px;
  margin: 0 auto 24px;
  background: linear-gradient(135deg, #5865F2 0%, #818cf8 100%);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 16px rgba(88, 101, 242, 0.3);
}
.login-discord-icon i { font-size: 2em; color: #fff; }
.login-card h2 {
  font-size: 1.45rem;
  font-weight: 700;
  margin-bottom: 28px;
  color: var(--text-color-headings, #333);
}
.login-terms {
  margin-top: 24px;
  font-size: 0.78em;
  color: var(--text-color-secondary, #999);
  line-height: 1.6;
}
.login-terms a { color: #5865F2; text-decoration: none; }
.login-terms a:hover { text-decoration: underline; }
</style>

<div class="login-page">
  <div class="login-card">
    <div class="login-discord-icon">
      <i class="fab fa-discord"></i>
    </div>
    <h2><?= __('login_title') ?></h2>

    <?= ui_button(__('login_button'), 'primary', 'lg', [
      'icon' => 'fab fa-discord',
      'href' => $loginRedirectUrl,
      'full' => true,
      'class' => 'login-discord-btn'
    ]) ?>

    <div class="login-terms">
      <?= __('login_disclaimer') ?>
      <a href="terms.php" target="_blank"><?= __('login_terms') ?></a>,
      <a href="privacy.php" target="_blank"><?= __('login_privacy') ?></a> e
      <a href="cookie.php" target="_blank"><?= __('login_cookie') ?></a>.
    </div>
  </div>
</div>

<style>
.login-discord-btn {
  display: flex !important;
  background: #5865F2 !important;
  border: none !important;
  box-shadow: 0 2px 8px rgba(88, 101, 242, 0.25);
}
.login-discord-btn:hover {
  background: #4752C4 !important;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(88, 101, 242, 0.35);
}
</style>
