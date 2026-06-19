<div class="internal-page">
  <div class="internal-card" style="max-width:500px">
    <div class="internal-card__title"><i class="fas fa-users"></i> <?= __('teams_create_title') ?></div>
    <form method="POST" action="/add-team.php">
      <?= csrf_field() ?>
      <label style="display:block;font-weight:600;margin-bottom:8px;color:var(--text-color-headings,#444)"><?= __('teams_create_name') ?></label>
      <div class="input-group">
        <input name="name" type="text" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed" placeholder="<?= __('teams_create_name') ?>" required>
      </div>
      <div style="margin-top:16px">
        <?= ui_button(__('teams_create_submit'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'type' => 'submit']) ?>
      </div>
    </form>
  </div>
</div>
