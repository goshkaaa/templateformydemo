document.addEventListener("DOMContentLoaded", async () => {
  const grid = document.querySelector("[data-catalog-grid]");
  if (!grid) return;
  let products = DEMO_PRODUCTS;
  try {
    const response = await fetch("data/products.json");
    if (response.ok) products = await response.json();
  } catch (error) {
    products = DEMO_PRODUCTS;
  }

  const search = document.querySelector("[data-catalog-search]");
  const filter = document.querySelector("[data-catalog-filter]");

  const render = () => {
    const query = (search.value || "").toLowerCase();
    const category = filter.value;
    const items = products.filter((product) => {
      const matchesQuery = `${product.title} ${product.description}`.toLowerCase().includes(query);
      const matchesCategory = category === "all" || product.category === category;
      return matchesQuery && matchesCategory;
    });
    grid.innerHTML = items.map((product) => `
      <article class="product card">
        <img class="product__media" src="${product.image}" alt="${product.title}" loading="lazy">
        <div class="product__body">
          <span class="badge">${product.category}</span>
          <h3>${product.title}</h3>
          <p class="muted">${product.description}</p>
          <strong class="product__price">${product.price.toLocaleString("ru-RU")} ₽</strong>
          <button class="btn" data-favorite="${product.id}" type="button">В избранное</button>
        </div>
      </article>
    `).join("") || "<p class='muted'>Ничего не найдено</p>";
  };

  search.addEventListener("input", render);
  filter.addEventListener("change", render);
  grid.addEventListener("click", (event) => {
    const button = event.target.closest("[data-favorite]");
    if (!button) return;
    if (!Storage.toggleFavorite(Number(button.dataset.favorite))) return toast("Войдите, чтобы добавить в избранное", "error");
    toast("Избранное обновлено");
  });
  render();
});
