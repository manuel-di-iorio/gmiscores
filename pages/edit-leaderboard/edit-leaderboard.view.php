<div class="internal-page">
    <form method="POST" class="internal-card internal-card--form">
        <?php if (isset($error)) { ?>
            <div style="background:#f44336;color:#fff;padding:8px 16px;border-radius:4px;margin-bottom:16px"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <div class="internal-card__title"><i class="fas fa-edit"></i> <?= __('edit_lb_title') ?></div>

        <div class="ui-input-group">
            <label class="ui-label" for="name"><?= __('edit_lb_name') ?></label>
            <input type="text" name="name" class="ui-input" required
                   value="<?= htmlspecialchars($_POST['name'] ?? $lb['name']) ?>">
        </div>

        <div class="ui-input-group">
            <label class="ui-label" for="description"><?= __('edit_lb_description') ?></label>
            <textarea name="description" class="ui-input"><?= htmlspecialchars($_POST['description'] ?? $lb['description'] ?? '') ?></textarea>
        </div>

        <div class="ui-input-group">
            <label class="ui-checkbox" style="display:flex;align-items:flex-start;gap:8px;cursor:pointer">
                <input type="checkbox" name="is_private" value="1" style="margin-top:3px"
                    <?= (isset($_POST['is_private']) ? $_POST['is_private'] === '1' : ($lb['is_private'] ?? true)) ? 'checked' : '' ?>>
                <div>
                    <b><?= __('edit_lb_protected') ?></b>
                    <small style="display:block;font-weight:400;color:var(--text-muted,#666)"><i class="fas fa-lock"></i> <?= __('edit_lb_protected_hint') ?></small>
                </div>
            </label>
        </div>

        <div style="display:flex;gap:10px;margin-top:20px">
            <?= ui_button(__('edit_lb_submit'), 'primary', 'md', ['icon' => 'fas fa-save', 'type' => 'submit']) ?>
            <?= ui_button(__('edit_lb_cancel'), 'secondary', 'md', ['href' => 'leaderboards.php?game_id=' . $lb['game_id']]) ?>
        </div>
    </form>
</div>
