<div class="w3-container w3-padding-large login-container">
  <div class="login-box">
    <img src="assets/images/logoSmall.png" alt="Logo" class="login-logo">
    <h4>Per continuare, effettua il login con Discord</h4>

    <p class="w3-small w3-center">
      Cliccando "Accedi con Discord", accetti i nostri
      <a href="terms.php" target="_blank">Termini e Condizioni</a>,
      <a href="privacy.php" target="_blank">Privacy Policy</a> e
      <a href="cookie.php" target="_blank">Cookie Policy</a>.
    </p>

    <a href="<?= $loginRedirectUrl ?>" class="discord-login-button">
      <button type="submit" class="w3-button w3-padding-large w3-margin-top w3-margin-bottom">
        <i class="fab fa-discord w3-margin-right"></i> Accedi con Discord
      </button>
    </a>
  </div>
</div>
