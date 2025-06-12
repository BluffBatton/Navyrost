<?php
require_once 'UserRepositoryInterface.php';

class LoggingUserSaveDecorator implements UserRepositoryInterface {
    private UserRepositoryInterface $wrapped;

    public function __construct(UserRepositoryInterface $wrapped) {
        $this->wrapped = $wrapped;
    }

    public function save(array $userData): void {
        $logData = date('Y-m-d H:i:s') . " | Saving user: " . json_encode([
            'firstname' => $userData['firstname'],
            'lastname' => $userData['lastname'],
            'email' => $userData['email'],
            'phone' => $userData['phonenumber'],
        ]) . PHP_EOL;

        file_put_contents(__DIR__ . '/../logs/user_save.log', $logData, FILE_APPEND);

        $this->wrapped->save($userData);
    }
}
