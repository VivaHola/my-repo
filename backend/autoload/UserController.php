<?php

class UserController
{
    private $mapper;

    public function __construct($table)
    {
        global $f3;                        // needed for $f3->get()
        $this->mapper = new DB\SQL\Mapper($f3->get('DB'), $table);    // create DB query mapper object
    }

    public function register($username, $password)
    {
        // check if username exits
        $this->mapper->load(array('username=?', $username));
        if (!$this->mapper->dry()) {
            // if exits, return error
            return ['error' => true, 'message' => 'Email already exists'];
        }

        // save new user
        $this->mapper->reset();
        $this->mapper->username = $username;
        $this->mapper->password = password_hash($password, PASSWORD_DEFAULT); // 加密密码
        $this->mapper->save();

        return ['success' => true, 'message' => 'User registered successfully'];
    }

    public function login($username, $password)
    {
        $this->mapper->load(array('username=?', $username));

        if ($this->mapper->dry()) {
            echo json_encode(['error' => 'User not found']);
            return;
        }

        if (password_verify($password, $this->mapper->password)) {
            // password correct
            echo json_encode(['success' => 'Login successful', 'user' => $username]);
        } else {
            // password fail
            echo json_encode(['error' => 'Invalid password']);
        }

    }
}