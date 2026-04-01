# Chemistry Adventure Game Server

A robust, scalable PHP backend for the Chemistry Adventure game, built with a custom Service-Repository architecture.

## 🚀 Features

- **User Authentication**: Secure registration and login with token-based session management.
- **Player Progress**: Real-time tracking of unlocked maps and levels.
- **Level Results**: Performance tracking (scores, stars, time) with high-score persistence.
- **Leaderboards**: Global and level-specific rankings.
- **Security**: Auth middleware protecting sensitive endpoints.
- **Automated Testing**: Comprehensive test suite covering all core modules.

## 🛠️ Tech Stack

- **Language**: PHP 8.x
- **Database**: MySQL (optimized with indexed performance tracking)
- **Architecture**: Service-Repository Pattern
- **Dev Environment**: Laragon / Composer

## 📦 Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/ManuelYosia/chemistry-adventure-game-server.git
   cd chemistry-adventure-game-server
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment**:
   - Copy `.env.example` to `.env`.
   - Update `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` for your local MySQL instance.

4. **Run Migrations**:
   ```bash
   php database/migrate.php
   ```

## 🔌 API Endpoints

### Authentication
- `POST /register`: Create a new player account.
- `POST /login`: Authenticate and receive an `auth_token`.

### Progression & Results
- `GET /user`: Retrieve authenticated user profile and progress (Requires Auth).
- `GET /progress`: Retrieve current player progress (Requires Auth).
- `POST /progress/update`: Manually update unlocked maps/levels (Requires Auth).
- `POST /level-result/save`: Save level performance and auto-update progress (Requires Auth).
- `GET /user/results`: Fetch level history for the authenticated user (Requires Auth).

### Leaderboards
- `GET /leaderboard/global`: Fetch top performers game-wide.
- `GET /leaderboard/level`: Fetch top performers for a specific level (`map_id`, `level_id`).

### Map-Specific Stats
- `GET /level-result/map-stars`: Get total stars earned in a map (Requires Auth, `map_id` query param).
  - **Returns**: `{"status":"success", "data":{"total_stars": 12}}`
- `GET /level-result/map`: Get performance details for all levels in a map (Requires Auth, `map_id` query param).
  - **Returns**: `{"status":"success", "data": [{"result_id":1, "map_id":1, "level_id":1, "stars":3, ...}]}`

## 🧪 Testing

Run the full verification suite to ensure all systems are operational:
```bash
php tests/RunAllTests.php
```

## 📄 License
This project is licensed under the MIT License.
