<?php

/**
 * Description of classGallery - klasa odczytująca zdjęcia z bazy galerii
 *
 * @author Tomek Rogalski
 */
class Gallery {

    public function __construct()
    {
        $this->select('SELECT * FROM gallery');
    }

    public function select($query)
    {        
        try
        {
            $res = Main::$db->query($query);
            $gallery = $res->fetchAll(PDO::FETCH_ASSOC);
            if(isset($gallery['title'])) $gallery['title'] = stripcslashes($gallery['title']);
            if(isset($gallery['description'])) $gallery['description'] = stripcslashes($gallery['description']);
        }
        catch(PDOException $e)
        {
            echo 'Selection failed: ' . $e->getMessage();
        }
        Main::$smarty->assign('gallery', $gallery);
    }

    public function  __destruct()
    {
        
    }
}
?>
