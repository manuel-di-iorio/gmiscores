<?php if ($_SERVER['REQUEST_METHOD'] === "GET") { ?>
  <!-- Load the ReCaptcha script -->
  <!--<script src="https://www.google.com/recaptcha/api.js" async defer></script>-->
  
  <!-- Add game form -->
  <form class="internal-card internal-card--form" id="form-add-game" method="POST" onsubmit="return onSubmit()">
    <div class="internal-card__title"><i class="fas fa-gamepad"></i> <?= __('add_game_title') ?></div>
    <div class="mb-4">
      <label class="block font-semibold mb-1.5 text-sm text-[var(--text-color)]" for="name"><?= __('add_game_name_label') ?></label>
      <input class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed" name="name" id="name" type="text" maxlength="64" required placeholder="<?= __('add_game_name_placeholder') ?>">
    </div>

    <!-- ReCaptcha div -->
    <div id='recaptcha' 
         class="g-recaptcha"
         data-sitekey="<?= $config["recaptchaKey"] ?>"
         data-callback="onCompleted"
         data-size="invisible"></div>

    <!-- Submit button -->
    <?= ui_button(__('add_game_submit'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'type' => 'submit', 'attrs' => ['id' => 'form-add-game__submit-btn']]) ?>
  </form>

  <!-- Validate the form submit -->
  <script>
  function onSubmit() {
    document.getElementById("form-add-game__submit-btn").setAttribute("disabled", "");
    //grecaptcha.execute();
    onCompleted();
    return false;
  }

  function onCompleted() {
    document.getElementById("form-add-game").submit();
  }
  </script>


<?php } else { ?>
<div class="internal-page">
  <div class="internal-card">
    <?= $formError ?>
    <?= ui_button(__('add_game_back'), 'primary', 'md', ['href' => '']) ?>
  </div>
</div>

<?php } ?>
