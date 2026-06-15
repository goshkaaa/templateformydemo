const crypto = require("crypto");
const fs = require("fs");
const http = require("http");
const path = require("path");
const mysql = require("mysql2/promise");

const ROOT = path.resolve(__dirname, "..");
const PORT = Number(process.env.PORT || 3000);
const COOKIE_NAME = process.env.COOKIE_NAME || "demo_exam_session";
const sessions = new Map();

const pool = mysql.createPool({
  host: process.env.DB_HOST || "127.0.0.1",
  port: Number(process.env.DB_PORT || 3306),
  user: process.env.DB_USER || "demo_user",
  password: process.env.DB_PASSWORD || "demo_password",
  database: process.env.DB_NAME || "demo_exam",
  waitForConnections: true,
  connectionLimit: 10
});

const mime = {
  ".html": "text/html; charset=utf-8",
  ".css": "text/css; charset=utf-8",
  ".js": "application/javascript; charset=utf-8",
  ".json": "application/json; charset=utf-8",
  ".svg": "image/svg+xml; charset=utf-8",
  ".md": "text/markdown; charset=utf-8"
};

function sendJson(res, status, payload, headers = {}) {
  res.writeHead(status, { "Content-Type": "application/json; charset=utf-8", ...headers });
  res.end(JSON.stringify(payload));
}

function parseCookies(req) {
  return Object.fromEntries((req.headers.cookie || "").split(";").filter(Boolean).map((item) => {
    const [key, ...value] = item.trim().split("=");
    return [key, decodeURIComponent(value.join("="))];
  }));
}

function readBody(req) {
  return new Promise((resolve, reject) => {
    let body = "";
    req.on("data", (chunk) => {
      body += chunk;
      if (body.length > 1_000_000) req.destroy();
    });
    req.on("end", () => {
      try {
        resolve(body ? JSON.parse(body) : {});
      } catch (error) {
        reject(error);
      }
    });
  });
}

function createPasswordHash(password) {
  const iterations = 120000;
  const salt = crypto.randomBytes(16).toString("hex");
  const hash = crypto.pbkdf2Sync(password, salt, iterations, 64, "sha512").toString("hex");
  return `pbkdf2_sha512$${iterations}$${salt}$${hash}`;
}

function verifyPassword(password, storedHash) {
  const [algorithm, iterations, salt, hash] = storedHash.split("$");
  if (algorithm !== "pbkdf2_sha512") return false;
  const calculated = crypto.pbkdf2Sync(password, salt, Number(iterations), 64, "sha512");
  const expected = Buffer.from(hash, "hex");
  return expected.length === calculated.length && crypto.timingSafeEqual(expected, calculated);
}

function publicUser(row) {
  return {
    id: row.id,
    name: row.name,
    surname: row.surname,
    phone: row.phone,
    email: row.email,
    role: row.role,
    createdAt: row.created_at
  };
}

async function getUserBySession(req) {
  const sid = parseCookies(req)[COOKIE_NAME];
  const userId = sid ? sessions.get(sid) : null;
  if (!userId) return null;
  const [rows] = await pool.query("SELECT * FROM users WHERE id = ?", [userId]);
  return rows[0] || null;
}

async function getProfileData(userId) {
  const [requests] = await pool.query("SELECT id, user_id AS userId, date, status, message FROM requests WHERE user_id = ? ORDER BY id DESC", [userId]);
  const [orders] = await pool.query("SELECT id, user_id AS userId, date, status FROM orders WHERE user_id = ? ORDER BY id DESC", [userId]);
  const [favorites] = await pool.query("SELECT id, user_id AS userId, product_id AS productId FROM favorites WHERE user_id = ? ORDER BY id DESC", [userId]);
  return { requests, orders, favorites };
}

