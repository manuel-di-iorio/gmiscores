<div class="internal-page">
  <div class="internal-card" style="max-width:500px">
    <div class="internal-card__title"><i class="fas fa-exchange-alt"></i> <?= __('team_games_move_title') ?>: <?= htmlspecialchars($game['name']) ?></div>
    <form method="POST" action="/team-move-game.php?id=<?= $gameId ?>">
      <?= csrf_field() ?>
      <label style="display:block;font-weight:600;margin-bottom:8px;color:var(--text-color-headings,#444)"><?= __('team_games_move_to') ?></label>
      <div class="input-group">
        <select name="target_team_id" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)]">
          <option value="0"><?= __('team_games_move_personal') ?></option>
          <?php foreach ($userTeams as $ut) { ?>
            <option value="<?= $ut['team_id'] ?>"><?= htmlspecialchars($ut['name']) ?></option>
          <?php } ?>
        </select>
      </div>
      <div style="margin-top:16px">
        <?= ui_button(__('team_games_move_confirm'), 'primary', 'md', ['icon' => 'fas fa-exchange-alt', 'type' => 'submit']) ?>
      </div>
    </form>
  </div>
</div>
