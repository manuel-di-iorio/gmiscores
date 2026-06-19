<div class="internal-page">
  <div class="internal-card" style="text-align:center;padding:40px">
    <i class="fab fa-discord" style="font-size:3em;color:#5865F2;margin-bottom:16px"></i>
    <h4 style="margin:0 0 8px;font-weight:600"><?= __('approval_title') ?></h4>
    <p style="color:var(--text-muted,#666);margin:0 0 24px;max-width:480px;margin-left:auto;margin-right:auto">
      <?= __('approval_desc1') ?>
      <strong><?= $config["platformTitle"] ?></strong> <?= __('approval_desc2') ?>
    </p>
    <?= ui_button(__('approval_button'), 'primary', 'md', ['icon' => 'fab fa-discord', 'href' => 'https://discord.gg/XfMfpNA', 'attrs' => ['style' => 'background:#5865F2!important']]) ?>
  </div>
</div>