async function handleApi(req, res, url) {
  try {
    if (req.method === "GET" && url.pathname === "/api/health") {
      await pool.query("SELECT 1");
      return sendJson(res, 200, { ok: true, database: "mysql" });
    }

    if (req.method === "POST" && url.pathname === "/api/auth/login") {
      const { email, password } = await readBody(req);
      const [rows] = await pool.query("SELECT * FROM users WHERE email = ?", [email]);
      const user = rows[0];
      if (!user || !verifyPassword(password || "", user.password_hash)) return sendJson(res, 401, { message: "Неверный email или пароль" });
      const sid = crypto.randomBytes(32).toString("hex");
      sessions.set(sid, user.id);
      return sendJson(res, 200, { user: publicUser(user) }, { "Set-Cookie": `${COOKIE_NAME}=${sid}; HttpOnly; SameSite=Lax; Path=/; Max-Age=86400` });
    }

    if (req.method === "POST" && url.pathname === "/api/auth/register") {
      const body = await readBody(req);
      if (!body.name || !body.surname || !body.phone || !body.email || !body.password) return sendJson(res, 400, { message: "Заполните все поля" });
      const passwordHash = createPasswordHash(body.password);
      const [result] = await pool.query(
        "INSERT INTO users (name, surname, phone, email, password_hash, role) VALUES (?, ?, ?, ?, ?, 'user')",
        [body.name, body.surname, body.phone, body.email, passwordHash]
      );
      const [rows] = await pool.query("SELECT * FROM users WHERE id = ?", [result.insertId]);
      const sid = crypto.randomBytes(32).toString("hex");
      sessions.set(sid, result.insertId);
      return sendJson(res, 201, { user: publicUser(rows[0]) }, { "Set-Cookie": `${COOKIE_NAME}=${sid}; HttpOnly; SameSite=Lax; Path=/; Max-Age=86400` });
    }

    if (req.method === "POST" && url.pathname === "/api/auth/logout") {
      const sid = parseCookies(req)[COOKIE_NAME];
      if (sid) sessions.delete(sid);
      return sendJson(res, 200, { ok: true }, { "Set-Cookie": `${COOKIE_NAME}=; HttpOnly; SameSite=Lax; Path=/; Max-Age=0` });
    }

    const user = await getUserBySession(req);
    if (!user) return sendJson(res, 401, { message: "Требуется авторизация" });

    if (req.method === "GET" && url.pathname === "/api/auth/me") {
      return sendJson(res, 200, { user: { ...publicUser(user), ...(await getProfileData(user.id)) } });
    }

    if (req.method === "PUT" && url.pathname === "/api/profile") {
      const body = await readBody(req);
      await pool.query("UPDATE users SET name = ?, surname = ?, phone = ?, email = ? WHERE id = ?", [body.name, body.surname, body.phone, body.email, user.id]);
      const [rows] = await pool.query("SELECT * FROM users WHERE id = ?", [user.id]);
      return sendJson(res, 200, { user: { ...publicUser(rows[0]), ...(await getProfileData(user.id)) } });
    }

    if (req.method === "PUT" && url.pathname === "/api/profile/password") {
      const body = await readBody(req);
      if (!body.password || body.password.length < 6) return sendJson(res, 400, { message: "Минимум 6 символов" });
      await pool.query("UPDATE users SET password_hash = ? WHERE id = ?", [createPasswordHash(body.password), user.id]);
      return sendJson(res, 200, { ok: true });
    }

    return sendJson(res, 404, { message: "API route not found" });
  } catch (error) {
    if (error.code === "ER_DUP_ENTRY") return sendJson(res, 409, { message: "Пользователь уже существует" });
    return sendJson(res, 500, { message: "Ошибка сервера", detail: error.message });
  }
}

function serveStatic(req, res, url) {
  const requested = url.pathname === "/" ? "/index.html" : decodeURIComponent(url.pathname);
  const filePath = path.normalize(path.join(ROOT, requested));
  if (!filePath.startsWith(ROOT)) {
    res.writeHead(403);
    return res.end("Forbidden");
  }
  fs.readFile(filePath, (error, content) => {
    if (error) {
      fs.readFile(path.join(ROOT, "404.html"), (notFoundError, notFoundContent) => {
        res.writeHead(404, { "Content-Type": "text/html; charset=utf-8" });
        res.end(notFoundError ? "Not found" : notFoundContent);
      });
      return;
    }
    res.writeHead(200, { "Content-Type": mime[path.extname(filePath)] || "application/octet-stream" });
    res.end(content);
  });
}

const server = http.createServer((req, res) => {
  const url = new URL(req.url, `http://${req.headers.host}`);
  if (url.pathname.startsWith("/api/")) return handleApi(req, res, url);
  return serveStatic(req, res, url);
});

server.listen(PORT, () => {
  console.log(`Demo exam site is running: http://127.0.0.1:${PORT}`);
});
