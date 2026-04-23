-- =====================================================
-- Dummy Data Seed: 15 Players
-- =====================================================

USE chemistry_adventure;

-- Disable checks for bulk insert
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Insert 15 Users
-- Password for all is 'password123' (hashed)
INSERT INTO users (username, email, password_hash) VALUES
('proton_warrior', 'proton@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('electron_queen', 'electron@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('neutron_master', 'neutron@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('molecule_maker', 'molecule@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('atom_smasher', 'atom@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('catalyst_king', 'catalyst@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('bond_breaker', 'bond@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('element_hunter', 'element@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('reaction_pro', 'reaction@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('chem_wizard', 'wizard@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('lab_rat', 'lab@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('alkali_knight', 'alkali@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('noble_gas', 'noble@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('oxide_ranger', 'oxide@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('ph_balancer', 'ph@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- 2. Insert Player Progress for each of the 15 users
-- We assume user_id 1 to 15 are the newly created ones.
-- In a real scenario, we might need to fetch IDs, but for a fresh seed this works.
INSERT INTO player_progress (user_id, highest_unlocked_map_id, highest_unlocked_level_id)
SELECT user_id, FLOOR(1 + RAND() * 3), FLOOR(1 + RAND() * 10)
FROM users
ORDER BY user_id DESC
LIMIT 15;

-- 3. Insert Level Results (at least one for each user)
INSERT INTO level_results (user_id, map_id, level_id, score, stars, remaining_time, bonus_score, is_completed)
SELECT 
    user_id, 
    1, -- Map 1
    1, -- Level 1
    FLOOR(500 + RAND() * 1000), 
    FLOOR(1 + RAND() * 3), 
    RAND() * 60, 
    FLOOR(RAND() * 200), 
    TRUE
FROM users
ORDER BY user_id DESC
LIMIT 15;

-- Add a second level result for some users
INSERT INTO level_results (user_id, map_id, level_id, score, stars, remaining_time, bonus_score, is_completed)
SELECT 
    user_id, 
    1, 
    2, 
    FLOOR(800 + RAND() * 1200), 
    FLOOR(1 + RAND() * 3), 
    RAND() * 45, 
    FLOOR(RAND() * 300), 
    TRUE
FROM users
WHERE user_id % 2 = 0
ORDER BY user_id DESC
LIMIT 7;

SET FOREIGN_KEY_CHECKS = 1;
