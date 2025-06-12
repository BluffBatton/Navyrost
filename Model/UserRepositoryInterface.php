<?php
interface UserRepositoryInterface {
    public function save(array $userData): void;
}
