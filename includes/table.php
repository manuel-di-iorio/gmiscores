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
 *                       - "table_class": (Optional) CSS classes for the table element. Defaults to "w3-table-all w3-hoverable".
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
    // Default options
    $tableId = $options["table_id"] ?? 'genericTable' . rand(1000, 9999);
    $tableClass = $options["table_class"] ?? 'modern-table w3-table w3-border w3-card-4';
    $defaultSortColumn = $options["default_sort_column"] ?? null;
    $defaultSortDirection = $options["default_sort_direction"] ?? 'asc';
    $paginationSettings = $options["pagination"] ?? null;
    $baseUrl = $options["base_url"] ?? '';
    $primaryKey = $options["primary_key"] ?? 'id';

    // Sorting parameters from GET request or defaults
    $sortColumn = $_GET['sort'] ?? $defaultSortColumn;
    $sortDirection = $_GET['dir'] ?? $defaultSortDirection;

    // --- Data Sorting (if a sort column is specified) ---
    if ($sortColumn !== null && !empty($data)) {
        usort($data, function ($a, $b) use ($sortColumn, $sortDirection) {
            $valA = $a[$sortColumn] ?? null;
            $valB = $b[$sortColumn] ?? null;

            if ($valA === $valB) {
                return 0;
            }

            if (is_numeric($valA) && is_numeric($valB)) {
                return ($sortDirection === 'asc') ? ($valA - $valB) : ($valB - $valA);
            } else {
                return ($sortDirection === 'asc') ? strcmp((string) $valA, (string) $valB) : strcmp((string) $valB, (string) $valA);
            }
        });
    }

    // --- Pagination Logic ---
    $pageData = $data;
    $totalPages = 1;
    $currentPage = 1;

    if ($paginationSettings && !empty($data)) {
        $itemsPerPage = (int) ($paginationSettings["items_per_page"] ?? 10);
        $totalItems = (int) ($paginationSettings["total_items"] ?? count($data)); // If total_items not provided, use count of current data
        $currentPage = (int) ($_GET['page'] ?? $paginationSettings["current_page"] ?? 1);
        if ($currentPage < 1)
            $currentPage = 1;

        $totalPages = ceil($totalItems / $itemsPerPage);
        if ($currentPage > $totalPages && $totalPages > 0)
            $currentPage = $totalPages;

        // If total_items was provided, we assume $data is already the slice for the current page.
        // Otherwise, if we are paginating the full $data array:
        if (!isset($paginationSettings["total_items"])) {
            $offset = ($currentPage - 1) * $itemsPerPage;
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
    echo '<div class="modern-table-container w3-responsive">';
    echo '<table id="' . $tableId . '" class="' . $tableClass . '">';

    // --- Table Header ---
    echo '<thead class="modern-table-header"><tr>';
    foreach ($columns as $column) {
        echo '<th class="modern-table-header-cell">';
        if ($column["sortable"] ?? false) {
            $newDirection = ($sortColumn === $column["key"] && $sortDirection === 'asc') ? 'desc' : 'asc';
            $sortUrlParams = http_build_query(array_merge($_GET, ['sort' => $column["key"], 'dir' => $newDirection, 'page' => ($paginationSettings ? 1 : ($currentPage ?? 1))])); // Reset to page 1 or current if no pagination
            echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $sortUrlParams) . '">';
            echo $column["label"];
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
        echo '<th class="modern-table-header-cell actions-header-cell">Azioni</th>';
    }
    echo '</tr></thead>';

    // --- Table Body ---
    echo '<tbody class="modern-table-body">';
    if (empty($pageData)) {
        $colspan = count($columns) + (!empty($actions) ? 1 : 0);
        echo '<tr class="modern-table-empty-row"><td colspan="' . $colspan . '" class="w3-center">Nessun dato disponibile.</td></tr>';
    } else {
        foreach ($pageData as $row) {
            echo '<tr class="modern-table-row">';
            foreach ($columns as $column) {
                $cellValue = $row[$column["key"]] ?? '';
                if (isset($column["format_callback"]) && is_callable($column["format_callback"])) {
                    $cellValue = call_user_func($column["format_callback"], $cellValue, $row);
                }
                echo '<td class="modern-table-cell">' . $cellValue . '</td>';
            }

            // Actions column
            if (!empty($actions)) {
                echo '<td class="modern-table-cell actions-cell">';
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
    echo '</div>'; // End modern-table-container

    // --- Pagination Controls ---
    if ($paginationSettings && $totalPages > 1) {
        // MODIFICA: Aggiunta classe 'modern-table-pagination' e rimossa 'w3-border w3-round' per uno stile più pulito dalla paginazione base
        echo '<div class="w3-center w3-padding-16 modern-table-pagination">';
        echo '<div class="w3-bar">';

        // Previous button
        if ($currentPage > 1) {
            $prevPageParams = http_build_query(array_merge($_GET, ['page' => $currentPage - 1]));
            // MODIFICA: Aggiunta classe 'pagination-arrow'
            echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $prevPageParams) . '" class="w3-button pagination-arrow">&laquo;</a>';
        } else {
            // MODIFICA: Aggiunta classe 'pagination-arrow'
            echo '<button class="w3-button w3-disabled pagination-arrow">&laquo;</button>';
        }

        // Page numbers
        $maxPagesToShow = 5;
        $startPage = max(1, $currentPage - floor($maxPagesToShow / 2));
        $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);
        if ($endPage - $startPage + 1 < $maxPagesToShow) {
            $startPage = max(1, $endPage - $maxPagesToShow + 1);
        }

        if ($startPage > 1) {
            $firstPageParams = http_build_query(array_merge($_GET, ['page' => 1]));
            echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $firstPageParams) . '" class="w3-button pagination-number">1</a>';
            if ($startPage > 2) {
                echo '<span class="w3-button w3-disabled pagination-ellipsis">...</span>';
            }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            $pageParams = http_build_query(array_merge($_GET, ['page' => $i]));
            $activeClass = ($i == $currentPage) ? 'w3-dark-grey pagination-active' : '';
            echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $pageParams) . '" class="w3-button pagination-number ' . $activeClass . '">' . $i . '</a>';
        }

        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1) {
                echo '<span class="w3-button w3-disabled pagination-ellipsis">...</span>';
            }
            $lastPageParams = http_build_query(array_merge($_GET, ['page' => $totalPages]));
            echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $lastPageParams) . '" class="w3-button pagination-number">' . $totalPages . '</a>';
        }

        // Next button
        if ($currentPage < $totalPages) {
            $nextPageParams = http_build_query(array_merge($_GET, ['page' => $currentPage + 1]));
            // MODIFICA: Aggiunta classe 'pagination-arrow'
            echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $nextPageParams) . '" class="w3-button pagination-arrow">Successivo &raquo;</a>';
        } else {
            // MODIFICA: Aggiunta classe 'pagination-arrow'
            echo '<button class="w3-button w3-disabled pagination-arrow">Successivo &raquo;</button>';
        }

        echo '</div>';
        echo '<p class="w3-small">Pagina ' . $currentPage . ' di ' . $totalPages . ' (Totale: ' . ($paginationSettings["total_items"] ?? count($data)) . ' elementi)</p>';
        echo '</div>';
    }
}

