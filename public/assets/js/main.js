// =======================
// Menu burger
// =======================
const toggle = document.querySelector(".menu-toggle");
const nav = document.querySelector(".nav");

if (toggle && nav) {
  toggle.addEventListener("click", () => {
    nav.classList.toggle("nav-open");
  });
}

// =======================
// Gestion des modales
// =======================

function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.add("is-open");
  }
}

function closeModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.remove("is-open");
  }
}

// Fermeture via boutons [x] ou "Annuler" (data-close-modal)
document.addEventListener("click", (event) => {
  const target = event.target;

  // Bouton avec data-close-modal
  if (target.matches("[data-close-modal]")) {
    const id = target.getAttribute("data-close-modal");
    if (id) {
      closeModal(id);
    }
  }

  // Clic sur le fond (overlay)
  if (target.classList.contains("modal-overlay")) {
    target.classList.remove("is-open");
  }
});

// =======================
// Participation covoiturage
// =======================

let formToSubmit = null;

// On supporte 1 ou plusieurs formulaires .js-participate-form
const participateForms = document.querySelectorAll(".js-participate-form");

participateForms.forEach((form) => {
  form.addEventListener("submit", (event) => {
    event.preventDefault(); // on bloque l'envoi immédiat

    const isLogged = form.dataset.logged === "1";
    const price = parseFloat(form.dataset.price || "0");

    // Cas 1 : utilisateur non connecté → modale "Connexion requise"
    if (!isLogged) {
      openModal("login-modal");
      return;
    }

    // Cas 2 : utilisateur connecté → modale de confirmation crédits
    const confirmModal = document.getElementById("confirm-modal");
    const confirmTextElt = document.getElementById("confirm-modal-text");
    const confirmBtn = document.getElementById("confirm-modal-yes");

    // Sécurité : si la modale n'existe pas, fallback sur confirm()
    if (!confirmModal || !confirmTextElt || !confirmBtn) {
      const ok = window.confirm(
        `Ce trajet va vous coûter ${price.toFixed(
          2
        )} crédits. Confirmez-vous ?`
      );
      if (ok) {
        form.submit();
      }
      return;
    }

    // On remplit le texte de la modale
    confirmTextElt.textContent =
      `Ce trajet va vous coûter ${price.toFixed(
        2
      )} crédits. Voulez-vous utiliser ces crédits pour participer ?`;

    // On mémorise le formulaire actuel
    formToSubmit = form;

    // On ouvre la modale
    openModal("confirm-modal");
  });
});

// Bouton "Confirmer" dans la modale
const confirmYesBtn = document.getElementById("confirm-modal-yes");
if (confirmYesBtn) {
  confirmYesBtn.addEventListener("click", () => {
    if (formToSubmit) {
      closeModal("confirm-modal");
      formToSubmit.submit(); // envoi réel
      formToSubmit = null;
    }
  });
}
