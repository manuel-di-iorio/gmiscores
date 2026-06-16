<div class="internal-page">
    <div class="internal-actions internal-actions--right">
        <?= ui_button('Crea classifica', 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'href' => 'add-leaderboard.php?game_id=' . $game['game_id']]) ?>
    </div>

    <?php
    $lbFilters = [
        [ 'name' => 'name', 'label' => 'Nome classifica', 'type' => 'text', 'placeholder' => 'Cerca per nome...' ],
        // [ 'name' => 'score_min', 'label' => 'Punteggi min', 'type' => 'number', 'placeholder' => 'Min' ],
        // [ 'name' => 'score_max', 'label' => 'Punteggi max', 'type' => 'number', 'placeholder' => 'Max' ],
    ];
    render_table_filters($lbFilters, ['reset_preserve' => ['game_id', 'sort', 'dir']]);

    if (!empty($leaderboards)) {
        $tableColumns = [
            [
                "label" => "ID",
                "key" => "leaderboard_id",
                "sortable" => true
            ],
            [
                "label" => "Classifica",
                "key" => "name",
                "sortable" => true,
                "format_callback" => function ($value, $row) {
                    $icon = !empty($row['is_private']) ? ' <i class="fas fa-lock text-gray" title="Classifica privata"></i>' : '';
                    return htmlspecialchars($value) . $icon;
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
                "label" => "Punteggi",
                "key" => "score_count",
                "sortable" => true
            ],
            [
                "label" => "Creata il",
                "key" => "created_at",
                "sortable" => true,
                "format_callback" => function ($value, $row) {
                    return $row["_created_at_pretty"] ?? $value;
                }
            ]
        ];

        $canDelete = count($leaderboards) > 1;
        $tableActions = [
            [
                "label" => "Visualizza Punteggi",
                "icon" => "fas fa-list-ol",
                "url" => function($row) use ($game) {
                    return "game-scores.php?id=" . $game['game_id'] . "&leaderboard_id=" . $row['leaderboard_id'];
                },
                "class" => "btn-link"
            ],
            [
                "label" => "Modifica",
                "icon" => "fas fa-edit",
                 "url" => function($row) {
                    return "edit-leaderboard.php?leaderboard_id=" . $row['leaderboard_id'];
                },
                "class" => "btn-link"
            ]
        ];

        if ($canDelete) {
            $tableActions[] = [
                "label" => "Cancella",
                "icon" => "fas fa-trash",
                "url" => "javascript:;",
                "class" => "btn-link",
                "onclick" => function ($row) use ($game) {
                    $leaderboard_id = $row['leaderboard_id'] ?? 'null';
                    $leaderboard_name = escapeChars($row['name'] ?? '');
                    return "openModal('modal-delete-leaderboard', onDeleteLeaderboardModalOpen, { leaderboardId: " . $leaderboard_id . ", leaderboardName: '" . $leaderboard_name . "' })";
                }
            ];
        }

        $tableOptions = [
            "table_id" => "leaderboardsTable",
            "table_class" => "ui-table",
            "primary_key" => "leaderboard_id",
            "base_url" => "leaderboards.php?game_id=" . $game["game_id"],
            "default_sort_column" => "name",
            "default_sort_direction" => "asc",
        ];

        render_table($leaderboards, $tableColumns, $tableActions, $tableOptions);
    } else { ?>
        <div class="internal-empty">
            <i class="fas fa-trophy"></i>
            <h4>Non ci sono ancora classifiche per questo gioco</h4>
            <p>Crea la prima classifica per iniziare a raccogliere punteggi.</p>
            <?= ui_button('Crea classifica', 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'href' => 'add-leaderboard.php?game_id=' . $game['game_id']]) ?>
        </div>
    <?php } ?>
</div>

<?= ui_modal('modal-delete-leaderboard', [
  'title' => 'Conferma eliminazione',
  'content' => '<p>Sei sicuro di voler cancellare la leaderboard <strong id="modal-delete-leaderboard__name"></strong>?</p><p><i class="fas fa-exclamation-triangle"></i> Attenzione: tutti i punteggi associati a questa leaderboard verranno cancellati definitivamente.</p>',
  'footer' =>
    ui_button('Annulla', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-delete-leaderboard')"]]) .
    ui_button('Cancella classifica', 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'deleteLeaderboard()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<script>
let deleteLeaderboardUrl = '';

function onDeleteLeaderboardModalOpen(params) {
    document.getElementById('modal-delete-leaderboard__name').textContent = params.leaderboardName;
    deleteLeaderboardUrl = 'delete-leaderboard.php?leaderboard_id=' + params.leaderboardId + '&game_id=<?= $game['game_id'] ?>';
}

function deleteLeaderboard() {
    if (deleteLeaderboardUrl) {
        window.location.href = deleteLeaderboardUrl;
    }
}
</script>
