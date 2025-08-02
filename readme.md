# WallySky 🎨🖼️

**WallySky** is a minimalist wallpaper-sharing platform where artists can upload original artworks and users can browse and download wallpapers. It’s built with a PHP backend and a clean JavaScript + SCSS frontend bundled via Webpack. Python Flask is optionally used for backend experimentation.

---

## 🛠️ Tech Stack

| Layer       | Technology                             |
|-------------|-----------------------------------------|
| Frontend    | HTML, SCSS, Vanilla JS (ES6+) + Webpack |
| Backend     | PHP (Primary), Python Flask (Optional)  |
| Database    | MySQL                                   |
| Assets      | Wallpapers stored in `img/` folder      |

---

## 🚩 Features

- Artists upload wallpapers with metadata (title, category, tags).
- Public gallery shows all submitted wallpapers.
- Users can download wallpapers in original quality.
- Tag-based filtering and search (chip selector).
- Clean UI with zero frontend framework dependency.

---

## 📁 Project Structure

```bash
WallySky/
├── Dlngo_tst/            # Python Backend
├── admin/                # Admin dashboard (if any)
├── api/                  # PHP APIs for upload, fetch, download
├── db/                   # SQL schema or DB interaction files
├── dist/                 # Webpack bundled output (CSS, JS)
├── img/                  # Uploaded wallpapers
├── src/                  # JS and SCSS source files
│   ├── js/               # JavaScript modules
│   └── styles/           # SCSS stylesheets
├── .gitignore
├── package.json
├── package-lock.json
├── webpack.config.cjs
└── README.md
```

---

## ⚙️ Setup Instructions

### 🧪 Requirements

- PHP 8.x with MySQL support
- MySQL 8.x
- Node.js + npm (for Webpack)
- Optional: Python 3.10+ (if using Flask)

---

### 🚀 Getting Started

#### 1. Clone the Repository

```bash
git clone https://github.com/GautamMakadia/WallySky.git
cd WallySky
```

#### 2. Install Frontend Dependencies

```bash
npm install
npm run build     # Production build (outputs to /dist)
npm run dev       # Development server with watch mode
```

#### 3. Setup PHP Backend

- Configure your database in `/db/config.php` or equivalent.
- Run `php -S localhost:8000 -t .` to serve via PHP built-in server.

#### 4. Setup MySQL Database

- Import schema from `/db/schema.sql` (if available).
- Ensure DB credentials are correctly set in backend files.

---

## 🧪 PHP API Endpoints

| Method | Endpoint           | Description                 |
|--------|--------------------|-----------------------------|
| POST   | `/api/upload.php`  | Upload a new wallpaper      |
| GET    | `/api/fetch.php`   | Get all wallpapers          |
| GET    | `/api/download.php?id=XYZ` | Download wallpaper by ID |

---

## 🧑‍💻 Developer Notes

- JS modules and SCSS files are located in `src/`, compiled into `dist/`.
- PHP is used for handling uploads, DB actions, and image downloads.
- Flask backend (optional) replicates PHP logic for cross-platform support/testing.


---

## 🙌 Author

Made with ❤️ by [Gautam Makadia](https://github.com/GautamMakadia)

---

## ⭐ Support the Project

If you found this helpful, please consider giving it a ⭐ on GitHub!
