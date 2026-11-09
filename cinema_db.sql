CREATE DATABASE IF NOT EXISTS cinema_db;
USE cinema_db;

-- 1. Admin Management
-- Used for the high-security SBA Admin Dashboard
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL -- Increased length for hashed passwords
);

-- 2. Audience / Club Members
-- Stores user profiles for the screening system
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Movie Library
-- Central repository for all cinematic content
CREATE TABLE movies (
    Movie_id INT AUTO_INCREMENT PRIMARY KEY,
    Movie_Name VARCHAR(255) NOT NULL,
    type VARCHAR(100), -- Genre
    RunTime VARCHAR(50),
    Description TEXT,
    Release_date DATE,
    poster VARCHAR(255),
    Actor VARCHAR(100),
    ActorImg VARCHAR(255),
    Actress VARCHAR(100),
    ActressImg VARCHAR(255),
    Director VARCHAR(100),
    DirectorImg VARCHAR(255),
    trailer VARCHAR(255), -- YouTube Embed ID or Link
    wiki VARCHAR(255)
);

-- 4. Infrastructure (Theatres)
-- Defined as physical halls within the SBA network
CREATE TABLE theatre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Theatre_Name VARCHAR(100) NOT NULL,
    Location VARCHAR(255),
    Seats INT DEFAULT 0
);

-- 5. Show Schedules (Timings)
-- Links halls to specific time slots
CREATE TABLE timings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    showtime VARCHAR(50) NOT NULL, -- Stored as string for display flexibility (e.g., "10:30 AM")
    show_date DATE, -- The calendar date this screening takes place
    Theatre_Name VARCHAR(100),
    ticket_rate_Gold INT DEFAULT 0,
    ticket_rate_Silver INT DEFAULT 0,
    seats_remaining INT DEFAULT 0
);

-- 6. Reservation Records (Bookings)
-- The audit trail for every ticket generated
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    movie VARCHAR(255),
    theatre VARCHAR(100),
    booking_date DATE,
    showtime VARCHAR(50),
    seat_class VARCHAR(20), -- Gold or Silver
    price INT,
    booking_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- SEED DATA (Default Setup)
-- --------------------------------------------------------

-- Default Admin Login (username: admin, password: 123)
INSERT INTO admin (username, password) VALUES ('admin', '123');

-- Sample Cinema Hall
INSERT INTO theatre (Theatre_Name, Location, Seats) 
VALUES ('SBA Grand Hall', 'Cyber City Center', 150);

-- Sample Movie Entry
INSERT INTO movies (Movie_Name, type, RunTime, Description, Release_date, poster) 
VALUES ('The Batman', 'Action/Thriller', '176 min', 'In his second year of fighting crime, Batman uncovers corruption in Gotham City that connects to his own family.', '2026-03-04', 'batman.jpg');

-- Sample Show Slot
INSERT INTO timings (showtime, show_date, Theatre_Name, ticket_rate_Gold, ticket_rate_Silver, seats_remaining)
VALUES ('06:30 PM', CURDATE(), 'SBA Grand Hall', 15, 10, 150);




