<div class="internal-page">
    <form method="POST" class="internal-card internal-card--form">
        <?php if (isset($error)) { ?>
            <div class="w3-panel w3-red"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <div class="internal-card__title"><i class="fas fa-trophy"></i> Nuova classifica</div>

        <div class="w3-section">
            <label style="font-weight:600;display:block;margin-bottom:6px;font-size:0.95em"><b>Nome classifica</b></label>
            <input type="text" name="name" class="w3-input internal-input" required
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>

        <div class="w3-section">
            <label style="font-weight:600;display:block;margin-bottom:6px;font-size:0.95em"><b>Descrizione (opzionale)</b></label>
            <textarea name="description" class="w3-input internal-input"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="w3-section">
            <label style="display:flex;align-items:flex-start;gap:8px;cursor:pointer">
                <input type="checkbox" name="is_private" value="1" class="w3-check" style="margin-top:3px"
                    <?= (!isset($_POST['is_private']) || $_POST['is_private'] === '1') ? 'checked' : '' ?>>
                <div>
                    <b>Classifica protetta</b>
                    <small style="display:block;font-weight:400;color:var(--text-muted,#666)"><i class="fas fa-lock"></i> Richiede hash per API</small>
                </div>
            </label>
        </div>

        <div style="display:flex;gap:10px;margin-top:20px">
            <button type="submit" class="w3-button w3-black">
                <i class="fas fa-plus-circle w3-margin-right"></i>Crea classifica
            </button>
            <a href="leaderboards.php?game_id=<?= $game_id ?>" class="w3-button w3-light-grey">Annulla</a>
        </div>
    </form>
</div>
