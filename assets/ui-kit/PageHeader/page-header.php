<?php
/**
 * ui_page_header($title, $options)
 *
 * Renders a card-style page header with title, description, badge and optional
 * back-navigation link.
 *
 * @param string $title   Page title (already translated / escaped if needed)
 * @param array  $options {
 *   string  desc        Short description shown below the title
 *   string  badge       Small pill label shown above the title
 *   string  badge_color Tailwind bg-* class for the badge (default: bg-primary/10)
 *   string  back_url    If set, renders a back-arrow link
 *   string  back_label  Tooltip / aria-label for the back link
 *   string  class       Extra classes appended to the root element
 * }
 */
function ui_page_header(string $title, array $options = []): string {
  $desc       = $options['desc']        ?? '';
  $badge      = $options['badge']       ?? '';
  $badgeColor = $options['badge_color'] ?? '';
  $backUrl    = $options['back_url']    ?? '';
  $backLabel  = $options['back_label']  ?? '';
  $extraClass = $options['class']       ?? '';

  /* ---------- back arrow ---------- */
  $backHtml = '';
  if ($backUrl) {
    $label = htmlspecialchars((string) $backLabel, ENT_QUOTES, 'UTF-8');
    $backIcon = ui_icon('fas fa-arrow-left', [
      'size' => 'sm',
      'class' => 'transition-transform duration-200 group-hover:-translate-x-0.5',
    ]);
    $backHtml = '
      <a href="' . htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') . '"
         class="group inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg
                border border-border-color bg-surface text-text-secondary shadow-sm
                no-underline transition-all duration-200 hover:border-primary-color hover:bg-surface-offset
                hover:text-primary-color hover:shadow-card-subtle
                focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-color/25"
         ' . ($label ? 'title="' . $label . '" aria-label="' . $label . '" data-tippy-content="' . $label . '"' : '') . '>
        ' . $backIcon . '
      </a>';
  }

  /* ---------- badge ---------- */
  $badgeHtml = '';
  if ($badge) {
    $colorClass = $badgeColor ?: 'bg-surface-offset text-primary-color';
    $badgeHtml = '
      <span class="mb-3 inline-flex items-center gap-1.5 rounded-full border border-border-color
                   px-3 py-1 text-[0.72rem] font-semibold uppercase
                   ' . htmlspecialchars($colorClass, ENT_QUOTES, 'UTF-8') . '">
        ' . htmlspecialchars((string) $badge, ENT_QUOTES, 'UTF-8') . '
      </span>';
  }

  /* ---------- description ---------- */
  $descHtml = '';
  if ($desc) {
    $descHtml = '
      <p class="mt-2 max-w-3xl text-sm leading-6 text-text-secondary sm:text-[0.98rem]">
        ' . htmlspecialchars((string) $desc, ENT_QUOTES, 'UTF-8') . '
      </p>';
  }

  /* ---------- assemble ---------- */
  return '
    <div class="ui-page-header mt-5 mb-7 overflow-hidden rounded-xl
                border border-border-color bg-surface-card shadow-card-subtle
                ' . htmlspecialchars($extraClass, ENT_QUOTES, 'UTF-8') . '">
      <div class="flex flex-col gap-5 border-l-4 border-primary-color px-5 py-7 sm:px-7 sm:py-8 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex min-w-0 flex-1 items-start gap-4">
          ' . $backHtml . '
          <div class="min-w-0">
            ' . $badgeHtml . '
            <h1 class="m-0 text-[1.65rem] font-bold text-text-headings sm:text-[2.05rem]">
              ' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '
            </h1>
            ' . $descHtml . '
          </div>
        </div>

        <div class="ui-page-header__slot flex shrink-0 items-center gap-3 lg:pt-1" id="page-header-slot"></div>
      </div>
    </div>';
}
