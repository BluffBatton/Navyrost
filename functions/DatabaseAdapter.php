<?php
interface DatabaseAdapter {
    public function connect();
    public function query($sql);
    public function fetchAll($result);
}