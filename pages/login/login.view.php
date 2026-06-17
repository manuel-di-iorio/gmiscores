<div class="login-container">
  <div class="login-box">
    <img src="assets/images/logoSmall.png" alt="Logo" class="login-logo">
    <h4><?= __('login_title') ?></h4>

    <p style="font-size:0.85em;color:var(--text-muted,#666);text-align:center;margin:8px 0 16px;line-height:1.5">
      <?= __('login_disclaimer') ?>
      <a href="terms.php" target="_blank"><?= __('login_terms') ?></a>,
      <a href="privacy.php" target="_blank"><?= __('login_privacy') ?></a> e
      <a href="cookie.php" target="_blank"><?= __('login_cookie') ?></a>.
    </p>

    <?= ui_button(__('login_button'), 'primary', 'lg', ['icon' => 'fab fa-discord', 'href' => $loginRedirectUrl]) ?>
  </div>
</div>
