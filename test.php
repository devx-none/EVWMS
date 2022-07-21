<?php

use Ripcord\Ripcord;

require __DIR__ . '/vendor/autoload.php';

require_once __DIR__.'/vendor/darkaonline/ripcord/src/Ripcord/Ripcord.php';


$url = "http://127.0.0.1:8000";
$db = "epicerieverte";
$username = "admin";
$password = "admin";

$info = Ripcord::client('https://demo.odoo.com/start')->start();
$common = Ripcord::client("$url/xmlrpc/2/common");
$ver = $common->version();
echo  "Odoo version: $ver\n";

//Authenticate the credentials
$uid = $common->authenticate($db, $username, $password, array());
echo $uid;

//Get the models of the database
$models = Ripcord::client("$url/xmlrpc/2/object");

//Get the fields of the model
$products = $models->execute_kw($db, $uid, $password, 'res.partner', 'fields_get', array(), array('products' => array('string', 'help', 'type')));
echo $products;

//Get the records of the model
$records = $models->execute_kw($db, $uid, $password, 'res.partner', 'search', array(array()));
echo $records;

//get the record by id
$record = $models->execute_kw($db, $uid, $password, 'res.partner', 'search', array(array('id', '=', 1)));
echo $record;

//get the record by name
$record = $models->execute_kw($db, $uid, $password, 'res.partner', 'search', array(array('name', '=', 'test')));
echo $record;

//get the record by quantity valid Quantity superior to 0
$record = $models->execute_kw($db, $uid, $password, 'res.partner', 'search', array(array('quantity', '>', 0)));
echo $record;

//get the record by quantity valid 
$record = $models->execute_kw($db, $uid, $password, 'res.partner', 'search', array(array('quantity', 'in', array(0, 1))));
echo $record;

//get the record by quantity valid
$record = $models->execute_kw($db, $uid, $password, 'res.partner', 'search', array(array('quantity', 'not in', array(0, 1))));
echo $record;





