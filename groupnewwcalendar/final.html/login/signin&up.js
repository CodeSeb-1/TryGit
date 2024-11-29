const sign_in_btn = document.querySelector("#sign-in-btn");
const sign_up_btn = document.querySelector("#sign-up-btn");
const container = document.querySelector(".container");

sign_up_btn.addEventListener("click", () => {
    container.classList.add("sign-up-mode");
});

sign_in_btn.addEventListener("click", () => {
    container.classList.remove("sign-up-mode");
});

// Your existing modal JavaScript
const termsLink = document.getElementById("terms-link");
const termsModal = document.getElementById("terms-modal");
const closeModal = document.getElementById("modal-close");
const agreeCheckbox = document.getElementByI