<div class="flex items-center justify-center min-h-[calc(100vh-160px)] bg-surface-offset">
  <div class="bg-surface-card shadow-card-prominent rounded-xl p-10 text-center max-w-md w-full">
    <img src="assets/images/logo.svg" alt="Logo" class="max-w-[80px] mb-6 mx-auto">
    <h4 class="mb-6 text-xl font-semibold text-text-headings"><?= __('login_title') ?></h4>

    <p class="text-xs text-text-secondary text-center mb-4 leading-relaxed" style="font-size:0.85em;margin:8px 0 16px">
      <?= __('login_disclaimer') ?>
      <a href="terms.php" target="_blank" class="text-primary-500 underline font-medium"><?= __('login_terms') ?></a>,
      <a href="privacy.php" target="_blank" class="text-primary-500 underline font-medium"><?= __('login_privacy') ?></a> e
      <a href="cookie.php" target="_blank" class="text-primary-500 underline font-medium"><?= __('login_cookie') ?></a>.
    </p>

    <?= ui_button(__('login_button'), 'primary', 'lg', ['icon' => 'fab fa-discord', 'href' => $loginRedirectUrl]) ?>
  </div>
</div>
