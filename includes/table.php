<?php

/**
 * Generates a generic HTML table with sorting, pagination, and actions.
 *
 * @param array $data An array of associative arrays representing the table rows.
 * @param array $columns An array of associative arrays defining the table columns.
 *                      Each column array should have:
 *                      - "label": The display name for the column header.
 *                      - "key": The key to access the data in each row.
 *                      - "sortable": (Optional) Boolean, true if the column is sortable. Defaults to false.
 *                      - "format_callback": (Optional) A callback function to format the cell value.
 *                                           It receives the value and the full row data as arguments.
 * @param array $actions An array of associative arrays defining actions for each row.
 *                       Each action array should have:
 *                       - "label": The tooltip or accessible label for the action.
 *                       - "icon": The Font Awesome icon class (e.g., "fas fa-edit").
 *                       - "url": The base URL for the action. Row ID will be appended as a query parameter `id`.
 *                                Or, can contain placeholders like {id} to be replaced with row's primary key.
 *                       - "class": (Optional) CSS class for the action button.
 *                       - "condition_callback": (Optional) A callback function that receives the row data
 *                                               and returns true if the action should be displayed for that row.
 * @param array $options An associative array for table options:
 *                       - "table_id": (Optional) The ID attribute for the table element.
 *                       - "table_class": (Optional) CSS classes for the table element. Defaults to "ui-table".
 *                       - "default_sort_column": (Optional) The key of the column to sort by default.
 *                       - "default_sort_direction": (Optional) "asc" or "desc". Defaults to "asc".
 *                       - "pagination": (Optional) An associative array for pagination settings:
 *                         - "current_page": The current page number.
 *                         - "items_per_page": Number of items to display per page.
 *                         - "total_items": The total number of items.
 *                       - "base_url": (Optional) The base URL for sorting and pagination links. Defaults to current page.
 *                       - "primary_key": (Optional) The key in the $data array that serves as the primary key for rows (e.g., "id"). Defaults to "id".
 *
 * @return void Outputs the HTML for the table directly.
 */
