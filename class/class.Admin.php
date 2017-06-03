<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classAdmin
 * Klasa obsługująca system administrowania galeria (dodanie usuniecie zdjęcia edycje tytułów i opisów zdjęć)
 * @author Tomek ROGALSKI
 */
class Admin {

    public function  __construct()
    {
        isset($_GET['act']) ? $act = $_GET['act'] : $act = 'showList';
        if($_SESSION['login'] == true)
        {
            switch($act)
            {
            case 'showList':
                $this->showList();
                break;
            case 'add':
                $this->add();
                break;
            case 'remove':
                $this->remove();
                break;
            case 'edit':
                $this->edit();
                break;
            default:
                break;
            }
        }
        else
        {
            $this->log('login');
        }
        
    }

    /**
     * Metoda logujaca/wylogujaca
     * @param string akcja do wykonania login/logout
     */
    private function log($act = 'login')
    {
        if($act == 'login')
        {
            try
            {
                $res = Main::$db->query('SELECT value FROM `config` WHERE `name`="login" OR `name`="pass"');
                $tabLog = $res->fetchAll(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e)
            {
                echo 'SELECT Login error '.$e->getMessage();
            }
            $user = $tabLog[0]['value'];
            $pass = $tabLog[1]['value'];
            if($user == $_POST['login'] && $pass == md5($_POST['pass']))
            {                
                $_SESSION['login'] = true;
                Main::$smarty->assign('context', 'admin/admin-lista-galerii.html');
            }
            else
            {
                if(isset($_POST['wyslij']))
                {
                    Main::$smarty->assign('msgError', 'Niepoprawny login lub hasło');
                }
                else
                {
                    Main::$smarty->assign('msgError', '');
                }
                Main::$smarty->assign('context', 'admin/logowanie.html');
                $_SESSION['login'] = false;
            }
        }
        elseif($act == 'logout')
        {
            $_SESSION['login'] = false;
        }
    }
    /**
     * Metoda wczytujaca liste zdjec
     */
    private function showList()
    {
        try
        {
            $res = Main::$db->query('SELECT * FROM gallery');
            $list = $res->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            echo 'ShowList error '.$e->getMessage();
        }
        Main::$smarty->assign('list', $list);
        Main::$smarty->assign('context', 'admin/admin-lista-galerii.html');
    }

    private function add()
    {
        if(isset($_POST['save']))
        {
            //Przygotowanie adresu zdjecia pod baze subkatalog jest zamieniany na ./img
            //lokalnie
            //$img = preg_replace('#\/.*\/#iU', './', $_POST['zdjecie'], 1);
            //serwer
            $img = $_POST['zdjecie'];
            //Obsluga bazy i zapisanie danych do bazy
            try
            {
                $res = Main::$db->prepare('INSERT INTO gallery (`title`, `description`, `src`) VALUE (:title, :description, :src)');
                $res->bindValue(':title', addslashes($_POST['title']));
                $res->bindValue(':description', preg_replace('#<.?p>#iU', '', addslashes($_POST['description'])));
                $res->bindValue(':src', $img);
                $res->execute();
            }
            catch(PDOException $e)
            {
                echo 'Błąd podczas dodawnaia nowego elementu do galerii '.$e->getMessage();
            }
            //Przekieroanie na liste galerii
            header('Location: admin.html');
        }
        else
        {
            Main::$smarty->assign('context', 'admin/admin-galerii-dodaj.html');
        }
    }

    private function remove()
    {
        try
        {
            $res = Main::$db->prepare('DELETE FROM gallery WHERE id=:id');
            $res->bindValue(':id', $_GET['id']);
            $res->execute();
        }
        catch(PDOException $e)
        {
            echo 'Błąd podczas usuwania wpisu z galerii '.$e->getMessage();
        }
        header('Location: admin.html');
    }

    private function edit()
    {
        if(isset($_POST['save']))
        {
            //Przygotowanie adresu zdjecia pod baze subkatalog jest zamieniany na ./img
            //lokalnie
            //$img = preg_replace('#\/.*\/#iU', './', $_POST['zdjecie'], 1);
            //serwer
            $img = $_POST['zdjecie'];

            //Obsluga bazy i zapisanie danych do bazy
            try
            {
                $res = Main::$db->prepare('UPDATE gallery SET `title`=:title, `description`=:description, `src`=:src WHERE `id`=:id');
                $res->bindValue(':id', $_POST['hidden_id']);
                $res->bindValue(':title', addslashes($_POST['title']));
                $res->bindValue(':description', preg_replace('#<.?p>#iU', '', addslashes($_POST['description'])));
                $res->bindValue(':src', $img);
                $res->execute();
            }
            catch(PDOException $e)
            {
                echo 'Błąd podczas uaktualniania wpisu w galerii '.$e->getMessage();
            }
            //Przekieroanie na liste galerii
            header('Location: admin.html');
        }
        else
        {
            try
            {
                $res = Main::$db->prepare('SELECT * FROM gallery WHERE `id`=:id');
                $res->bindValue(':id', intval($_GET['id']));
                $res->execute();
                $list = $res->fetch(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e)
            {
                echo 'ShowList error '.$e->getMessage();
            }
            Main::$smarty->assign('list', $list);
            Main::$smarty->assign('context', 'admin/admin-edytuj-galerie.html');
        }
    }

    public function  __destruct()
    {
        
    }
}
?>
