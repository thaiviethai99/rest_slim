<?php
use Medoo\Medoo;

class DbHandler
{

    private $conn;

    public function __construct()
    {
        $database = new Medoo([
            'database_type' => 'mysql',
            'database_name' => 'demo',
            'server'        => '192.168.142.128',
            'username'      => 'root',
            'password'      => '1281988',
        ]);
        // opening db connection
        $this->conn = $database;
    }


    public function getAll($start, $limit)
    {
        $result = $this->conn->select('articles', '*', [ 'ORDER' => ['id' => 'DESC'],"LIMIT" => [$start, $limit]]);
        return $result;
    }

    public function totalRow()
    {
        $result = $this->conn->count(
            'articles'
        );
        return $result;
    }

    public function getOne($id)
    {
        $result = $this->conn->select(
            'articles',
            "*",
            array('id' => $id)
        );
        return $result;
    }

    public function insertTitle($data)
    {
        $result = $this->conn->insert(
            'articles',
            $data
        );
        return $this->conn->id();
    }

    public function deleteTitle($id)
    {
        $result = $this->conn->delete(
            'articles',
            array('id' => $id)
        );
        return 1;
    }

    public function updateTitle($id,$data){
        $result = $this->conn->update(
            'articles',
            $data,
            array('id' => $id)
        );
        return 1;
    }
}
