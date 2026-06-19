<div class="internal-page">
    <div class="internal-actions internal-actions--right">
        <?= ui_button(__('leaderboards_create_btn'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'href' => 'add-leaderboard.php?game_id=' . $game['game_id']]) ?>
    </div>

    <?php
    $lbFilters = [
        [ 'name' => 'name', 'label' => __('leaderboards_filter_name'), 'type' => 'text', 'placeholder' => __('leaderboards_filter_placeholder') ],
        // [ 'name' => 'score_min', 'label' => 'Punteggi min', 'type' => 'number', 'placeholder' => 'Min' ],
        // [ 'name' => 'score_max', 'label' => 'Punteggi max', 'type' => 'number', 'placeholder' => 'Max' ],
    ];
    render_table_filters($lbFilters, ['reset_preserve' => ['game_id', 'sort', 'dir']]);

    if (!empty($leaderboards)) {
        $tableColumns = [
            [
                "label" => __('leaderboards_col_id'),
                "key" => "leaderboard_id",
                "sortable" => true,
                "format_callback" => function ($value, $row) use ($game) {
                    return '<a href="game-scores.php?id=' . $game['game_id'] . '&leaderboard_id=' . $row['leaderboard_id'] . '" class="link" data-tippy-content="' . __('leaderboards_row_tooltip') . '">' . htmlspecialchars($value) . '</a>';
                }
            ],
            [
                "label" => __('leaderboards_col_name'),
                "key" => "name",
                "sortable" => true,
                "format_callback" => function ($value, $row) {
                    $icon = !empty($row['is_private']) ? ' <i class="fas fa-lock text-gray" title="' . __('leaderboards_col_private') . '"></i>' : '';
                    return htmlspecialchars($value) . $icon;
                }
            ],
            [
                "label" => __('leaderboards_col_description'),
                "key" => "description",
                "sortable" => false,
                 "format_callback" => function ($value, $row) {
                    return htmlspecialchars($value ?? __('leaderboards_col_description_na'));
                }
            ],
            [
                "label" => __('leaderboards_col_scores'),
                "key" => "score_count",
                "sortable" => true
            ],
            [
                "label" => __('leaderboards_col_created'),
                "key" => "created_at",
                "sortable" => true,
                "format_callback" => function ($value, $row) {
                    return $row["_created_at_pretty"] ?? $value;
                }
            ]
        ];

        $tableActions = [
            [
                "label" => __('leaderboards_action_view'),
                "icon" => "fas fa-list-ol",
                "url" => function($row) use ($game) {
                    return "game-scores.php?id=" . $game['game_id'] . "&leaderboard_id=" . $row['leaderboard_id'];
                },
                "class" => "btn-link"
            ],
            [
                "label" => __('leaderboards_action_edit'),
                "icon" => "fas fa-edit",
                 "url" => function($row) {
                    return "edit-leaderboard.php?leaderboard_id=" . $row['leaderboard_id'];
                },
                "class" => "btn-link"
            ]
        ];

        $tableActions[] = [
            "label" => __('leaderboards_action_delete'),
            "icon" => "fas fa-trash",
            "url" => "javascript:;",
            "class" => "btn-link",
            "onclick" => function ($row) use ($game) {
                $leaderboard_id = $row['leaderboard_id'] ?? 'null';
                $leaderboard_name = escapeChars($row['name'] ?? '');
                return "openModal('modal-delete-leaderboard', onDeleteLeaderboardModalOpen, { leaderboardId: " . $leaderboard_id . ", leaderboardName: '" . $leaderboard_name . "' })";
            }
        ];

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
            <h4><?= __('leaderboards_empty_title') ?></h4>
            <p><?= __('leaderboards_empty_desc') ?></p>
            <?= ui_button(__('leaderboards_empty_btn'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'href' => 'add-leaderboard.php?game_id=' . $game['game_id']]) ?>
        </div>
    <?php } ?>
</div>

<?= ui_modal('modal-delete-leaderboard', [
  'title' => __('leaderboards_modal_delete_title'),
  'content' => '<p>' . __('leaderboards_modal_delete_body') . ' <strong id="modal-delete-leaderboard__name"></strong>?</p><p>' . __('leaderboards_modal_delete_warning') . '</p>',
  'footer' =>
    ui_button(__('leaderboards_modal_delete_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-delete-leaderboard')"]]) .
    ui_button(__('leaderboards_modal_delete_confirm'), 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'deleteLeaderboard()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<script>
var csrfToken = '<?= csrf_token() ?>';
let deleteLeaderboardData = {};

function onDeleteLeaderboardModalOpen(params) {
    document.getElementById('modal-delete-leaderboard__name').textContent = params.leaderboardName;
    deleteLeaderboardData = { leaderboard_id: params.leaderboardId, game_id: '<?= $game['game_id'] ?>' };
}

function deleteLeaderboard() {
    if (deleteLeaderboardData.leaderboard_id) {
        var body = 'leaderboard_id=' + encodeURIComponent(deleteLeaderboardData.leaderboard_id)
            + '&game_id=' + encodeURIComponent(deleteLeaderboardData.game_id)
            + '&csrf_token=' + encodeURIComponent(csrfToken);
        fetch('delete-leaderboard.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: body
        }).then(function() { location.reload(); });
    }
}
</script>
