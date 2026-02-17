<?php
require_once __DIR__ . '/../connect.php';

class Login
{
    public $con;
    public $user;
    public $pass;

    public function __construct($con, $user, $pass)
    {
        $this->con  = $con;
        $this->user = $user;
        $this->pass = $pass;
    }

    // ✅ Define it here so it's always available
    private function mysql_native_password_hash($plain)
    {
        return '*' . strtoupper(sha1(sha1($plain, true)));
    }
 public function login()
    {
        $hash = $this->mysql_native_password_hash($this->pass);

        $sql = "
            SELECT *
            FROM employees
            WHERE email = ?
              AND password = ?
              AND usertype = 1
              AND designationid = 1
            LIMIT 1
        ";

        $stmt = $this->con->prepare($sql);
        if (!$stmt) {
            die($this->con->error);
        }

        $stmt->bind_param("ss", $this->user, $hash);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $_SESSION['tittu']    = $row['email'];
            $_SESSION['empname']  = $row['name'];
            $_SESSION['empid']    = $row['empid'];
            $_SESSION['id']       = $row['id'];
            $_SESSION['image']    = $row['image'];
            $_SESSION['usertype'] = $row['usertype'];
            return true;
        }

        return false;
    }
}

?>


