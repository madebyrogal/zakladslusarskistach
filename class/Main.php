<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ('./inc/smarty/Smarty.class.php');
require_once('./class/class.Contact.php');
require_once('./class/class.Gallery.php');
require_once('./class/class.Admin.php');

session_start();
//Sesja 10 min
session_set_cookie_params('1200');
/**
 * Description of Main
 * Główna klasa jako inicjacja strony oraz innych głównych enginów
 * @author Tomek Rogalski
 */
class Main {
    public static $smarty;
    public static $db;
    public function __construct() {        
        $context = (isset($_GET['context'])) ? $_GET['context'] : 'home';
        $page = (isset($_GET['page'])) ? $_GET['page'] : 'home';
        $this -> init_smart(); //inicjacja smarty
        $this -> init_db(); //inicjacja bazy danych        
        $this -> init_menu($page);
        $this -> init_context($context);
    }
    public function init_smart(){        
        self::$smarty = new Smarty();
        self::$smarty -> template_dir = './tpls/';
        self::$smarty -> compile_dir = './tpls/compile';
        self::$smarty -> config_dir = './tpls/config';
        self::$smarty -> cache_dir = './tpls/cache';
    }

    public function init_db(){
        $dbTable = parse_ini_file('./cfg/config.ini');
        $dsn = 'mysql:dbname='.$dbTable['db_name'].';host='.$dbTable['host'];
        $user = $dbTable['user'];
        $password = $dbTable['pass'];

        try
        {
            self::$db = new PDO($dsn, $user, $password);
        }
        catch(PDOException $e)
        {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
    
    public function init_menu($page){
        // Inicjacja głównego menu
        $menu['home'] =         array('id' => 'm_main', 'title' => 'Strona główna', 'addres' => 'home', 'class' => '');
        $menu['o-nas'] =        array('id' => 'm_nas', 'title' => 'O nas', 'addres' => 'o-nas', 'class' => '');
        $menu['oferta'] =       array('id' => 'm_oferta', 'title' => 'Oferta', 'addres' => 'oferta', 'class' => '');
        $menu['galeria'] =      array('id' => 'm_gal', 'title' => 'Galeria', 'addres' => 'galeria', 'class' => '');
        $menu['lokalizacja'] =  array('id' => 'm_local', 'title' => 'Gdzie nas znaleźć', 'addres' => 'lokalizacja', 'class' => '');
        $menu['kontakt'] =      array('id' => 'm_cont', 'title' => 'Kontakt', 'addres' => 'kontakt', 'class' => '');
        
        foreach($menu as $key => $val){
            if($key === $page){
                $menu[$key]['class'] = 'active';
                self::$smarty -> assign('page', $menu[$key]['title']);
                break;
            }
        }
        self::$smarty -> assign('menu', $menu);
    }
    
    public function init_context($contextpage){
        //Strona startowa
        $context['home']    = array('title' => 'Metaloplastyka Rzeszów', 'keywords' => 'metaloplastyka,kowalstwo artystyczne,balustrady,ogrodzenia,bramy,kute,Rzeszów', 'description' => 'Artystyczna metaloplastyka oferująca piękne kute balustrady,ogrodzenia,bramy.','context' => 'home');
        //O nas
        $context['o-nas']  = array('title' => 'Kowalstwo artystyczne Rzeszów', 'keywords' => 'ogrodzenia,schody,zadaszenia,Rzeszów,usługi ślusarskie,kute meble,balustrady', 'description' => 'Szeroki wachlarz usług kowalstwa artystycznego z Rzeszowa', 'context' => 'o-nas');
        //Oferta
        $context['oferta']  = array('title' => 'Balustrady, bramy, ogrodzenia kute', 'keywords' => 'balustrady,Rzeszów,zadaszenia,bramy,kute,konstrukcje stalowe', 'description' => 'Wykonuje wysokiej klasy balustrady, bramy, zadaszenia, konstrukcje stalowe.', 'context' => 'oferta');
        //Galeria
        $context['galeria'] = array('title' => 'Kowalstwo artystyczne z zachowaniem tradycyjnych metod kowalskich', 'keywords' => 'kowalstwo artystyczne,Stach,konstrukcje stalowe,Rzeszów,Stach,spawanie,kraty', 'description' => 'Zajmuje się wykonywaniem bram,konstrukcji stalowych,krat,spawaniem na indywidualne zamówienie klienta.', 'context' => 'galeria');
        //Lokalizacja
        $context['lokalizacja']  = array('title' => 'Metaloplastyka artystyczna i kowalstwo artystyczne Rzeszów', 'keywords' => 'usługi ślusarskie,metaloplastyka artystyczna,wyroby kute,Stach,Rzeszów,kraty', 'description' => 'Pracownia kowalstwa artystycznego i metaloplastyki Stach wykonuje balustrady kute, poręcze kute, bramy kute, ogrodzenia kute, płoty kute, kraty, elementy kute, elementy wystroju wnętrz, usługi kowalskie', 'context' => 'lokalizacja');
        //Kontakt
        $context['kontakt'] = array('title' => 'Pracowania rzemiosła artystycznego', 'keywords' => 'Rzeszów,metaloplastyka,zakład ślusarski stach,kowalstwo artystyczne,spawanie,bramy,ogrodzenia', 'description' => 'Zakład ślusarski Stach zajmuje się wykonywaniem usług w zakresie metaloplastyki, kowalstwa artystycznego - począwszy od świeczników po balustrady i konstrukcje stalowe. ', 'context' => 'kontakt');
        //Admin ale jest to główna strona nie kontekst
        $context['admin'] = array('title' => 'Panel Administratora', 'keywords' => '', 'description' => '', 'context' => '');

        switch($contextpage){
            case 'home':
                $this->initMainGalery(4);
                break;
            case 'kontakt':
                new Contact();
                break;
            case 'galeria':
                new Gallery();
                break;
            case 'admin':
                new Admin();
                break;
        }
        if(self::$smarty -> get_template_vars('title') == '')
        {
            self::$smarty -> assign('title', $context[$contextpage]['title']);
        }
        if(self::$smarty -> get_template_vars('keywords') == '')
        {
            self::$smarty -> assign('keywords', $context[$contextpage]['keywords']);
        }
        if(self::$smarty -> get_template_vars('description') == '')
        {
            self::$smarty -> assign('description', $context[$contextpage]['description']);
        }
        if(self::$smarty -> get_template_vars('context') == '')
        {
            self::$smarty -> assign('context', $context[$contextpage]['context'].'.html');
        }
        self::$smarty->assign('actYear', date('Y'));
        if($_GET['display'] != 'admin')
        {
            self::$smarty -> display('index.html');
        }
        else
        {
            self::$smarty->display('admin/admin.html');
        }
    }

    /**
     * Metoda generująca losowa tablicę liczba określających nr obrazka
     * @param int $amountOfImg Liczaba obrazków w galerii (./img/main/[1-$amountOfImg].jpg)
     */
    public function initMainGalery($amountOfImg)
    {
        $slideShow = array();
        //Wypełnienie tablicy elementami
        for($i=1; $i<=$amountOfImg; $i++)
        {
            array_push($slideShow, $i);
        }
        shuffle($slideShow);
        self::$smarty->assign('slideShow', $slideShow);        
    }
    public function __destruct() {

    }
}
?>