function render_table(array $data, array $columns, array $actions = [], array $options = []): void
{
    static $actionIconCssOutput = false;
    if (!$actionIconCssOutput) {
        $actionIconCssOutput = true;
        echo '<style>
    .actions-cell .btn-link {
        color: var(--table-action-icon-color, #555);
        font-size: 14px;
        margin-right: 5px;
        text-decoration: none;
        transition: color 0.15s, transform 0.15s;
        display: inline-block;
        padding: 4px 6px;
        border-radius: 4px;
    }
    .actions-cell .btn-link:hover {
        color: var(--primary-color, #6366f1);
        transform: scale(1.15);
        background: var(--table-action-icon-hover-bg, transparent);
    }
</style>';
    }

    // Default options
    $tableId = $options["table_id"] ?? 'genericTable' . rand(1000, 9999);
     $tableClass = $options["table_class"] ?? 'ui-table';
    $defaultSortColumn = $options["default_sort_column"] ?? null;
    $defaultSortDirection = $options["default_sort_direction"] ?? 'asc';
    $paginationSettings = $options["pagination"] ?? null;
    $baseUrl = $options["base_url"] ?? '';
    $primaryKey = $options["primary_key"] ?? 'id';
    $selectable = $options["selectable"] ?? false;

    // Pagination is always 0-indexed

    // Sorting parameters from GET request or defaults
    $sortColumn = $_GET['sort'] ?? $defaultSortColumn;
    $sortDirection = $_GET['dir'] ?? $defaultSortDirection;

    // --- Data Sorting (if a sort column is specified) ---
    if ($sortColumn !== null && !empty($data)) {
        usort($data, function ($a, $b) use ($sortColumn, $sortDirection) {
            $valA = $a[$sortColumn] ?? null;
            $valB = $b[$sortColumn] ?? null;

            if ($valA === $valB) {
                return 0; // Se i valori sono identici, non c'è bisogno di ordinare
            }

            // Gestione specifica per valori null: i null vanno alla fine per ascendente, all'inizio per discendente
            if ($valA === null) {
                return ($sortDirection === 'asc') ? 1 : -1;
            }
            if ($valB === null) {
                return ($sortDirection === 'asc') ? -1 : 1;
            }

            if (is_numeric($valA) && is_numeric($valB)) {
                // Confronto numerico
                return ($sortDirection === 'asc') ? ($valA - $valB) : ($valB - $valA);
            } else {
                // Confronto come stringhe (case-sensitive, standard per la maggior parte dei casi)
                $sValA = strval($valA);
                $sValB = strval($valB);
                return ($sortDirection === 'asc') ? strcmp($sValA, $sValB) : strcmp($sValB, $sValA);
            }
        });
    }

    // --- Pagination Logic ---
    $pageData = $data;
    $totalPages = 0; // Default for 0-indexed
    $currentPage = 0; // Default for 0-indexed

    if ($paginationSettings && !empty($data)) {
        $itemsPerPage = (int) ($paginationSettings["items_per_page"] ?? 10);
        $totalItems = (int) ($paginationSettings["total_items"] ?? count($data)); 
        
        // Adjust current page from GET, defaulting to 0 for 0-indexed pagination
        $currentPage = (int) ($_GET['page'] ?? $paginationSettings["current_page"] ?? 0);

        // 0-indexed pagination logic
        if ($currentPage < 0) $currentPage = 0;
        $totalPages = $itemsPerPage > 0 ? ceil($totalItems / $itemsPerPage) -1 : 0;
        if ($totalPages < 0) $totalPages = 0; // Ensure totalPages is not negative
        if ($currentPage > $totalPages && $totalItems > 0) $currentPage = $totalPages;
        $offset = $currentPage * $itemsPerPage;
        

        if (!isset($paginationSettings["total_items"])) {
            $pageData = array_slice($data, $offset, $itemsPerPage);
        } else {
            // If total_items is set, it implies $data is already the correct slice for the current page.
            // However, we still need to respect items_per_page for display consistency if $data has more items.
            if (count($data) > $itemsPerPage) {
                $pageData = array_slice($data, 0, $itemsPerPage);
            } else {
                $pageData = $data;
            }
        }
    }

    // --- Start Table Output ---
    echo '<div class="ui-table-container">';
    echo '<table id="' . $tableId . '" class="' . $tableClass . '">';

    // --- Table Header ---
    echo '<thead class="ui-table-header"><tr>';
    if ($selectable) {
        echo '<th class="ui-table-header-cell" style="width:40px;vertical-align:middle"><input type="checkbox" onclick="toggleSelectAll(this, \'selected_ids[]\')" style="vertical-align:middle"></th>';
    }
    foreach ($columns as $column) {
        echo '<th class="ui-table-header-cell">';
        if ($column["sortable"] ?? false) {
            $newDirection = ($sortColumn === $column["key"] && $sortDirection === 'asc') ? 'desc' : 'asc';
            
            $urlParts = parse_url($baseUrl);
            $path = $urlParts['path'] ?? '';
            $queryParams = [];
            if (isset($urlParts['query'])) {
                parse_str($urlParts['query'], $queryParams);
            }

            $mergedQueryParams = array_merge($_GET, $queryParams); 
            $mergedQueryParams['sort'] = $column["key"];
            $mergedQueryParams['dir'] = $newDirection;
            // Reset to page 0 when sorting for 0-indexed pagination
            $mergedQueryParams['page'] = 0; 

            if (isset($queryParams['id']) && !empty($queryParams['id'])) {
                 $mergedQueryParams['id'] = $queryParams['id']; 
            }

            $sortUrl = $path . '?' . http_build_query($mergedQueryParams);
            
            echo '<a href="' . htmlspecialchars($sortUrl) . '">';
            echo htmlspecialchars($column["label"]); // Ensure label is also escaped
            if ($sortColumn === $column["key"]) {
                echo ' <i class="fas fa-sort-' . ($sortDirection === 'asc' ? 'up' : 'down') . '"></i>';
            }
            echo '</a>';
        } else {
            echo htmlspecialchars($column["label"]);
        }
        echo '</th>';
    }
    if (!empty($actions)) {
        echo '<th class="ui-table-header-cell actions-header-cell">Azioni</th>';
    }
    echo '</tr></thead>';

    // --- Table Body ---
    echo '<tbody class="ui-table-body">';
    if (empty($pageData)) {
        $colspan = count($columns) + (!empty($actions) ? 1 : 0) + ($selectable ? 1 : 0);
        echo '<tr class="ui-table-empty-row"><td colspan="' . $colspan . '" style="text-align:center">Nessun dato disponibile.</td></tr>';
    } else {
        foreach ($pageData as $row) {
            echo '<tr class="ui-table-row">';
            if ($selectable) {
                $pkValue = $row[$primaryKey] ?? '';
                echo '<td class="ui-table-cell" style="width:40px;vertical-align:middle"><input type="checkbox" name="selected_ids[]" value="' . htmlspecialchars($pkValue) . '" style="vertical-align:middle"></td>';
            }
            foreach ($columns as $column) {
                $cellValue = $row[$column["key"]] ?? '';
                if (isset($column["format_callback"]) && is_callable($column["format_callback"])) {
                    $cellValue = call_user_func($column["format_callback"], $cellValue, $row);
                }
                echo '<td class="ui-table-cell">' . $cellValue . '</td>';
            }

            // Actions column
            if (!empty($actions)) {
                echo '<td class="ui-table-cell actions-cell">';
                foreach ($actions as $action) {
                    $showAction = true;
                    if (isset($action["condition_callback"]) && is_callable($action["condition_callback"])) {
                        $showAction = call_user_func($action["condition_callback"], $row);
                    }

                    if ($showAction) {
                        $actionClass = $action["class"] ?? 'table-action-icon';

                        // Handle URL with placeholders
                        if (is_callable($action["url"])) {
                            $actionUrl = call_user_func($action["url"], $row);
                        } else {
                            $actionUrl = $action["url"];
                        }

                        // Replace placeholders in the onClick action
                        $linkAttributes = '';
                        if (isset($action["onclick"])) {
                            $onclickValue = call_user_func($action["onclick"], $row);
                            $linkAttributes .= ' onclick="' . $onclickValue . '"';
                        }

                        // Render the action button
                        echo '<a href="' . $actionUrl . '" title="' . $action["label"] . '" class="' . $actionClass . '"' . $linkAttributes . '>';
                        echo '<i class="' . $action["icon"] . '"></i>';
                        echo '</a> ';
                    }
                }
                echo '</td>';
            }
            echo '</tr>';
        }
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>'; // End ui-table-container

    // --- Pagination Controls ---
    // Show pagination controls only if there is more than one page (totalPages > 0 for 0-indexed)
    if ($paginationSettings && $totalPages > 0) { 
        echo '<div class="ui-table-pagination" style="text-align:center;margin-top:16px">';
        echo '<div class="pagination-bar">';

        // Previous button (for 0-indexed)
        if ($currentPage > 0) {
            $prevPageParams = http_build_query(array_merge($_GET, ['page' => $currentPage - 1]));
            echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $prevPageParams) . '" class="pagination-btn">&laquo; Precedente</a>';
        }

        // Page numbers (for 0-indexed)
        $numPageLinksToShow = 5; 
        $startPage = 0;
        $endPage = $totalPages;

        if ($totalPages + 1 > $numPageLinksToShow) { // +1 because totalPages is 0-indexed
            $halfLinks = floor($numPageLinksToShow / 2);
            $startPage = $currentPage - $halfLinks;
            $endPage = $currentPage + $halfLinks;

            if ($startPage < 0) {
                $endPage -= $startPage; 
                $startPage = 0;
            }
            if ($endPage > $totalPages) {
                $startPage -= ($endPage - $totalPages); 
                $endPage = $totalPages;
            }
            if ($startPage < 0) $startPage = 0; 
        }
        
        // Ellipsis for first page if needed (for 0-indexed)
        if ($startPage > 0) {
            $firstPageParams = http_build_query(array_merge($_GET, ['page' => 0]));
            echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $firstPageParams) . '" class="pagination-btn">0</a>';
            if ($startPage > 1) { 
                 echo '<span class="pagination-btn pagination-disabled">...</span>';
            }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            $pageParams = http_build_query(array_merge($_GET, ['page' => $i]));
            if ($i == $currentPage) {
                echo '<button class="pagination-btn pagination-active"> ' . $i . '</button>';
            } else {
                echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $pageParams) . '" class="pagination-btn">' . $i . '</a>';
            }
        }
        
        // Ellipsis for last page if needed (for 0-indexed)
        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1 ) { 
                echo '<span class="pagination-btn pagination-disabled">...</span>';
            }
            $lastPageParams = http_build_query(array_merge($_GET, ['page' => $totalPages]));
            echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $lastPageParams) . '" class="pagination-btn">' . $totalPages . '</a>';
        }

        // Next button (for 0-indexed)
        if ($currentPage < $totalPages) {
            $nextPageParams = http_build_query(array_merge($_GET, ['page' => $currentPage + 1]));
            echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $nextPageParams) . '" class="pagination-btn">Successivo &raquo;</a>';
        }

        echo '</div>';
        echo '</div>';
    }
    echo '</div>'; // Close ui-table-container
}

