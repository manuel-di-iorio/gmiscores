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
function render_table(array $data, array $columns, array $actions = [], array $options = []): void {
    // Default options
    $tableId = $options["table_id"] ?? 'genericTable' . rand(1000, 9999);
    $tableClass = $options["table_class"] ?? 'w3-table-all w3-hoverable';
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
                return ($sortDirection === 'asc') ? strcmp((string)$valA, (string)$valB) : strcmp((string)$valB, (string)$valA);
            }
        });
    }

    // --- Pagination Logic ---
    $pageData = $data;
    $totalPages = 1;
    $currentPage = 1;

    if ($paginationSettings && !empty($data)) {
        $itemsPerPage = (int)($paginationSettings["items_per_page"] ?? 10);
        $totalItems = (int)($paginationSettings["total_items"] ?? count($data)); // If total_items not provided, use count of current data
        $currentPage = (int)($_GET['page'] ?? $paginationSettings["current_page"] ?? 1);
        if ($currentPage < 1) $currentPage = 1;

        $totalPages = ceil($totalItems / $itemsPerPage);
        if ($currentPage > $totalPages && $totalPages > 0) $currentPage = $totalPages;

        // If total_items was provided, we assume $data is already the slice for the current page.
        // Otherwise, if we are paginating the full $data array:
        if (!isset($paginationSettings["total_items"])) {
            $offset = ($currentPage - 1) * $itemsPerPage;
            $pageData = array_slice($data, $offset, $itemsPerPage);
        } else {
             // If total_items is set, it implies $data is already the correct slice for the current page.
             // However, we still need to respect items_per_page for display consistency if $data has more items.
             if(count($data) > $itemsPerPage) {
                $pageData = array_slice($data, 0, $itemsPerPage);
             } else {
                $pageData = $data;
             }
        }
    }

    // --- Start Table Output ---
    echo '<div class="w3-responsive generic-table-container">';
    echo '<table id="' . $tableId . '" class="' . $tableClass . '">';

    // --- Table Header ---
    echo '<thead><tr>';
    foreach ($columns as $column) {
        echo '<th>';
        if ($column["sortable"] ?? false) {
            $newDirection = ($sortColumn === $column["key"] && $sortDirection === 'asc') ? 'desc' : 'asc';
            $sortUrlParams = http_build_query(array_merge($_GET, ['sort' => $column["key"], 'dir' => $newDirection, 'page' => 1])); // Reset to page 1 on sort
            echo '<a href="' . $baseUrl . '&' . $sortUrlParams . '">';
            echo $column["label"];
            if ($sortColumn === $column["key"]) {
                echo ' <i class="fas fa-sort-' . ($sortDirection === 'asc' ? 'up' : 'down') . '"></i>';
            }
            echo '</a>';
        } else {
            echo $column["label"];
        }
        echo '</th>';
    }
    if (!empty($actions)) {
        echo '<th>Azioni</th>';
    }
    echo '</tr></thead>';

    // --- Table Body ---
    echo '<tbody>';
    if (empty($pageData)) {
        $colspan = count($columns) + (!empty($actions) ? 1 : 0);
        echo '<tr><td colspan="' . $colspan . '" class="w3-center">Nessun dato disponibile.</td></tr>';
    } else {
        foreach ($pageData as $row) {
            echo '<tr>';
            foreach ($columns as $column) {
                $cellValue = $row[$column["key"]] ?? '';
                if (isset($column["format_callback"]) && is_callable($column["format_callback"])) {
                    $cellValue = call_user_func($column["format_callback"], $cellValue, $row);
                }
                echo '<td>' . (string)$cellValue . '</td>';
            }

            // Actions column
            if (!empty($actions)) {
                echo '<td class="actions-cell">';
                foreach ($actions as $action) {
                    $showAction = true;
                    if (isset($action["condition_callback"]) && is_callable($action["condition_callback"])) {
                        $showAction = call_user_func($action["condition_callback"], $row);
                    }

                    if ($showAction) {
                        $actionClass = $action["class"] ?? 'w3-button w3-tiny w3-padding-small';

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
    echo '</div>'; // End w3-responsive

    // --- Pagination Controls ---
    if ($paginationSettings && $totalPages > 1) {
        echo '<div class="w3-center w3-padding-16 generic-table-pagination">';
        echo '<div class="w3-bar w3-border w3-round">';

        // Previous button
        if ($currentPage > 1) {
            $prevPageParams = http_build_query(array_merge($_GET, ['page' => $currentPage - 1]));
            echo '<a href="' . htmlspecialchars($baseUrl) . '&' . $prevPageParams . '" class="w3-button">&laquo; Precedente</a>';
        } else {
            echo '<button class="w3-button w3-disabled">&laquo; Precedente</button>';
        }

        // Page numbers
        // Show a limited number of page links to avoid clutter
        $maxPagesToShow = 5;
        $startPage = max(1, $currentPage - floor($maxPagesToShow / 2));
        $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);
        
        if ($endPage - $startPage + 1 < $maxPagesToShow) {
            $startPage = max(1, $endPage - $maxPagesToShow + 1);
        }

        if ($startPage > 1) {
            $firstPageParams = http_build_query(array_merge($_GET, ['page' => 1]));
            echo '<a href="' . htmlspecialchars($baseUrl) . '&' . $firstPageParams . '" class="w3-button">1</a>';
            if ($startPage > 2) {
                echo '<span class="w3-button w3-disabled">...</span>';
            }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $currentPage) {
                echo '<button class="w3-button w3-theme-d1">' . $i . '</button>';
            } else {
                $pageParams = http_build_query(array_merge($_GET, ['page' => $i]));
                echo '<a href="' . htmlspecialchars($baseUrl) . '&' . $pageParams . '" class="w3-button">' . $i . '</a>';
            }
        }

        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1) {
                echo '<span class="w3-button w3-disabled">...</span>';
            }
            $lastPageParams = http_build_query(array_merge($_GET, ['page' => $totalPages]));
            echo '<a href="' . htmlspecialchars($baseUrl) . '&' . $lastPageParams . '" class="w3-button">' . $totalPages . '</a>';
        }

        // Next button
        if ($currentPage < $totalPages) {
            $nextPageParams = http_build_query(array_merge($_GET, ['page' => $currentPage + 1]));
            echo '<a href="' . htmlspecialchars($baseUrl) . '&' . $nextPageParams . '" class="w3-button">Successivo &raquo;</a>';
        } else {
            echo '<button class="w3-button w3-disabled">Successivo &raquo;</button>';
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
    overflow-x: auto; /* Ensure table is responsive on small screens */
}
.generic-table-pagination .w3-bar .w3-button {
    min-width: 30px; /* Ensure page numbers are not too squeezed */
}
.generic-table-pagination .w3-disabled {
    cursor: not-allowed;
    opacity: 0.6;
}
</style>
