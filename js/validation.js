function setError(input, message) {
  const field = input.closest(".field");
  const error = field ? field.querySelector(".form-error") : null;
  if (error) error.textContent = message || "";
}

function validateForm(form) {
  let valid = true;
  form.querySelectorAll("[data-required]").forEach((input) => {
    const value = input.value.trim();
    let message = "";
    if (!value) message = "Заполните поле";
    if (!message && input.type === "email" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) message = "Введите корректный email";
    if (!message && input.name === "password" && value.length < 6) message = "Минимум 6 символов";
    if (!message && input.name === "phone" && !/^[+\d\s()-]{10,}$/.test(value)) message = "Введите корректный телефон";
    setError(input, message);
    if (message) valid = false;
  });
  return valid;
}
