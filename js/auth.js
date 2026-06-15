document.addEventListener("DOMContentLoaded", async () => {
  const loginForm = document.querySelector("[data-login-form]");
  const registerForm = document.querySelector("[data-register-form]");
  const isProfile = document.body.dataset.page === "profile";
  const apiOnline = await Api.isAvailable();

  showAuthMode(apiOnline);

  if (isProfile) {
    const user = await getCurrentUser(apiOnline);
    if (!user) location.href = "login.html";
    else renderProfile(user, apiOnline);
  }

  loginForm?.addEventListener("submit", async (event) => {
    event.preventDefault();
    if (!validateForm(loginForm)) return;
    const data = new FormData(loginForm);
    const email = data.get("email");
    const password = data.get("password");

    if (apiOnline) {
      try {
        const result = await Api.login(email, password);
        Storage.setCurrentUser(result.user.id);
        location.href = "profile.html";
      } catch (error) {
        toast(error.message || "Неверный email или пароль", "error");
      }
      return;
    }

    const user = Storage.getDb().users.find((item) => item.email === email && item.password === password);
    if (!user) return toast("Неверный email или пароль", "error");
    Storage.setCurrentUser(user.id);
    toast("Демо-вход через localStorage");
    location.href = "profile.html";
  });

  registerForm?.addEventListener("submit", async (event) => {
    event.preventDefault();
    if (!validateForm(registerForm)) return;
    const data = new FormData(registerForm);
    const payload = {
      name: data.get("name"),
      surname: data.get("surname"),
      phone: data.get("phone"),
      email: data.get("email"),
      password: data.get("password")
    };

    if (apiOnline) {
      try {
        const result = await Api.register(payload);
        Storage.setCurrentUser(result.user.id);
        location.href = "profile.html";
      } catch (error) {
        toast(error.message || "Ошибка регистрации", "error");
      }
      return;
    }

    const db = Storage.getDb();
    if (db.users.some((user) => user.email === payload.email)) return toast("Пользователь уже существует", "error");
    const user = { id: Date.now(), ...payload, role: "user", createdAt: new Date().toISOString() };
    db.users.push(user);
    Storage.saveDb(db);
    Storage.setCurrentUser(user.id);
    toast("Демо-регистрация через localStorage");
    location.href = "profile.html";
  });

  document.querySelector("[data-logout]")?.addEventListener("click", async () => {
    if (apiOnline) {
      try {
        await Api.logout();
      } catch (error) {
        toast("Сессия уже завершена", "error");
      }
    }
    Storage.logout();
    location.href = "login.html";
  });
});

function showAuthMode(apiOnline) {
  document.querySelectorAll("[data-auth-mode]").forEach((node) => {
    node.textContent = apiOnline
      ? "Режим БД: авторизация идет через Node.js API и MySQL."
      : "Демо-режим: API не запущен, используется localStorage. Для MySQL запустите docker compose up --build и откройте http://127.0.0.1:3000";
  });
}

async function getCurrentUser(apiOnline) {
  if (apiOnline) {
    try {
      const result = await Api.me();
      Storage.setCurrentUser(result.user.id);
      return result.user;
    } catch (error) {
      Storage.logout();
      return null;
    }
  }
  return Storage.currentUser();
}

function profileDataFor(user) {
  const db = Storage.getDb();
  return {
    requests: user.requests || db.requests.filter((item) => item.userId === user.id),
    orders: user.orders || db.orders.filter((item) => item.userId === user.id),
    favorites: user.favorites || db.favorites.filter((item) => item.userId === user.id)
  };
}

function renderProfile(user, apiOnline) {
  document.querySelectorAll("[data-user-name]").forEach((node) => node.textContent = `${user.name} ${user.surname}`);
  document.querySelector("[name='profileName']")?.setAttribute("value", user.name);
  document.querySelector("[name='profileSurname']")?.setAttribute("value", user.surname);
  document.querySelector("[name='profilePhone']")?.setAttribute("value", user.phone);
  document.querySelector("[name='profileEmail']")?.setAttribute("value", user.email);

  const data = profileDataFor(user);
  fillList("requestsList", data.requests, (item) => `Заявка #${item.id}: ${item.message} — ${item.status}`);
  fillList("ordersList", data.orders, (item) => `Заказ #${item.id}: ${item.status}, дата ${item.date}`);
  fillList("favoritesList", data.favorites, (item) => `Избранное: продукт #${item.productId || item.product_id}`);

  document.querySelector("[data-profile-form]")?.addEventListener("submit", async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const formData = new FormData(form);
    const payload = {
      name: formData.get("profileName"),
      surname: formData.get("profileSurname"),
      phone: formData.get("profilePhone"),
      email: formData.get("profileEmail")
    };

    if (apiOnline) {
      try {
        const result = await Api.updateProfile(payload);
        toast("Профиль обновлен в MySQL");
        renderProfile(result.user, apiOnline);
      } catch (error) {
        toast(error.message || "Не удалось обновить профиль", "error");
      }
      return;
    }

    const freshDb = Storage.getDb();
    const editable = freshDb.users.find((item) => item.id === user.id);
    Object.assign(editable, payload);
    Storage.saveDb(freshDb);
    toast("Профиль обновлен в localStorage");
    renderProfile(editable, apiOnline);
  }, { once: true });

  document.querySelector("[data-password-form]")?.addEventListener("submit", async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    if (!validateForm(form)) return;
    const data = new FormData(form);
    if (data.get("newPassword") !== data.get("repeatPassword")) return toast("Пароли не совпадают", "error");

    if (apiOnline) {
      try {
        await Api.updatePassword({ password: data.get("newPassword") });
        form.reset();
        toast("Пароль изменен в MySQL");
      } catch (error) {
        toast(error.message || "Не удалось изменить пароль", "error");
      }
      return;
    }

    const freshDb = Storage.getDb();
    freshDb.users.find((item) => item.id === user.id).password = data.get("newPassword");
    Storage.saveDb(freshDb);
    form.reset();
    toast("Пароль изменен в localStorage");
  }, { once: true });
}

function fillList(id, items, mapper) {
  const list = document.getElementById(id);
  if (!list) return;
  list.innerHTML = items.length ? items.map((item) => `<div class="list__item"><span>${mapper(item)}</span></div>`).join("") : "<p class='muted'>Пока пусто</p>";
}