?>
<script>
function toggleSelectAll(source, name) {
  var checkboxes = document.getElementsByName(name);
  for (var i = 0; i < checkboxes.length; i++) {
    checkboxes[i].checked = source.checked;
  }
  if (typeof updateDeleteSelectedButton === 'function') {
    updateDeleteSelectedButton();
  }
}
</script>
<style>
    .actions-cell a {
        margin-right: 5px;
        text-decoration: none;
    }

    .actions-cell a:last-child {
        margin-right: 0;
    }

    .ui-table-container {
        overflow-x: auto;
    }

    .ui-table {
        width: 100%;
        border-collapse: collapse;
    }

    .ui-table-header {
        background-color: var(--table-header-bg, #f0f2f5);
    }

    .ui-table-header-cell {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid var(--table-border-color, #ddd);
    }

    .ui-table-body {
        background-color: var(--bg-color-card, #fff);
    }

    .ui-table-row:nth-child(even) {
        background-color: var(--table-row-even-bg, #f9f9f9);
    }

    .ui-table-cell {
        padding: 8px;
        border-bottom: 1px solid var(--table-border-color, #ddd);
    }

    .ui-table-empty-row {
        text-align: center;
        color: var(--text-color-secondary, #777);
    }

    .ui-table-pagination {
        margin-top: 16px;
    }

    .pagination-arrow {
        padding: 8px 16px;
    }

    .pagination-number {
        padding: 8px 16px;
    }

    .pagination-active {
        background-color: var(--pagination-active-bg, #4CAF50);
        color: var(--pagination-active-text, white);
    }

    .pagination-ellipsis {
        padding: 8px 16px;
    }
</style>