?>
<style>
    /* Optional: Basic styling for action icons if not using a framework like w3-button */
    .actions-cell a {
        margin-right: 5px;
        text-decoration: none;
    }

    .actions-cell a:last-child {
        margin-right: 0;
    }

    .generic-table-container {
        overflow-x: auto;
        /* Ensure table is responsive on small screens */
    }

    .generic-table-pagination .w3-bar .w3-button {
        min-width: 30px;
        /* Ensure page numbers are not too squeezed */
    }

    .generic-table-pagination .w3-disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    .modern-table-container {
        overflow-x: auto;
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
    }

    .modern-table-header {
        background-color: #f1f1f1;
    }

    .modern-table-header-cell {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .modern-table-body {
        background-color: #fff;
    }

    .modern-table-row:nth-child(even) {
        background-color: #f9f9f9;
    }

    .modern-table-cell {
        padding: 8px;
        border-bottom: 1px solid #ddd;
    }

    .modern-table-empty-row {
        text-align: center;
        color: #999;
    }

    .modern-table-pagination {
        margin-top: 16px;
    }

    .pagination-arrow {
        padding: 8px 16px;
    }

    .pagination-number {
        padding: 8px 16px;
    }

    .pagination-active {
        background-color: #4CAF50;
        color: white;
    }

    .pagination-ellipsis {
        padding: 8px 16px;
    }

    .table-action-icon {
        color: #333;
        font-size: 14px;
        margin-right: 5px;
        text-decoration: none;
    }
</style>
