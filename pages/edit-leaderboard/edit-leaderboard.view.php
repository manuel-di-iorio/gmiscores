<div class="w3-container w3-padding-large">
    <form method="POST" class="w3-card-4 w3-padding">
        <?php if (isset($error)) { ?>
            <div class="w3-panel w3-red"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <div class="w3-section">
            <label><b>Nome classifica</b></label>
            <input type="text" name="name" class="w3-input w3-border w3-round" required
                   value="<?= htmlspecialchars($_POST['name'] ?? $lb['name']) ?>">
        </div>

        <div class="w3-section">
            <label><b>Descrizione (opzionale)</b></label>
            <textarea name="description" class="w3-input w3-border w3-round"><?= htmlspecialchars($_POST['description'] ?? $lb['description'] ?? '') ?></textarea>
        </div>

        <div class="w3-section">
            <label>
                <input type="checkbox" name="is_private" value="1" class="w3-check"
                    <?= ($lb['is_private'] ?? false) ? 'checked' : '' ?>>
                <b>Classifica privata</b>
                <small><i class="fas fa-lock w3-margin-left"></i> Richiede autenticazione per essere letta via API</small>
            </label>
        </div>

        <button type="submit" class="w3-button w3-black">
            <i class="fas fa-save w3-margin-right"></i>Salva
        </button>
        <a href="leaderboards.php?game_id=<?= $lb['game_id'] ?>" class="w3-button w3-light-grey">Annulla</a>
    </form>
</div>