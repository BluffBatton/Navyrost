<?php
class NewDatabase
{
    private $pdo;
    private $dbFile;

    public function __construct(string $dbFile = __DIR__ . '/../SQLite/sqlite3/testDB.db')
    {
        $this->dbFile = $dbFile;
        $isNewDatabase = !file_exists($this->dbFile);

        try {
            // Спроба створити/відкрити БД
            $this->pdo = new PDO('sqlite:' . $this->dbFile);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec('PRAGMA journal_mode = WAL;');

            if ($isNewDatabase) {
                $this->runInitialization(); // Запуск транзакції для нової БД
            }

        } catch (PDOException $e) {
            $this->handleDatabaseError($e);
        }
    }

    private function runInitialization()
    {
        try {
            $this->pdo->beginTransaction();

            $this->createTables();

            $this->seedData();
            
            $this->pdo->commit();

        } catch (Exception $e) {
            $this->pdo->rollBack();
            $this->handleDatabaseError($e);
        }
    }

    private function createTables()
    {
        $schema = [
            "CREATE TABLE Users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                email TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
            "CREATE TABLE Orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                amount REAL NOT NULL,
                FOREIGN KEY(user_id) REFERENCES Users(id)
            )"
        ];

        foreach ($schema as $query) {
            $this->pdo->exec($query);
        }
    }

    private function seedData()
    {
        $this->pdo->exec("INSERT INTO Users (username, email) VALUES 
            ('john_doe', 'john@example.com'),
            ('alice', 'alice@company.ua')");
        
        $this->pdo->exec("INSERT INTO Orders (user_id, amount) VALUES
            (1, 100.50),
            (2, 299.99)");
    }

    private function handleDatabaseError(Exception $e)
    {

        if (file_exists($this->dbFile)) {
            unlink($this->dbFile);
        }
        
        die("ПОМИЛКА СТВОРЕННЯ БАЗИ ДАНИХ: " . $e->getMessage());
    }

    public function getData(string $table)
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM $table");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Помилка отримання даних: " . $e->getMessage());
        }
    }
}