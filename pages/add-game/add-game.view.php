<?php if ($_SERVER['REQUEST_METHOD'] === "GET") { ?>
  <!-- Load the ReCaptcha script -->
  <!--<script src="https://www.google.com/recaptcha/api.js" async defer></script>-->
  
  <!-- Add game form -->
  <form class="internal-card internal-card--form" id="form-add-game" method="POST" onsubmit="return onSubmit()">
    <div class="internal-card__title"><i class="fas fa-gamepad"></i> Nuovo gioco</div>
    <div class="ui-input-group">
      <label class="ui-label" for="name">Nome</label>
      <input class="ui-input" name="name" id="name" type="text" maxlength="64" required placeholder="Inserisci il nome del gioco">
    </div>

    <!-- ReCaptcha div -->
    <div id='recaptcha' 
         class="g-recaptcha"
         data-sitekey="<?= $config["recaptchaKey"] ?>"
         data-callback="onCompleted"
         data-size="invisible"></div>

    <!-- Submit button -->
    <?= ui_button('Aggiungi', 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'type' => 'submit', 'attrs' => ['id' => 'form-add-game__submit-btn']]) ?>
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
    <?= ui_button('Torna alla pagina precedente', 'primary', 'md', ['href' => '']) ?>
  </div>
</div>

<?php } ?>
