<?php
class MysqlAdapter {
    private $conn;

    public function connect() {
        $this->conn = new mysqli("localhost", "root", "YkGqm5N9", "navyrost");
        if ($this->conn->connect_error) {
            throw new Exception("MySQL connection failed: " . $this->conn->connect_error);
        }
    }

    public function displayProductsAsHtml() {
        try {
            $this->connect();
            $query = "SELECT * FROM products";
            $result = $this->conn->query($query);
            
            if (!$result) {
                return "<div class='alert alert-danger'>Помилка запиту до MySQL: " . $this->conn->error . "</div>";
            }

            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }

            if (empty($rows)) {
                return "<div class='alert alert-warning'>Таблиця products пуста або не існує</div>";
            }

            $html = '<table class="table table-striped"><thead><tr>';
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
            return "<div class='alert alert-danger'>Помилка MySQL: " . $e->getMessage() . "</div>";
        }
    }
}