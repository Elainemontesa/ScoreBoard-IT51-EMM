CREATE DATABASE IF NOT EXISTS scoreboard;
USE scoreboard;

DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS diary_entries;
DROP TABLE IF EXISTS follows;
DROP TABLE IF EXISTS games;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    display_name VARCHAR(120) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    bio VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    genre VARCHAR(50) NOT NULL,
    developer VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE follows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL,
    following_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE diary_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    game_id INT NOT NULL,
    status ENUM('completed','playing','backlog') DEFAULT 'playing',
    playtime_hours DECIMAL(8,1) DEFAULT 0,
    liked TINYINT(1) DEFAULT 0,
    last_played_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    achievement_notes TEXT,
    UNIQUE KEY uniq_diary_user_game (user_id, game_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    game_id INT NOT NULL,
    rating TINYINT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_review_user_game (user_id, game_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message VARCHAR(255) NOT NULL,
    action_label VARCHAR(50) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (display_name, username, password, bio) VALUES
('Pixel Hollow', 'pixelhollow', '$2y$10$9za8ZSkFk80w2aerj6KqA.ORvQNdP2jEVs0KBlqim1A10cNqG5v7u', 'Action RPG fan, achievement hunter, and review writer.'),
('PixelNomad', 'pixelnomad', '$2y$10$9za8ZSkFk80w2aerj6KqA.ORvQNdP2jEVs0KBlqim1A10cNqG5v7u', 'Explorer of open worlds.'),
('EchoDrift', 'echodrift', '$2y$10$9za8ZSkFk80w2aerj6KqA.ORvQNdP2jEVs0KBlqim1A10cNqG5v7u', 'FPS and co-op player.'),
('LunarByte', 'lunarbyte', '$2y$10$9za8ZSkFk80w2aerj6KqA.ORvQNdP2jEVs0KBlqim1A10cNqG5v7u', 'Story-rich games enjoyer.'),
('NeonClover', 'neonclover', '$2y$10$9za8ZSkFk80w2aerj6KqA.ORvQNdP2jEVs0KBlqim1A10cNqG5v7u', 'Horror and indie addict.'),
('FrostyOrbit', 'frostyorbit', '$2y$10$9za8ZSkFk80w2aerj6KqA.ORvQNdP2jEVs0KBlqim1A10cNqG5v7u', 'Tactical and strategy fan.'),
('CrimsonKite', 'crimsonkite', '$2y$10$9za8ZSkFk80w2aerj6KqA.ORvQNdP2jEVs0KBlqim1A10cNqG5v7u', 'Competitive multiplayer player.');

INSERT INTO games (title, genre, developer, description) VALUES
('Red Dead Redemption 2', 'Action', 'Rockstar Games', 'A sweeping open-world western adventure focused on story, exploration, and immersion.'),
('God of War: Ragnarok', 'RPG', 'Santa Monica Studio', 'A mythic action adventure with cinematic storytelling and hard-hitting combat.'),
('Ghost of Tsushima', 'Action', 'Sucker Punch Productions', 'A samurai epic set in feudal Japan with stealth, swordplay, and exploration.'),
('Apex Legends', 'FPS', 'Respawn Entertainment', 'A fast-paced hero battle royale built around squad synergy and mobility.'),
('Devil May Cry 5', 'Action', 'Capcom', 'A stylish action game known for combo-driven combat and over-the-top spectacle.'),
('Overwatch 2', 'Co-op', 'Blizzard Entertainment', 'A team-based hero shooter with objective play and strong class identities.'),
('Fallout: New Vegas', 'RPG', 'Obsidian Entertainment', 'A post-apocalyptic RPG centered on freedom of choice, factions, and roleplay.'),
('Fallout 4', 'RPG', 'Bethesda Softworks', 'A post-nuclear open-world RPG with crafting, combat, and settlement systems.');

INSERT INTO diary_entries (user_id, game_id, status, playtime_hours, liked, last_played_at, achievement_notes) VALUES
(1, 1, 'completed', 434.0, 1, '2026-01-24 20:00:00', 'Collected legendary outfits, completed most side quests, and cleared multiple challenges.'),
(1, 2, 'completed', 214.0, 1, '2025-11-11 19:30:00', 'Finished story, post-game cleanup, and high-level boss encounters.'),
(1, 3, 'playing', 67.0, 1, '2026-02-01 21:15:00', 'Liberated camps, duels completed, and mythic quest progress ongoing.'),
(1, 4, 'playing', 335.8, 0, '2026-01-16 23:45:00', 'Season challenges and ranked progression tracked.'),
(1, 5, 'completed', 105.0, 1, '2025-08-23 13:00:00', 'S-rank attempts and combo mastery on multiple difficulties.'),
(1, 6, 'playing', 704.8, 0, '2026-01-16 22:10:00', 'Competitive matches, role queue stats, and weekly objectives completed.'),
(1, 7, 'backlog', 15.0, 1, '2026-01-10 16:20:00', 'Early NCR quests done; more faction routes left to explore.'),
(1, 8, 'backlog', 12.0, 0, '2026-01-08 17:50:00', 'Settlement building tutorial completed; main quest still early.');

INSERT INTO reviews (user_id, game_id, rating, content, created_at) VALUES
(1, 1, 10, 'Red Dead Redemption 2 delivers one of the most immersive open worlds I have played. The pacing is slow at times, but the emotional weight and attention to detail are unmatched.', '2026-01-22 10:00:00'),
(1, 2, 9, 'God of War: Ragnarok feels polished, intense, and heartfelt. Combat variety is strong and the presentation is excellent from start to finish.', '2025-11-12 09:00:00'),
(1, 7, 10, 'Fallout: New Vegas still stands out because of player freedom and world-building. Every major decision feels meaningful and the role-playing depth is fantastic.', '2026-01-15 12:00:00');

INSERT INTO follows (follower_id, following_id) VALUES
(2, 1), (3, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6);

INSERT INTO notifications (user_id, message, action_label, created_at) VALUES
(1, 'ELAINE has started following you.', 'Follow', '2026-03-16 09:00:00'),
(1, 'James, Yono, and 3 others liked your post.', NULL, '2026-03-16 10:00:00'),
(1, 'James, Yono, and 3 others started following you.', 'Follow', '2026-03-15 18:00:00'),
(1, 'ELAINE reposted your post.', NULL, '2026-03-14 13:00:00'),
(1, 'You have 10 new followers. Check them out!', 'Follow', '2026-03-13 11:00:00'),
(1, 'INAKI, ROLAN, and 6 others reposted your post.', NULL, '2026-03-02 11:00:00'),
(1, 'KLEIN, BILLIE, and 7 others liked your post.', NULL, '2026-02-25 16:00:00');
