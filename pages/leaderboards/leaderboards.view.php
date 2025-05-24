<div class="w3-container w3-padding-large">
    <div class="w3-cell-row w3-margin-bottom">
        <div class="w3-cell w3-right-align">
            <a href="add-leaderboard.php?game_id=<?= $game['game_id'] ?>" class="w3-button w3-black">
                <i class="fas fa-plus-circle w3-margin-right"></i>Crea Leaderboard
            </a>
        </div>
    </div>

    <?php
    if (!empty($leaderboards)) {
        $tableColumns = [
            [
                "label" => "Nome Leaderboard",
                "key" => "name",
                "sortable" => true,
                "format_callback" => function ($value, $row) {
                    return htmlspecialchars($value);
                }
            ],
            [
                "label" => "Descrizione",
                "key" => "description",
                "sortable" => false,
                 "format_callback" => function ($value, $row) {
                    return htmlspecialchars($value ?? 'N/A');
                }
            ],
            [
                "label" => "Creata il",
                "key" => "created_at",
                "sortable" => true
            ]
        ];

        $tableActions = [
            [
                "label" => "Visualizza Punteggi",
                "icon" => "fas fa-list-ol",
                "url" => function($row) use ($game) {
                    return "game-scores.php?game_id=" . $game['game_id'] . "&leaderboard_id=" . $row['leaderboard_id'];
                },
                "class" => "btn-link w3-margin-right"
            ],
            [
                "label" => "Modifica",
                "icon" => "fas fa-edit",
                 "url" => function($row) {
                    return "edit-leaderboard.php?leaderboard_id=" . $row['leaderboard_id'];
                },
                "class" => "btn-link w3-margin-right"
            ],
            [
                "label" => "Cancella",
                "icon" => "fas fa-trash",
                "url" => "javascript:;", // Placeholder, will need a modal confirmation
                "class" => "btn-link",
                "onclick" => function ($row) {
                    $leaderboard_id = $row['leaderboard_id'] ?? 'null';
                    $leaderboard_name = escapeChars($row['name'] ?? '');
                    // TODO: Implementare openModal per la cancellazione della leaderboard
                    return "openModal(\\'modal-delete-leaderboard\\', onDeleteLeaderboardModalOpen, { leaderboardId: " . $leaderboard_id . ", leaderboardName: \\'" . $leaderboard_name . "\\' })";
                }
            ]
        ];

        $tableOptions = [
            "table_id" => "leaderboardsTable",
            "primary_key" => "leaderboard_id",
            "base_url" => "leaderboards.php?game_id=" . $game["game_id"],
            "default_sort_column" => "name",
            "default_sort_direction" => "asc",
            // Pagination can be added later if needed
        ];

        render_table($leaderboards, $tableColumns, $tableActions, $tableOptions);
    } else {
        echo "<h4>Non ci sono ancora leaderboards per questo gioco.</h4>";
    }
    ?>
</div>

<!-- TODO: Aggiungere modale per la cancellazione della leaderboard -->
<!-- 
<div id="modal-delete-leaderboard" class="w3-modal">
    <div class="w3-modal-content w3-animate-top">
        <div class="w3-container ModalContent">
            <h4>Sei sicuro di voler cancellare la leaderboard <strong id="modal-delete-leaderboard__name"></strong>?</h4>
            <p class="w3-text-red"><i class="fas fa-exclamation-triangle"></i> Attenzione: i punteggi associati a questa leaderboard NON verranno cancellati e potrebbero diventare orfani se non riassegnati.</p>
        </div>
        <footer class="w3-container w3-light-grey w3-padding-16 w3-right-align">
            <a href="javascript:;" onclick="deleteLeaderboard()" class="btn-link ModalFooterLink w3-text-red">
                <i class="fas fa-trash"></i> Cancella Leaderboard
            </a>
            <button onclick="closeModal(\'modal-delete-leaderboard\')" type="button" class="w3-button w3-black">Annulla</button>
        </footer>
    </div>
</div>
<script>
// function onDeleteLeaderboardModalOpen(params) {
// document.getElementById('modal-delete-leaderboard__name').textContent = params.leaderboardName;
// window.deleteLeaderboardAction = 'delete-leaderboard.php?leaderboard_id=' + params.leaderboardId + '&game_id=<?= $game['game_id'] ?>';
// }
// function deleteLeaderboard() {
// if(window.deleteLeaderboardAction) window.location.href = window.deleteLeaderboardAction;
// }
</script>
-->
