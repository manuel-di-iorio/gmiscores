<!-- Load the W3 Code Syntax Highlighter -->
<script src="https://www.w3schools.com/lib/w3codecolor.js"></script>
<script>
w3CodeColor();

// Toggle the secret visibility
function toggleSecretVisibility(inputSecretEyeBtn) {
  const inputSecret = document.getElementById("input-secret");
  const icon = inputSecretEyeBtn.querySelector("i") || inputSecretEyeBtn; // Handle if the icon is nested or direct

  if (inputSecret.type === "password") {
    inputSecret.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
    inputSecretEyeBtn.setAttribute("data-tippy-content", "Nascondi il secret del gioco");
  } else {
    inputSecret.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
    inputSecretEyeBtn.setAttribute("data-tippy-content", "Mostra il secret del gioco");
  }
  // Refresh tippy instance if available
  if (inputSecretEyeBtn._tippy) {
    inputSecretEyeBtn._tippy.setContent(inputSecretEyeBtn.getAttribute("data-tippy-content"));
  }
}

function regenerateSecret() {
  location.href = "/game-regenerate-secret.php?id=<?= $game["game_id"] ?>";
}

// Ensure Tippy tooltips are initialized after DOM is ready
document.addEventListener('DOMContentLoaded', (event) => {
  tippy('[data-tippy-content]', {
    animation: 'scale',
  });
});

// Modal handling (assuming openModal and closeModal are globally defined)
// If not, you might need to include or define them here.
// Example:
// function openModal(modalId) {
//   document.getElementById(modalId).style.display = 'block';
// }
// function closeModal(modalId) {
//   document.getElementById(modalId).style.display = 'none';
// }
</script>
