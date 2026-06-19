<div class="internal-page">
    <form method="POST" class="internal-card internal-card--form">
        <?= csrf_field() ?>
        <?php if (isset($error)) { ?>
            <div style="background:#f44336;color:#fff;padding:8px 16px;border-radius:4px;margin-bottom:16px"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <div class="internal-card__title"><i class="fas fa-edit"></i> <?= __('edit_lb_title') ?></div>

        <div class="mb-4">
            <label class="block font-semibold mb-1.5 text-sm text-[var(--text-color)]" for="name"><?= __('edit_lb_name') ?></label>
            <input type="text" name="name" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed" required
                   value="<?= htmlspecialchars($_POST['name'] ?? $lb['name']) ?>">
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1.5 text-sm text-[var(--text-color)]" for="description"><?= __('edit_lb_description') ?></label>
            <textarea name="description" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed min-h-[80px] resize-y"><?= htmlspecialchars($_POST['description'] ?? $lb['description'] ?? '') ?></textarea>
        </div>

        <?= ui_checkbox('is_private', isset($_POST['is_private']) ? $_POST['is_private'] === '1' : ($lb['is_private'] ?? true), [
            'label' => __("edit_lb_protected"),
            'description' => __("edit_lb_protected_hint"),
            'icon' => 'fas fa-lock'
        ]) ?>

        <div style="display:flex;gap:10px;margin-top:20px">
            <?= ui_button(__('edit_lb_submit'), 'primary', 'md', ['icon' => 'fas fa-save', 'type' => 'submit']) ?>
            <?= ui_button(__('edit_lb_cancel'), 'secondary', 'md', ['href' => 'leaderboards.php?game_id=' . $lb['game_id']]) ?>
        </div>
    </form>
</div>
