<?php
// Начинаем сессию
session_start();
// Подключаем файл с настройками ldap
include_once ("ldap.php");
 
// Проверка пользователя используя LDAP
if (isset($_POST['login']) && isset($_POST['password']))
      {
      $username = $_POST['login'];
      $login = $_POST['login'].$domain;
      $password = $_POST['password'];

      // Создаем подключение к LDAP серверу
      $ldap = ldap_connect($ldaphost,$ldapport) or die("Не могу подключиться к LDAP серверу");

      if ($ldap)
            {
            // Пытаемся войти в LDAP при помощи введенных логина и пароля
            $bind = ldap_bind($ldap,$login,$password);
            if ($bind)
                  {
                  // Делаем проверку, состоит ли пользователь в группе
        		    $result = ldap_search($ldap,$base,"samaccountname=".$username, array("*"));
                  // Получаем количество результатов предыдущей проверки
                    $result_ent = ldap_get_entries($ldap,$result);
            }
            else
                  {
                  die('Неправильное имя пользователя или пароль<br /> <a href="index.php">Попробовать снова</a>');
            }
      }
 
      // Если аутентификация успешна, то перебрасываем на следующую страницу cabinet.php
      if ($result_ent['count'] != 0)
            {
            $_SESSION['user_id'] = $login;
            $_SESSION['user_ent'] = $result_ent;
            header('Location: cabinet.php');
            exit;
      }
      else
            {
            die('Доступ запрещен<br /> <a href="index.php">Попробовать снова</a>');
      }
}
?>