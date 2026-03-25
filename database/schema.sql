-- =====================================================
-- Database: Chemistry Adventure Game
-- =====================================================

CREATE DATABASE IF NOT EXISTS chemistry_adventure;
USE chemistry_adventure;

-- Drop existing tables to reset
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS level_results;
DROP TABLE IF EXISTS player_progress;
DROP TABLE IF EXISTS leaderboard;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS levels;

-- =====================================================
-- Table: users
-- =====================================================
CREATE TABLE users (
    user_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    auth_token TEXT NULL
);

-- =====================================================
-- Table: player_progress
-- =====================================================
CREATE TABLE player_progress (
    progress_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,

    total_score INT DEFAULT 0,
    total_stars INT DEFAULT 0,

    -- Stored as JSON arrays: [1,2,3]
    unlocked_map_ids JSON,
    unlocked_level_ids JSON,

    last_unlocked_map_id INT DEFAULT 0,

    CONSTRAINT fk_progress_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
);

-- =====================================================
-- Table: level_results
-- =====================================================
CREATE TABLE level_results (
    result_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,

    map_id INT NOT NULL,
    level_id INT NOT NULL,

    score INT DEFAULT 0,
    stars INT DEFAULT 0,
    remaining_time FLOAT DEFAULT 0,
    bonus_score INT DEFAULT 0,

    is_completed BOOLEAN DEFAULT FALSE,


    CONSTRAINT fk_result_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
);

-- =====================================================
-- Indexes (Performance Optimization)
-- =====================================================

-- Faster lookup by token
CREATE INDEX idx_users_token ON users(auth_token(100));

-- Faster progress lookup
CREATE INDEX idx_progress_user ON player_progress(user_id);

-- Leaderboard optimization
CREATE INDEX idx_results_score ON level_results(score);

-- Query optimization for user history
CREATE INDEX idx_results_user_level ON level_results(user_id, level_id);

-- =====================================================
-- Initial Data (Optional)
-- =====================================================

-- Example default progress JSON
-- (used when creating new player)
-- unlocked_map_ids: [1]
-- unlocked_level_ids: [1]

SET FOREIGN_KEY_CHECKS = 1;