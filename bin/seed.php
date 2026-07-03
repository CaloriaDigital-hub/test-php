<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';


echo "Seeding test users into database...\n";

// Set of absolutely unique users (16 records - perfect for pagination testing)
$users = [
    ['login' => 'jdoe',        'password' => 'password', 'first_name' => 'John',    'last_name' => 'Doe',       'gender' => 'male',   'birth_date' => '1990-05-15'],
    ['login' => 'asmith',      'password' => 'password', 'first_name' => 'Anna',    'last_name' => 'Smith',     'gender' => 'female', 'birth_date' => '1985-12-01'],
    ['login' => 'bwayne',      'password' => 'password', 'first_name' => 'Bruce',   'last_name' => 'Wayne',     'gender' => 'male',   'birth_date' => '1982-02-19'],
    ['login' => 'ckent',       'password' => 'password', 'first_name' => 'Clark',   'last_name' => 'Kent',      'gender' => 'male',   'birth_date' => '1980-06-18'],
    ['login' => 'dprince',     'password' => 'password', 'first_name' => 'Diana',   'last_name' => 'Prince',    'gender' => 'female', 'birth_date' => '1984-03-22'],
    ['login' => 'pparker',     'password' => 'password', 'first_name' => 'Peter',   'last_name' => 'Parker',    'gender' => 'male',   'birth_date' => '1995-08-10'],
    ['login' => 'tsstark',     'password' => 'password', 'first_name' => 'Tony',    'last_name' => 'Stark',     'gender' => 'male',   'birth_date' => '1970-05-29'],
    ['login' => 'natromanoff', 'password' => 'password', 'first_name' => 'Natasha', 'last_name' => 'Romanoff',  'gender' => 'female', 'birth_date' => '1984-11-22'],
    ['login' => 'bbanner',     'password' => 'password', 'first_name' => 'Bruce',   'last_name' => 'Banner',    'gender' => 'male',   'birth_date' => '1969-12-18'],
    ['login' => 'swilson',     'password' => 'password', 'first_name' => 'Sam',     'last_name' => 'Wilson',    'gender' => 'male',   'birth_date' => '1978-09-23'],
    ['login' => 'wmaximoff',   'password' => 'password', 'first_name' => 'Wanda',   'last_name' => 'Maximoff',  'gender' => 'female', 'birth_date' => '1989-02-10'],
    ['login' => 'ccarter',     'password' => 'password', 'first_name' => 'Peggy',   'last_name' => 'Carter',    'gender' => 'female', 'birth_date' => '1921-04-09'],
    ['login' => 'ssandwich',   'password' => 'password', 'first_name' => 'Steve',   'last_name' => 'Rogers',    'gender' => 'male',   'birth_date' => '1918-07-04'],
    ['login' => 'barryallen',  'password' => 'password', 'first_name' => 'Barry',   'last_name' => 'Allen',     'gender' => 'male',   'birth_date' => '1992-03-14'],
    ['login' => 'dinahlance',  'password' => 'password', 'first_name' => 'Dinah',   'last_name' => 'Lance',     'gender' => 'female', 'birth_date' => '1987-04-10'],
    ['login' => 'haljordan',   'password' => 'password', 'first_name' => 'Hal',     'last_name' => 'Jordan',    'gender' => 'male',   'birth_date' => '1980-02-20']
];

try {
    $db = \App\Core\Database::getInstance();

    // Clear the table before seeding to avoid duplication on repeated runs
    $db->exec("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE users; SET FOREIGN_KEY_CHECKS = 1;");

    $sql = "INSERT INTO users (login, password_hash, first_name, last_name, gender, birth_date) 
            VALUES (:login, :password_hash, :first_name, :last_name, :gender, :birth_date)";
    
    $stmt = $db->prepare($sql);

    foreach ($users as $user) {
        $stmt->execute([
            'login'         => $user['login'],
            'password_hash' => password_hash($user['password'], PASSWORD_BCRYPT), // Hash by standard
            'first_name'    => $user['first_name'],
            'last_name'     => $user['last_name'],
            'gender'        => $user['gender'],
            'birth_date'    => $user['birth_date']
        ]);
    }

    echo "Successfully seeded " . count($users) . " unique users!\n";
} catch (\Throwable $e) {
    echo "Seeding failed: " . $e->getMessage() . "\n";
    exit(1);
}
