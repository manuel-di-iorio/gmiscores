<?php if ($_SERVER['REQUEST_METHOD'] === "GET") { ?>
  <!-- Load the ReCaptcha script -->
  <!--<script src="https://www.google.com/recaptcha/api.js" async defer></script>-->
  
  <!-- Add game form -->
  <form class="internal-card internal-card--form" id="form-add-game" method="POST" onsubmit="return onSubmit()">
    <div class="internal-card__title"><i class="fas fa-gamepad"></i> <?= __('add_game_title') ?></div>
    <div class="ui-input-group">
      <label class="ui-label" for="name"><?= __('add_game_name_label') ?></label>
      <input class="ui-input" name="name" id="name" type="text" maxlength="64" required placeholder="<?= __('add_game_name_placeholder') ?>">
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
