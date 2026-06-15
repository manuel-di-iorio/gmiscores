<?php
/**
 * Renders a reusable table filters form.
 *
 * Usage:
 *  $fields = [
 *    [ 'name' => 'player', 'label' => 'Giocatore', 'type' => 'text', 'placeholder' => 'Nome giocatore' ],
 *    [ 'name' => 'date_from', 'label' => 'Da', 'type' => 'date' ],
 *  ];
 *  render_table_filters($fields);
 *
 * Supported field types: text, number, date, select
 */
function render_table_filters(array $fields, array $options = []) {
    static $cssPrinted = false;
    if (!$cssPrinted) {
        echo '<style>
.table-filters { background: #fff; border: 1px solid #e9e9e9; padding: 12px; border-radius: 8px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); }
.table-filters .w3-row { margin: 0 -8px; }
.table-filters .w3-col { padding: 0 8px 12px 8px; box-sizing: border-box; }
.table-filters label { font-weight: 600; color: #333; font-size: 0.95rem; display: block; margin-bottom: 6px; }
.table-filters .w3-input, .table-filters .w3-select { padding: 10px 12px; border-radius: 4px; border: 1px solid #e0e0e0; background: #fff; height: 40px; box-sizing: border-box; }
.table-filters .w3-input::placeholder { color: #bbb; }
.table-filters .w3-button.w3-black { border-radius: 20px; padding: 8px 18px; box-shadow: none; }
.table-filters .filters-actions { display:flex; align-items:flex-end; gap: 10px; }
.table-filters .filters-actions .btn-link { margin-left: auto; color: #333; text-decoration: none; align-self: center; }
.table-filters .btn-link { color: #333; text-decoration: none; align-self: center; }
@media (max-width: 600px) { .table-filters .filters-actions { flex-direction: row; } .table-filters .w3-col { padding-bottom: 6px; } }
</style>';
        $cssPrinted = true;
    }
    // Names of the filter fields to exclude from preserved hidden inputs
    $filterNames = array_map(function($f){ return $f['name']; }, $fields);

    // Exclude page so filters reset to first page
    $exclude = array_merge($filterNames, ['page']);

    // Which keys to preserve on reset (only keep id, sort, dir by default)
    $resetPreserve = $options['reset_preserve'] ?? ['id', 'sort', 'dir'];

    echo '<form method="GET" class="table-filters">';
    echo '<div class="w3-row">';

    // Preserve existing GET parameters (hidden inputs) except filter fields and page
    foreach ($_GET as $k => $v) {
        if (in_array($k, $exclude, true)) continue;
        if (is_array($v)) {
            foreach ($v as $sub) {
                echo '<input type="hidden" name="' . htmlspecialchars($k) . '[]" value="' . htmlspecialchars($sub) . '">';
            }
            continue;
        }
        echo '<input type="hidden" name="' . htmlspecialchars($k) . '" value="' . htmlspecialchars($v) . '">';
    }

    // Render fields
    foreach ($fields as $field) {
        $name = $field['name'];
        $label = $field['label'] ?? ucfirst($name);
        $type = $field['type'] ?? 'text';
        $placeholder = $field['placeholder'] ?? '';
        $value = isset($_GET[$name]) ? $_GET[$name] : ($field['default'] ?? '');

        echo '<div class="w3-col s12 m6 l3">';
        echo '<label>' . htmlspecialchars($label) . '</label>';

        if ($type === 'select' && isset($field['options']) && is_array($field['options'])) {
            echo '<select name="' . htmlspecialchars($name) . '" class="w3-select w3-border">';
            echo '<option value="">(Tutti)</option>';
            foreach ($field['options'] as $optValue => $optLabel) {
                $sel = ((string)$optValue === (string)$value) ? ' selected' : '';
                echo '<option value="' . htmlspecialchars($optValue) . '"' . $sel . '>' . htmlspecialchars($optLabel) . '</option>';
            }
            echo '</select>';
        } else {
            // Map to HTML input types
            $htmlType = 'text';
            if ($type === 'number') $htmlType = 'number';
            if ($type === 'date') $htmlType = 'date';

            echo '<input type="' . $htmlType . '" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" placeholder="' . htmlspecialchars($placeholder) . '" class="w3-input w3-border">';
        }

        echo '</div>';
    }

    // Action buttons
        echo '<div class="w3-col s12 m6 l3">';
    echo '<label>&nbsp;</label>';
    echo '<div class="filters-actions">';
    echo '<button type="submit" class="w3-button w3-black">Applica filtri</button> ';

    // Build reset URL preserving only selected keys
    $resetParams = [];
    foreach ($resetPreserve as $k) {
        if (isset($_GET[$k]) && $_GET[$k] !== '') {
            $resetParams[$k] = $_GET[$k];
        }
    }
    $resetUrl = $_SERVER['PHP_SELF'] . (count($resetParams) ? ('?' . http_build_query($resetParams)) : '');
    echo '<a href="' . htmlspecialchars($resetUrl) . '" class="btn-link w3-margin-left">Azzera</a>';

    echo '</div>'; // close filters-actions
    echo '</div>'; // close action col
    echo '</div>'; // close w3-row
    echo '</form>';
}

?>
