// Script to open and close the navbar
function w3_open() {
  document.getElementById("navbar").style.display = "block";
  document.getElementById("overlay").style.display = "block";
}

function w3_close() {
  document.getElementById("navbar").style.display = "none";
  document.getElementById("overlay").style.display = "none";
}

// Modals
function openModal(modalId, onOpen, ctx) {
  document.getElementById(modalId).style.display = "block";
  if (onOpen) onOpen(ctx);
}

function closeModal(modalId, onClose, ctx) {
  document.getElementById(modalId).style.display = 'none';
  if (onClose) onClose(ctx);
}

// Accordions
function toggleAccordion(el) {
  // Change the arrow icon
  const elIcon = el.querySelector("i");
  direction = elIcon.className.includes("down") ? "up" : "down";
  elIcon.classList.remove("fa-arrow-circle-down");
  elIcon.classList.remove("fa-arrow-circle-up");
  elIcon.classList.add("fa-arrow-circle-" + direction);

  // Toggle the accordion
  if (direction === "down") {
    el.nextElementSibling.classList.add("w3-hide");
  } else {
    el.nextElementSibling.classList.remove("w3-hide");
  }
}

// Intersection Observer for fade-in animations on scroll
const animatedElements = document.querySelectorAll('.fade-in-up-on-scroll');

if (animatedElements.length > 0) {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
      }
    });
  }, {
    threshold: 0.1 // Trigger when 10% of the element is visible
  });

  animatedElements.forEach(element => {
    observer.observe(element);
  });
}

// Smooth scroll for anchor links (optional, if you add them)
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const hrefAttribute = this.getAttribute('href');
    // Ensure it's a valid selector and not just "#" or "#!"
    if (hrefAttribute && hrefAttribute.length > 1 && document.querySelector(hrefAttribute)) {
      e.preventDefault();
      document.querySelector(hrefAttribute).scrollIntoView({
        behavior: 'smooth'
      });
    }
  });
});
