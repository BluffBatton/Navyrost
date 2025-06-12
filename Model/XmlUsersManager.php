<?php
class XmlUsersManager {
    private $dom;
    private $root;
    private $xmlFile;

    public function __construct() {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;
        $this->xmlFile = __DIR__ . '/../data/users.xml';

        if (!file_exists(dirname($this->xmlFile))) {
            mkdir(dirname($this->xmlFile), 0755, true);
        }

        libxml_use_internal_errors(true);
        
        if (file_exists($this->xmlFile) && $this->dom->load($this->xmlFile)) {
            $this->root = $this->dom->documentElement;
        } else {
            $this->root = $this->dom->createElement('users');
            $this->dom->appendChild($this->root);
            $this->save();
        }
        
        libxml_clear_errors();
    }

    public function getRoot() {
        return $this->root;
    }

    public function save() {
        try {
            $result = $this->dom->save($this->xmlFile);
            if ($result === false) {
                throw new Exception("Не вдалося зберегти XML файл");
            }
            return $result;
        } catch (Exception $e) {
            error_log("Помилка збереження XML: " . $e->getMessage());
            return false;
        }
    }

public function addUser(array $userData) {
    $userNode = $this->dom->createElement('user');
    
    foreach ($userData as $key => $value) {
        if (!is_string($key)) {
            continue;
        }

        $cleanKey = $this->sanitizeXmlElementName($key);
        $element  = $this->dom->createElement($cleanKey);
        $text     = $this->dom->createTextNode(htmlspecialchars($value, ENT_XML1));
        $element->appendChild($text);
        $userNode->appendChild($element);
    }
    
    $this->root->appendChild($userNode);
    return $this->save();
}


    private function sanitizeXmlElementName($name) {
        $name = preg_replace('/[^a-zA-Z_\-\.0-9]/', '', $name);
        
        if (preg_match('/^[0-9]/', $name)) {
            $name = 'field_' . $name;
        }
        
        return empty($name) ? 'unknown_field' : $name;
    }

    public function getAllUsers() {
        $users = [];
        foreach ($this->root->getElementsByTagName('user') as $userNode) {
            $userData = [];
            foreach ($userNode->childNodes as $childNode) {
                if ($childNode->nodeType === XML_ELEMENT_NODE) {
                    $userData[$childNode->nodeName] = $childNode->nodeValue;
                }
            }
            $users[] = $userData;
        }
        return $users;
    }

    public function getUserById($id) {
        $xpath = new DOMXPath($this->dom);
        $result = $xpath->query("//user/id[.='".addslashes($id)."']/parent::*");
        return $result->count() > 0 ? $this->nodeToArray($result->item(0)) : null;
    }

    public function updateUser($id, array $newData) {
        $xpath = new DOMXPath($this->dom);
        $userNode = $xpath->query("//user/id[.='".addslashes($id)."']/parent::*")->item(0);

        if ($userNode) {
            foreach ($newData as $key => $value) {
                $cleanKey = $this->sanitizeXmlElementName($key);
                $element = null;
                
                foreach ($userNode->childNodes as $child) {
                    if ($child->nodeType === XML_ELEMENT_NODE && $child->nodeName === $cleanKey) {
                        $element = $child;
                        break;
                    }
                }
                
                if ($element) {
                    $element->nodeValue = htmlspecialchars($value, ENT_XML1);
                } else {
                    $newElement = $this->dom->createElement($cleanKey);
                    $newElement->appendChild($this->dom->createTextNode(htmlspecialchars($value, ENT_XML1)));
                    $userNode->appendChild($newElement);
                }
            }
            return $this->save();
        }
        return false;
    }

    public function deleteUser($id) {
        $xpath = new DOMXPath($this->dom);
        $userNode = $xpath->query("//user/id[.='".addslashes($id)."']/parent::*")->item(0);

        if ($userNode) {
            $this->root->removeChild($userNode);
            return $this->save();
        }
        return false;
    }

    private function nodeToArray(DOMElement $node) {
        $data = [];
        foreach ($node->childNodes as $childNode) {
            if ($childNode->nodeType === XML_ELEMENT_NODE) {
                $data[$childNode->nodeName] = $childNode->nodeValue;
            }
        }
        return $data;
    }

    public function fileExists() {
        return file_exists($this->xmlFile);
    }

