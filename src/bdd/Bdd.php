<?php
function bdd($name = 'Tplprs')
{
    try {
        return new PDO("mysql:host=127.0.0.1;port=8889;dbname=$name;charset=utf8", 'root', 'root');//bdd pour moi mamp
    } catch (Exception $e) { // ou PDOException $e
        return new PDO("mysql:host=127.0.0.1;port=3306;dbname=$name;charset=utf8", 'root', '');//bdd pour vous wamp tt les cas ça change rien
    }
}