<?php
class PostgresAdapter {
    private $conn;

    public function connect() {
        $this->conn = pg_connect("host=localhost dbname=Navyrost user=postgres password=YkGqm5N9");
        if (!$this->conn) {
            throw new Exception("PostgreSQL connection failed: " . pg_last_error());
        }
    }
    
    public function displayUsersAsHtml() {
        try {
            $this->connect();
            $query = "SELECT * FROM users";
            $result = pg_query($this->conn, $query);
            
            if (!$result) {
                return "<div class='alert alert-danger'>Помилка запиту до PostgreSQL: " . pg_last_error($this->conn) . "</div>";
            }

            $rows = [];
            while ($row = pg_fetch_assoc($result)) {
                $rows[] = $row;
            }

            if (empty($rows)) {
                return "<div class='alert alert-warning'>Таблиця users пуста або не існує</div>";
            }

            $html = '<table class="table table-bordered"><thead><tr>';
            $headers = array_keys($rows[0]);
            foreach ($headers as $header) {
                $html .= "<th>" . htmlspecialchars($header) . "</th>";
            }
            $html .= "</tr></thead><tbody>";

            foreach ($rows as $row) {
                $html .= "<tr>";
                foreach ($row as $value) {
                    $html .= "<td>" . htmlspecialchars($value) . "</td>";
                }
                $html .= "</tr>";
            }

            $html .= "</tbody></table>";
            return $html;
            
        } catch (Exception $e) {
            return "<div class='alert alert-danger'>Помилка PostgreSQL: " . $e->getMessage() . "</div>";
        }
    }
}