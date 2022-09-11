<?php

declare(strict_types=1);


namespace App\Model;


use App\Service\Text;
use PDO;

class User extends Model
{

    public  $id;
    public  $name;
    public  $email;
    public  $password;
    public  $token;
    public  $isAdmin;
    protected $table = 'user';

   public function findUserById(int $id)
   {
       $query =  $this->connection->prepare('SELECT * FROM user WHERE id=:id');
       $query->execute([":id" =>  $id]);

       $row = $query->fetch(PDO::FETCH_ASSOC);
       if ($row) {
           $user = new self;
           $user->id = (int) $row['id'];
           $user->email = $row['email'];
           $user->name = $row['name'];
           $user->password = $row['password'];
           $user->token = $row['token'];
           $user->isAdmin = (int) $row['is_admin'];
           return $user;
       }
       return false;
   }

    public function findUserByEmail(string $email)
    {
        $query =  $this->connection->prepare('SELECT * FROM user WHERE email=:email');
        $query->execute([":email" => $email]);

        $row = $query->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $user = new self();
            $user->id = (int) $row['id'];
            $user->email = $row['email'];
            $user->name = $row['name'];
            $user->password = $row['password'];
            $user->token = $row['token'];
            $user->isAdmin = $row['is_admin'];
            return $user;
        }
        return false;
    }

    public function isAdmin():bool
    {
        return $this->isAdmin === 1;
    }


    public function login(string $email, string $password): ?self
    {
        $user = (new self())->findUserByEmail($email);

        if (isset($user) && password_verify($password, $user->password)) {
            return $user;
        }
        return null;
    }




    public function save()
    {
        $query =  $this->connection->prepare('INSERT INTO user (email, name, password) VALUES (:email, :name, :password)');
        $query->execute([
            ':email' =>  $this->email,
            ':name'=> $this->name,
            ':password'=> $this->password
        ]);

        $id = $this->connection->lastInsertId();
        $this->id = is_numeric($id) ? (int) $id : $id;

        return $this;
    }




    public function createToken()
    {
        $token = hash('sha256', $plainTextToken = Text::random(40));
        $query =  $this->connection->prepare('UPDATE `user` SET token=:token WHERE id=:id');
        $query->execute([
            ":token" => $token,
            ":id" => $this->id
        ]);

        return  $this->id.'|'.$plainTextToken;
    }


    public function logout()
    {

    }
}