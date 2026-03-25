# GitHub Issues for Backend Implementation (Updated - Simple Token Auth)

## Issue 1: Set Up Backend Project Structure
**Description**  
Prepare the PHP backend project structure using the service approach. Organize the folders for controllers, services, repositories, models, middleware, routes, config, and core utilities.

**Task**  
- Create folder structure (Controllers, Services, Repositories, Models, Middleware, Core, Routes, Config)
- Set up public entry point
- Configure autoloading
- Add environment configuration
- Prepare routing system
- Configure `.htaccess` to redirect all requests to `/public/index.php`
- Create `.htaccess` inside `/public` to route requests to `index.php`
- Ensure Apache `mod_rewrite` is enabled
- Test base URL access (`localhost/` loads correctly)
- Run server locally

**Goal**  
Establish backend foundation and routing system.

---

## Issue 2: Create Database Schema
**Description**  
Create database structure for accounts, player progress, and level results.

**Task**  
- Create `accounts` table (include `auth_token`)
- Create `player_progress` table
- Create `level_results` table
- Define PK and FK
- Add unique constraints (username, email)
- Test schema

**Goal**  
Provide database structure.

---

## Issue 3: Build Core Backend Utilities
**Description**  
Implement reusable backend utilities.

**Task**  
- Database connection class
- Request handler
- JSON response helper
- Validation helper
- Error handler
- Token helper (generateToken function)
- Config loader
- Test utilities

**Goal**  
Provide reusable backend foundation.

---

## Issue 4: Implement Authentication Module (Simple Token)
**Description**  
Implement register and login using simple token authentication.

**Task**  
- Create `AuthController`
- Create `AuthService`
- Create `AccountRepository`
- Implement register logic
- Implement login logic
- Hash password using `password_hash`
- Verify password using `password_verify`
- Generate token using `random_bytes`
- Store token in database
- Return token in response
- Test register and login

**Goal**  
Enable authentication with token generation.

---

## Issue 5: Initialize Default Player Data After Registration
**Description**  
Create default progress after account registration.

**Task**  
- Insert player_progress record
- Set default score and stars
- Set default unlocked maps (JSON)
- Set default unlocked levels (JSON)
- Prevent duplicate initialization
- Test new account flow

**Goal**  
Ensure player starts with valid data.

---

## Issue 6: Implement Player Progress Module
**Description**  
Handle retrieving and updating player progress.

**Task**  
- Create `ProgressController`
- Create `ProgressService`
- Create `PlayerProgressRepository`
- Implement get progress endpoint
- Implement update progress logic
- Handle JSON fields (unlocked maps/levels)
- Test progress retrieval

**Goal**  
Manage player progress.

---

## Issue 7: Implement Level Result Module
**Description**  
Store level results submitted from Unity.

**Task**  
- Create `LevelResultController`
- Create `LevelResultService`
- Create `LevelResultRepository`
- Validate request data
- Insert level result into database
- Handle invalid input
- Test submission

**Goal**  
Store gameplay results.

---

## Issue 8: Implement Progression Update Logic
**Description**  
Update progress after level completion.

**Task**  
- Update total score
- Update total stars
- Update unlocked levels (JSON)
- Update unlocked maps (JSON)
- Avoid duplicate entries
- Return updated progress
- Test progression flow

**Goal**  
Maintain progression logic.

---

## Issue 9: Implement Leaderboard Module
**Description**  
Provide leaderboard based on player scores.

**Task**  
- Create `LeaderboardController`
- Create `LeaderboardService`
- Create ranking query
- Get top players
- Format response
- Test leaderboard

**Goal**  
Provide ranking system.

---

## Issue 10: Define API Endpoints
**Description**  
Define API routes.

**Task**  
- POST /register
- POST /login
- GET /progress
- POST /level-results
- GET /leaderboard
- Map routes to controllers
- Test routing

**Goal**  
Expose backend API.

---

## Issue 11: Add Authentication Middleware (Token-Based)
**Description**  
Protect routes using simple token validation.

**Task**  
- Create `AuthMiddleware`
- Extract token from header
- Validate token against database
- Reject invalid requests
- Attach account data to request
- Apply middleware to protected routes
- Test protected endpoints

**Goal**  
Secure API endpoints.

---

## Issue 12: Test Backend Modules
**Description**  
Test all backend features.

**Task**  
- Test register/login
- Test token validation
- Test progress retrieval
- Test level result submission
- Test progression update
- Test leaderboard
- Test invalid requests
- Fix bugs

**Goal**  
Ensure backend works correctly.

---

## Issue 13: Test Unity Integration
**Description**  
Test communication between Unity and backend.

**Task**  
- Send login request from Unity
- Store token in Unity
- Send authenticated requests
- Test progress loading
- Test result submission
- Verify response format
- Fix integration issues

**Goal**  
Ensure Unity and backend integration.

---

## Issue 14: Refactor and Documentation
**Description**  
Clean and finalize backend.

**Task**  
- Remove duplicate logic
- Improve naming consistency
- Clean unused code
- Document API endpoints
- Document database structure
- Final review

**Goal**  
Prepare backend for final submission.