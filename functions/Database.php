<?php
class Database
{
    public $pdo = null;
    private $dbFile;
    
    public function __construct(string $dbFile = __DIR__ . '/../SQLite/Mydatabase/Navyrost.db')
    {
        $this->dbFile = $dbFile;
        
        try {
           
            $isNewDatabase = !file_exists($this->dbFile);
            
            // Підключаємось до БД
            $this->pdo = new PDO('sqlite:' . $this->dbFile);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec('PRAGMA busy_timeout = 5000;');
            $this->pdo->exec('PRAGMA journal_mode = WAL;');
                       
        } catch (PDOException $e) {
            // Видаляємо частково створений файл БД при помилці
            if (file_exists($this->dbFile)) {
                unlink($this->dbFile);
            }
            die("Неможливо створити базу даних: " . $e->getMessage());
        }
    }
    
    public function connect(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = new PDO('sqlite:' . $this->dbFile);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec('PRAGMA busy_timeout = 5000;');
            $this->pdo->exec('PRAGMA journal_mode = WAL;');
        }
        return $this->pdo;
    }
    
    public function execQuery(string $sql, array $params = [], bool $fetchAll = true)
    {
        $pdo = $this->connect();
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            if (stripos(trim($sql), 'SELECT') === 0 && $fetchAll) {
                return $stmt->fetchAll();
            }

            return $stmt;
        } catch (PDOException $e) {
            $errorCode = $pdo->errorCode();
            $errorInfo = $pdo->errorInfo();
            throw new Exception(
                'Помилка виконання запиту: ' . $e->getMessage() 
                . " [Код помилки: $errorCode, Деталі: " . json_encode($errorInfo) . "]"
            );
        }
    }
    
    public function disconnect()
    {
        $this->pdo = null;
    }

    public function getLastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function fetchAll($sql, $params = []) 
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}