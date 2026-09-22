# Modern Corporate Ticketing & Real-Time Communication System

A modern corporate ticketing desk and real-time department coordination system built with **Laravel 11**, **Livewire 3**, and **Tailwind CSS**. Designed with an eye-pleasing **Claymorphism** 3D aesthetic, soft pastel pink accents, and a dual-pane split-screen console.

---

## ✨ Features

- **Split-Screen Console**:
  - **Left Panel (Ticket Dispatch Form)**: Create service requests with Title, Sender Department, Target Department, Category (IT, HR, Maintenance), Description, Selectable Priority Badges (Low, Medium, High, Critical), and Status.
  - **Right Panel (Real-Time Communication Livewire Desk)**: Dynamic chat attached directly to the active ticket, simulated VoIP voice call action, auto-polling updates, and distinct sender/receiver claymorphic bubble styles.
- **Active Ticket Queue & Multi-Filter**:
  - Live search and filter by Status (`Pending`, `Open`, `In Progress`, `Resolved`) and Target Department.
  - Instant ticket selection to switch communication channels without full page reloads.
- **Header & Sidebar Navigation**:
  - Top header with company branding, user profile card with department indicator, and an active alert bell with live ping indicator.
  - Sidebar with quick links for *Dashboard*, *My Tickets*, *Dept Tickets*, *New Request*, and *Team Chat*.
- **Claymorphism & Soft Pastel Design**:
  - Custom soft 3D clay-effect cards (`.clay-card`, `.clay-inset`, `.clay-button-pink`).
  - Eye-friendly `bg-slate-50` / `#f8f9fa` backdrop with soft pastel pink (`#ffb6c1` / `bg-pink-300`) interactive CTAs.
- **Multi-Role / User Identity Switcher**:
  - Quick perspective switcher to simulate interactions between IT Specialists, HR Personnel, and Facility Technicians.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 11.x (PHP 8.3+)
- **Reactive Frontend**: Laravel Livewire 3
- **CSS Framework**: Tailwind CSS (with custom Claymorphism tokens)
- **Database**: MySQL (Local Laragon environment)
- **Build Tool**: Vite 6+

---

## 🚀 Getting Started

### 1. Prerequisites
- PHP 8.3 or higher (with `pdo_mysql` extension)
- Composer
- Node.js (v18+) and npm
- MySQL Server (e.g. via Laragon or XAMPP)

### 2. Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/kevinamay/Corporate-Ticketing-Communication-System.git
   cd Corporate-Ticketing-Communication-System
   ```

2. **Install PHP and Node dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment:**
   Ensure your `.env` is configured with your MySQL database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ticketing_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations & Seed Sample Corporate Data:**
   ```bash
   php artisan migrate --seed
   ```

6. **Build Frontend Assets:**
   ```bash
   npm run build
   # Or for development hot reloading:
   # npm run dev
   ```

7. **Serve the Application:**
   ```bash
   php artisan serve
   ```
   Open `http://127.0.0.1:8000` in your web browser.

---

## 🧪 Testing

Run PHPUnit tests:
```bash
php artisan test
```

---

## 📄 License
This project is open-sourced software licensed under the [MIT license](LICENSE).
