<?php if ($_SERVER['REQUEST_METHOD'] === "GET") { ?>
  <!-- Load the ReCaptcha script -->
  <!--<script src="https://www.google.com/recaptcha/api.js" async defer></script>-->
  
  <!-- Add game form -->
  <form class="w3-container" id="form-add-game" method="POST" onsubmit="return onSubmit()">
    <p class="w3-margin-bottom w3-margin-top">
      <label for="name">Nome</label>
      <input class="w3-input" name="name" id="name" type="text" maxlength="64" required placeholder="Inserisci il nome del gioco">
    </p>

    <!-- ReCaptcha div -->
    <div id='recaptcha' 
         class="g-recaptcha"
         data-sitekey="<?= $config["recaptchaKey"] ?>"
         data-callback="onCompleted"
         data-size="invisible"></div>

    <!-- Submit button -->
    <button type="submit" id="form-add-game__submit-btn" class="w3-button w3-black w3-padding-large w3-margin-top">
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
<div class="w3-container w3-padding-large">
  <?= $formError ?>

  <!-- Back button -->
  <a href="">
    <button type="submit" class="w3-button w3-black w3-padding-large w3-margin-top">
      Torna alla pagina precedente
    </button>
  </a>
</div>

<?php } ?>
