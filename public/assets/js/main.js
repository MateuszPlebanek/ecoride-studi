const toggle = document.querySelector(".menu-toggle");
const nav = document.querySelector(".nav");

if (toggle && nav) {
  toggle.addEventListener("click", () => {
    nav.classList.toggle("nav-open");
  });
}


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

document.addEventListener("click", (event) => {
  const target = event.target;

  if (target.matches("[data-close-modal]")) {
    const id = target.getAttribute("data-close-modal");
    if (id) {
      closeModal(id);
    }
  }

  if (target.classList.contains("modal-overlay")) {
    target.classList.remove("is-open");
  }
});


let formToSubmit = null;

const participateForms = document.querySelectorAll(".js-participate-form");

participateForms.forEach((form) => {
  form.addEventListener("submit", (event) => {
    event.preventDefault(); 

    const isLogged = form.dataset.logged === "1";
    const price = parseFloat(form.dataset.price || "0");

    if (!isLogged) {
      openModal("login-modal");
      return;
    }

    const confirmModal = document.getElementById("confirm-modal");
    const confirmTextElt = document.getElementById("confirm-modal-text");
    const confirmBtn = document.getElementById("confirm-modal-yes");

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

    confirmTextElt.textContent =
      `Ce trajet va vous coûter ${price.toFixed(
        2
      )} crédits. Voulez-vous utiliser ces crédits pour participer ?`;

    formToSubmit = form;

    openModal("confirm-modal");
  });
});

const confirmYesBtn = document.getElementById("confirm-modal-yes");
if (confirmYesBtn) {
  confirmYesBtn.addEventListener("click", () => {
    if (formToSubmit) {
      closeModal("confirm-modal");
      formToSubmit.submit(); 
      formToSubmit = null;
    }
  });
}
