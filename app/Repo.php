<?php ///teo /app/Repo.php

class Repo {
    ///Related object name
    static $OBJ;

    public static function row(array $a)
    {
        $r = new static::$OBJ;
        $r->set($a);
        return $r;
    }

    public static function findById($id)
    {
        
    }
    
    public static function findAll()
    {
        $rows = DB::rows('SELECT * FROM '.self::table());
        $r = array();
        foreach($rows as $v)
        {
            self::row($v);
        }
        
        return $r;
    }
    
    public static function table()
    {
        $t = static::$OBJ;
        return $t::table();
    }
}
