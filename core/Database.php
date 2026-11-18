<?php

namespace Core;

use PDO;

class Database extends PDO{
    public function __construct(){
        parent::__construct(
            dsn: "mysql:hostname-localhost;dbname=pettisco",
            username: "root",
            password: ""
        );
    }
}