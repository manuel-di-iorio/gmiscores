<?php
/**
 * Site footer component.
 * 
 * Usage:
 *   require_once("includes/footer.php");
 * 
 * Variables expected from the calling page:
 *   $theme        - 'light' or 'dark'
 *   $currentLang  - current language code
 * 
 * Optional:
 *   $footerMarginLeft - CSS margin-left value (default: '0!important')
 */
$footerMarginLeft = $footerMarginLeft ?? '0!important';
?>
<footer class="modern-footer PageContentFooter" style="margin-left:<?= $footerMarginLeft ?>">
  <div class="footer-content">
    <div class="footer-section about">
      <a href="/"><img src="/assets/images/logo<?= $theme === 'dark' ? 'White' : '' ?>.svg" class="footer-logo" alt="Logo"></a>
      <p><?= __("footer_about") ?></p>
      <p>&copy; <?= date("Y") ?> GameMaker Italia. <?= __("footer_copyright") ?></p>
    </div>
    <div class="footer-section links">
      <h5 class="footer-heading"><?= __("footer_links_title") ?></h5>
      <ul>
        <li><a href="/documentation.php" class="footer-link"><?= __("footer_documentation") ?></a></li>
        <li><a href="/terms.php" class="footer-link"><?= __("footer_terms") ?></a></li>
        <li><a href="/privacy.php" class="footer-link"><?= __("footer_privacy") ?></a></li>
        <li><a href="/cookie.php" class="footer-link"><?= __("footer_cookie") ?></a></li>
        <li><a href="https://github.com/manuel-di-iorio/gmicloud/issues" class="footer-link" target="_blank" rel="noopener noreferrer"><?= __("footer_report_issue") ?></a></li>
      </ul>
    </div>
    <div class="footer-section social">
      <h5 class="footer-heading"><?= __("footer_follow_title") ?></h5>
      <a href="https://discord.gg/85RCMD9VQD" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Discord"><i class="fab fa-discord"></i></a>
      <a href="https://www.facebook.com/gmitalia" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
      <a href="https://twitter.com/gamemakerita" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
      <a href="https://github.com/manuel-di-iorio/gmicloud" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="GitHub"><i class="fab fa-github"></i></a>
    </div>
  </div>
  <div class="footer-bottom">
    <a href="/switch-theme.php?theme=<?= $theme === 'dark' ? 'light' : 'dark' ?>&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="footer-theme-link">
      <i class="fas <?= $theme === 'dark' ? 'fa-sun' : 'fa-moon' ?>"></i> <?= __('index_theme_toggle') ?>
    </a>
    <div class="footer-lang">
      <a href="/switch-lang.php?lang=en&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="footer-lang-link<?php if ($currentLang === 'en') { echo ' footer-lang-link--active'; } ?>"><?= __("lang_en") ?></a>
      <span class="footer-lang-sep">|</span>
      <a href="/switch-lang.php?lang=it&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="footer-lang-link<?php if ($currentLang === 'it') { echo ' footer-lang-link--active'; } ?>"><?= __("lang_it") ?></a>
      <span class="footer-lang-sep">|</span>
      <a href="/switch-lang.php?lang=es&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="footer-lang-link<?php if ($currentLang === 'es') { echo ' footer-lang-link--active'; } ?>"><?= __("lang_es") ?></a>
      <span class="footer-lang-sep">|</span>
      <a href="/switch-lang.php?lang=fr&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="footer-lang-link<?php if ($currentLang === 'fr') { echo ' footer-lang-link--active'; } ?>"><?= __("lang_fr") ?></a>
      <span class="footer-lang-sep">|</span>
      <a href="/switch-lang.php?lang=de&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="footer-lang-link<?php if ($currentLang === 'de') { echo ' footer-lang-link--active'; } ?>"><?= __("lang_de") ?></a>
    </div>
  </div>
</footer>