    public function exportToFile($filename) {
        try {
            $result = $this->dom->save($filename);
            return $result !== false;
        } catch (Exception $e) {
            error_log("Помилка експорту у файл: " . $e->getMessage());
            return false;
        }
    }

    public function importFromFile($filename) {
        if (!file_exists($filename)) {
            error_log("Файл для імпорту не знайдено: " . $filename);
            return false;
        }

        libxml_use_internal_errors(true);
        $tempDom = new DOMDocument();
        
        if (!$tempDom->load($filename)) {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                error_log("Помилка парсингу XML: " . $error->message);
            }
            libxml_clear_errors();
            return false;
        }

        if ($tempDom->documentElement->nodeName !== 'users') {
            error_log("Невірний кореневий елемент XML");
            libxml_clear_errors();
            return false;
        }

        while ($this->root->firstChild) {
            $this->root->removeChild($this->root->firstChild);
        }

        foreach ($tempDom->documentElement->childNodes as $node) {
            if ($node->nodeType === XML_ELEMENT_NODE && $node->nodeName === 'user') {
                $importedNode = $this->dom->importNode($node, true);
                $this->root->appendChild($importedNode);
            }
        }

        libxml_clear_errors();
        return $this->save();
    }

    public function exportFromSqlite(Database $db) {
        try {
            $users = $db->execQuery("SELECT * FROM users");
            
            while ($this->root->firstChild) {
                $this->root->removeChild($this->root->firstChild);
            }
            
            foreach ($users as $user) {
                $this->addUser($user);
            }
            
            return $this->save();
        } catch (Exception $e) {
            error_log("Помилка експорту з SQLite: " . $e->getMessage());
            return false;
        }
    }

    public function displayXmlAsHtml() {
    if ($this->root->childNodes->length === 0) {
        return '<div class="alert alert-info">XML файл порожній. Додайте користувачів через імпорт або експорт з бази даних.</div>';
    }

    $users = $this->root->getElementsByTagName('user');

    $first = $users->item(0);
    $cols  = [];
    foreach ($first->childNodes as $field) {
        if ($field->nodeType === XML_ELEMENT_NODE) {
            $cols[] = $field->nodeName;
        }
    }

    $html  = '<table class="table table-striped">';
    $html .= '<thead><tr>';
    foreach ($cols as $col) {
        $html .= '<th>' . htmlspecialchars($col) . '</th>';
    }
    $html .= '</tr></thead><tbody>';

    foreach ($users as $user) {
        $html .= '<tr>';
        foreach ($cols as $col) {
            $nodeList = $user->getElementsByTagName($col);
            $value    = $nodeList->length ? $nodeList->item(0)->nodeValue : '';
            $html    .= '<td>' . htmlspecialchars($value) . '</td>';
        }
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    return $html;
}
    // public function displayXmlAsHtml() {
    //     // Якщо немає користувачів, повертаємо інформаційне повідомлення
    //     if ($this->root->childNodes->length === 0) {
    //         return '<div class="alert alert-info">XML файл порожній. Додайте користувачів через імпорт або експорт з бази даних.</div>';
    //     }

    //     // Спроба використати XSLT трансформацію, якщо доступно
    //     if (class_exists('XSLTProcessor')) {
    //         try {
    //             $xslPath = __DIR__ . '/../View/style.xsl';
    //             if (file_exists($xslPath)) {
    //                 $xsl = new DOMDocument();
    //                 if ($xsl->load($xslPath)) {
    //                     $proc = new XSLTProcessor();
    //                     $proc->importStyleSheet($xsl);
    //                     $result = $proc->transformToXML($this->dom);
    //                     if ($result !== false) {
    //                         return $result;
    //                     }
    //                 }
    //             }
    //         } catch (Exception $e) {
    //             error_log("XSLT помилка: " . $e->getMessage());
    //         }
    //     }

    //     // Fallback: відображення чистого XML з підсвіткою синтаксису
    //     $this->dom->formatOutput = true;
    //     $xmlString = $this->dom->saveXML();
        
    //     if ($xmlString === false) {
    //         return '<div class="alert alert-danger">Не вдалося сформувати XML</div>';
    //     }

    //     return '<pre class="xml-display">' . htmlspecialchars($xmlString) . '</pre>';
    // }

    public function hasUsers() {
        return $this->root->childNodes->length > 0;
    }
}