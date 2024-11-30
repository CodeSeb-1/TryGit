const termsLink = document.getElementById("terms-link");
const termsModal = document.getElementById("terms-modal");
const closeModal = document.getElementById("modal-close");
const agreeCheckbox = document.getElementById("agree-checkbox");
const submitButton = document.getElementById("submit-button");
const agreeRadio = document.getElementById("agree");
const disagreeRadio = document.getElementById("disagree");

termsLink.onclick = function() {
    termsModal.style.display = "flex";
}

closeModal.onclick = function() {
    termsModal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == termsModal) {
        termsModal.style.display = "none";
    }
}

agreeRadio.onclick = function() {
    agreeCheckbox.checked = true;
    agreeCheckbox.disabled = false;
    submitButton.disabled = false;
    termsModal.style.display = "none";
}

disagreeRadio.onclick = function() {
    agreeCheckbox.checked = false;
    agreeCheckbox.disabled = true;
    submitButton.disabled = true;
    termsModal.style.display = "none";
}

