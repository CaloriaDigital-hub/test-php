-- ============================================================
-- Full database dump: structure + seed data
-- ============================================================
-- Default admin credentials:  username=admin  password=secretpassword
-- Default user password for all seed users: password
-- ============================================================

CREATE TABLE IF NOT EXISTS users (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `login` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(255) NOT NULL,
    `last_name` VARCHAR(255) NOT NULL,
    `gender` ENUM('male', 'female') NOT NULL,
    `birth_date` DATE NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 16 seed users, same dataset as bin/seed.php (password: "password")
INSERT INTO users (login, password_hash, first_name, last_name, gender, birth_date) VALUES
('jdoe',        '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'John',    'Doe',      'male',   '1990-05-15'),
('asmith',      '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'Anna',    'Smith',    'female', '1985-12-01'),
('bwayne',      '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'Bruce',   'Wayne',    'male',   '1982-02-19'),
('ckent',       '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'Clark',   'Kent',     'male',   '1980-06-18'),
('dprince',     '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'Diana',   'Prince',   'female', '1984-03-22'),
('pparker',     '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'Peter',   'Parker',   'male',   '1995-08-10'),
('tsstark',     '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'Tony',    'Stark',    'male',   '1970-05-29'),
('natromanoff', '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'Natasha', 'Romanoff', 'female', '1984-11-22'),
('bbanner',     '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'Bruce',   'Banner',   'male',   '1969-12-18'),
('swilson',     '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'Sam',     'Wilson',   'male',   '1978-09-23'),
('wmaximoff',   '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'Wanda',   'Maximoff', 'female', '1989-02-10'),
('ccarter',     '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'Peggy',   'Carter',   'female', '1921-04-09'),
('ssandwich',   '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'Steve',   'Rogers',   'male',   '1918-07-04'),
('barryallen',  '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'Barry',   'Allen',    'male',   '1992-03-14'),
('dinahlance',  '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'Dinah',   'Lance',    'female', '1987-04-10'),
('haljordan',   '$2y$10$4m.4BRcLo9.YhLonfeoDEuS22YrK3Ewc6B2loMrRJw212RM9blV46', 'Hal',     'Jordan',   'male',   '1980-02-20');

-- Admin account (password: secretpassword)
INSERT INTO admins (username, password_hash) VALUES
('admin', '$2y$10$5JWQH0HuRwFjbSah.BhNje0G0pkDNiIC4gjbtp8376.SyLyuFAa4G');
