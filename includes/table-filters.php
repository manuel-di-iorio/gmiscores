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
    $filterNames = array_map(function($f){ return $f['name']; }, $fields);
    $exclude = array_merge($filterNames, ['page']);
    $resetPreserve = $options['reset_preserve'] ?? ['id', 'sort', 'dir'];
    $formAction = $options['action'] ?? '';

    echo '<form method="GET"' . ($formAction ? ' action="' . htmlspecialchars($formAction) . '"' : '') . ' class="bg-surface-card border border-solid border-border-color rounded-lg p-3 shadow-sm mb-4">';
    echo '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">';

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

    foreach ($fields as $field) {
        $name = $field['name'];
        $label = $field['label'] ?? ucfirst($name);
        $type = $field['type'] ?? 'text';
        $placeholder = $field['placeholder'] ?? '';
        $value = isset($_GET[$name]) ? $_GET[$name] : ($field['default'] ?? '');

        echo '<div>';
        echo '<label class="font-semibold text-sm text-[var(--text-color)] block mb-1.5">' . htmlspecialchars($label) . '</label>';

        if ($type === 'select' && isset($field['options']) && is_array($field['options'])) {
            echo '<select name="' . htmlspecialchars($name) . '" class="w-full px-3.5 py-2 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] h-10">';
            echo '<option value="">' . __('filter_all') . '</option>';
            foreach ($field['options'] as $optValue => $optLabel) {
                $sel = ((string)$optValue === (string)$value) ? ' selected' : '';
                echo '<option value="' . htmlspecialchars($optValue) . '"' . $sel . '>' . htmlspecialchars($optLabel) . '</option>';
            }
            echo '</select>';
        } else {
            $htmlType = 'text';
            if ($type === 'number') $htmlType = 'number';
            if ($type === 'date') $htmlType = 'date';
            echo '<input type="' . $htmlType . '" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" placeholder="' . htmlspecialchars($placeholder) . '" class="w-full px-3.5 py-2 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed h-10">';
        }

        echo '</div>';
    }

    echo '<div class="flex items-end">';
    echo '<div class="flex items-end gap-5">';
    echo ui_button(__('filter_apply'), 'primary', 'md', ['type' => 'submit']);

    $hasFilter = false;
    foreach ($fields as $field) {
        $name = $field['name'];
        if (isset($_GET[$name]) && $_GET[$name] !== '' && $_GET[$name] !== null) {
            $hasFilter = true;
            break;
        }
    }

    if ($hasFilter) {
        $resetParams = [];
        foreach ($resetPreserve as $k) {
            if (isset($_GET[$k]) && $_GET[$k] !== '') {
                $resetParams[$k] = $_GET[$k];
            }
        }
        $resetUrl = ($formAction ?: $_SERVER['PHP_SELF']) . (count($resetParams) ? ('?' . http_build_query($resetParams)) : '');
        echo '<a href="' . htmlspecialchars($resetUrl) . '" class="text-sm text-[var(--text-color)] no-underline self-center transition-colors duration-150 hover:text-[var(--primary-color)]">' . __('filter_reset') . '</a>';
    }

    echo '</div>';
    echo '</div>';

    echo '</div>'; // close grid
    echo '</form>';
}

?>
