<?php
declare(strict_types=1);

// Loads a template file with the given data variables extracted into scope.
// Throws if the template doesn't exist so we catch typos early.
function render(string $template, array $data = []): void
{
    extract($data);
    $templatePath = __DIR__ . '/../../templates/' . $template . '.php';
    if (!file_exists($templatePath)) {
        throw new RuntimeException("Template {$template} not found");
    }
    require $templatePath;
}

// Short alias for htmlspecialchars — every user-supplied value in templates goes through this
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Builds a sortable column header link.
// Clicking the same column toggles asc/desc; clicking another column resets to asc.
// Keeps the current page number so sorting doesn't jump back to page 1.
function sortLink(string $column, string $label, string $currentSort, string $currentDir): string
{
    $newDir = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';

    $arrowChar = $currentDir === 'asc' ? '↑' : '↓';

    // Show the arrow only on the currently sorted column; hidden span keeps the layout stable
    $arrowHtml = ($currentSort === $column)
        ? '<span class="sort-arrow">' . $arrowChar . '</span>'
        : '<span class="sort-arrow-hidden">↑</span>';

    $page = max(1, (int)($_GET['page'] ?? 1));
    return '<a href="?sort=' . $column . '&dir=' . $newDir . '&page=' . $page . '" data-column="' . $column . '" data-label="' . $label . '" class="sort-link">' . $label . ' ' . $arrowHtml . '</a>';
}

// Returns a page range with "..." gaps for large page counts.
// E.g. for page 7 of 20: [1, '...', 5, 6, 7, 8, 9, '...', 20]
// Delta controls how many pages to show around the current one.
function getPaginationRange(int $currentPage, int $lastPage): array
{
    $delta = 2;
    $range = [];
    for ($i = 1; $i <= $lastPage; $i++) {
        if ($i == 1 || $i == $lastPage || ($i >= $currentPage - $delta && $i <= $currentPage + $delta)) {
            $range[] = $i;
        }
    }

    // Insert dots (or the missing single page) between non-consecutive numbers
    $rangeWithDots = [];
    $l = null;
    foreach ($range as $i) {
        if ($l !== null) {
            if ($i - $l === 2) {
                // gap is just one page — add it directly instead of '...'
                $rangeWithDots[] = $l + 1;
            } elseif ($i - $l !== 1) {
                $rangeWithDots[] = '...';
            }
        }
        $rangeWithDots[] = $i;
        $l = $i;
    }
    return $rangeWithDots;
}