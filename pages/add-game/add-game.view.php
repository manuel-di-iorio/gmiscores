<?php if ($_SERVER['REQUEST_METHOD'] === "GET") { ?>
  <!-- Load the ReCaptcha script -->
  <!--<script src="https://www.google.com/recaptcha/api.js" async defer></script>-->
  
  <!-- Add game form -->
  <form class="internal-card internal-card--form" id="form-add-game" method="POST" onsubmit="return onSubmit()">
    <div class="internal-card__title"><i class="fas fa-gamepad"></i> Nuovo gioco</div>
    <div class="w3-margin-bottom">
      <label for="name" style="font-weight:600;display:block;margin-bottom:6px;font-size:0.95em">Nome</label>
      <input class="w3-input internal-input" name="name" id="name" type="text" maxlength="64" required placeholder="Inserisci il nome del gioco">
    </div>

    <!-- ReCaptcha div -->
    <div id='recaptcha' 
         class="g-recaptcha"
         data-sitekey="<?= $config["recaptchaKey"] ?>"
         data-callback="onCompleted"
         data-size="invisible"></div>

    <!-- Submit button -->
    <button type="submit" id="form-add-game__submit-btn" class="w3-button w3-black w3-padding-large">
      <i class="fas fa-plus-circle w3-margin-right"></i>
      Aggiungi
    </button>
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
    <a href="">
      <button type="submit" class="w3-button w3-black w3-padding-large w3-margin-top">
        Torna alla pagina precedente
      </button>
    </a>
  </div>
</div>

<?php } ?>
