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
        $r = DB::row($id);
        if($r)
            return self::row($r);
        else
            return null;
    }
    
    public static function findAll()
    {
        $rows = DB::rows('SELECT * FROM '.self::table());
        $r = array();
        foreach($rows as $v)
            $r[] = self::row($v);
        
        return $r;
    }
    
    public static function table()
    {
        $t = static::$OBJ;
        return $t::table();
    }
}
