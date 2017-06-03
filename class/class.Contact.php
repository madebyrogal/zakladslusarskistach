<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classContact
 *
 * @author Tomek Rogalski
 */
class Contact{
    public function __construct(){
        if(isset($_POST['wyslij']))
        {
            (isset($_POST['name'])) ? $name = $_POST['name'] : $name = 0;
            $email = (isset($_POST['email'])) ? $_POST['email'] : 0;
            $message = (isset($_POST['message'])) ? $_POST['message'] : 0;
            $this->send($name, $email, $message);
            Main::$smarty->assign('formularz', 0);
        }
        else Main::$smarty->assign('formularz', 1);
    }
    private function send($name, $email, $message){
        $header = 'Content-type: text/html; charset=utf-8' . "\r\n";
        $header .= 'From: '.$name.' <'.$email.'>';

        if(@mail('zakladslusarskistach@gmail.com', 'Zapytanie ze strony internetowej', $message, $header))
        {
            $mail = 1;
        }
        else $mail = 0;
        Main::$smarty->assign('mail', $mail);
    }

    public function __destruct(){
        
    }
}
?>
