const Api = {
  async isAvailable() {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 900);
    try {
      const response = await fetch("/api/health", { signal: controller.signal });
      return response.ok;
    } catch (error) {
      return false;
    } finally {
      clearTimeout(timeout);
    }
  },
  async request(path, options = {}) {
    const response = await fetch(`/api${path}`, {
      credentials: "include",
      headers: { "Content-Type": "application/json", ...(options.headers || {}) },
      ...options
    });
    const data = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error(data.message || "Ошибка запроса");
    return data;
  },
  async login(email, password) {
    return this.request("/auth/login", { method: "POST", body: JSON.stringify({ email, password }) });
  },
  async register(payload) {
    return this.request("/auth/register", { method: "POST", body: JSON.stringify(payload) });
  },
  async me() {
    return this.request("/auth/me");
  },
  async logout() {
    return this.request("/auth/logout", { method: "POST" });
  },
  async updateProfile(payload) {
    return this.request("/profile", { method: "PUT", body: JSON.stringify(payload) });
  },
  async updatePassword(payload) {
    return this.request("/profile/password", { method: "PUT", body: JSON.stringify(payload) });
  }
};
