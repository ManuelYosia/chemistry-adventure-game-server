# Chemistry Adventure Game Server

## Project Overview
A robust, scalable PHP backend for the **Chemistry Adventure** game. This server provides the RESTful API that handles user authentication, player progression, map and level unlocks, level result tracking (scores, stars), and competitive leaderboards (both global and level-specific).

## System Architecture
The application is built using a custom PHP framework that strictly adheres to the **Service-Repository Pattern**.
- **Language**: PHP 8.x
- **Database**: MySQL
- **Routing**: Centralized routing (`App\Core\Router`) configured in `app/Routes/api.php` handles incoming HTTP requests.
- **Controllers**: Located in `app/Controllers`, they validate incoming requests, handle input, and format JSON responses (`App\Core\Response`).
- **Services**: Located in `app/Services`, representing the core business logic (e.g., `LeaderboardService`, `AuthService`).
- **Middleware**: Used for protecting endpoints (e.g., `AuthMiddleware` verifies user tokens before requests reach controllers).
- **Responses**: Standardized JSON responses for both success and error states.

## Features
- **User Authentication**: Secure user registration and login with token-based session management.
- **Player Progress Tracking**: Real-time tracking of highest unlocked maps, levels, and aggregated scores/stars.
- **Level Results Persistence**: Track individual level performances including scores, stars, and completion status.
- **Leaderboards**: Support for both game-wide (Global) leaderboards and granular (per-level) high-score rankings.
- **Security**: Token-based authentication middleware protects sensitive player routes.
- **Automated Testing**: A suite of custom tests (`tests/RunAllTests.php`) validates core functionality.

## API Endpoints

*Note: All endpoints returning a successful response follow the format: `{"status": "success", "data": {...}, "message": "..."}`. Errors follow: `{"status": "error", "message": "..."}`.*

### 1. Authentication

- **`POST /register`**
  - **Description**: Create a new player account.
  - **Request Body**: `{"username": "player1", "email": "player@test.com", "password": "password123"}`
  - **Response**: `{"status": "success", "data": {"user_id": 1}, "message": "User registered successfully."}`

- **`POST /login`**
  - **Description**: Authenticate and receive an auth token.
  - **Request Body**: `{"email": "player@test.com", "password": "password123"}`
  - **Response**: `{"status": "success", "data": {"token": "...", "user_id": 1, ...}, "message": "Login successful."}`

### 2. User & Progress (Requires Auth Token)

- **`GET /user`**
  - **Description**: Retrieve authenticated user profile and summarized progress.
  - **Request Body**: None (Relies on token).
  - **Response**: `{"status": "success", "data": {"user_id": 1, "username": "player1", "email": "player@test.com", "highest_unlocked_map": 2, "highest_unlocked_level": 5, "total_score": 1500, "total_stars": 20}, "message": "User profile retrieved."}`

- **`GET /progress`**
  - **Description**: Retrieve current player progression details.
  - **Request Body**: None.
  - **Response**: `{"status": "success", "data": {"highest_unlocked_map_id": 2, "highest_unlocked_level_id": 5, ...}, "message": "Progress fetched successfully."}`

- **`POST /progress/update`**
  - **Description**: Manually update unlocked maps/levels.
  - **Request Body**: `{"highest_unlocked_map_id": 3, "highest_unlocked_level_id": 1}`
  - **Response**: `{"status": "success", "data": {...new_progress...}, "message": "Progress updated successfully."}`

### 3. Level Results (Requires Auth Token)

- **`POST /level-result/save`**
  - **Description**: Save level performance and auto-update overall progress.
  - **Request Body**: `{"map_id": 1, "level_id": 1, "score": 250, "stars": 3, "is_completed": true}`
  - **Response**: `{"status": "success", "data": [], "message": "Level result saved successfully."}`

- **`GET /user/results`**
  - **Description**: Fetch full level history for the authenticated user.
  - **Request Body**: None.
  - **Response**: `{"status": "success", "data": [{"map_id": 1, "level_id": 1, "score": 250, "stars": 3}, ...], "message": "Level results fetched successfully."}`

- **`GET /level-result/map-stars`**
  - **Description**: Get total stars earned across all levels for all maps played by the user.
  - **Request Body / QueryParams**: None required (returns all maps).
  - **Response**: `{"status": "success", "data": [{"map_id": 1, "total_stars": 12}, {"map_id": 2, "total_stars": 8}], "message": "Total stars for all maps fetched successfully."}`

- **`GET /level-result/map`**
  - **Description**: Get performance details for all individual levels in a map.
  - **Request Body / QueryParams**: `{"map_id": 1}`
  - **Response**: `{"status": "success", "data": [{"result_id": 1, "map_id": 1, "level_id": 1, "stars": 3, ...}], "message": "Level results for map 1 fetched successfully."}`

### 4. Leaderboards

- **`GET /leaderboard/global`**
  - **Description**: Fetch top players game-wide by total score.
  - **Request Body**: `{"limit": 10}` (Optional)
  - **Response**: `{"status": "success", "data": [{"username": "player1", "total_score": 5000}, ...], "message": "Global leaderboard fetched successfully."}`

- **`GET /leaderboard/level`**
  - **Description**: Fetch top performers for a specific level.
  - **Request Body**: `{"map_id": 1, "level_id": 1, "limit": 10}` (map_id and level_id are required).
  - **Response**: `{"status": "success", "data": [{"username": "player1", "score": 250}, ...], "message": "Level leaderboard fetched successfully."}`

## Installation

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

## Testing

Run the full verification suite to ensure all systems are operational:
```bash
php tests/RunAllTests.php
```

## License
This project is licensed under the MIT License.
