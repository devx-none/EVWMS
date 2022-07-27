<?php

namespace App\Http\Controllers;

use Ripcord\Ripcord;

use App\Models\delivery;
use App\Http\Requests\StoredeliveryRequest;
use App\Http\Requests\UpdatedeliveryRequest;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoredeliveryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $url = env('RPC_URL');
        $db = env('RPC_DB');
        $username = env('RPC_USERNAME');
        $password = env('RPC_PASSWORD');
        $url_auth = $url . '/xmlrpc/2/common';
        $url_exec = $url . '/xmlrpc/2/object';

        $info = Ripcord::client('https://demo.odoo.com/start')->start();
        $common = Ripcord::client($url_auth);
        $ver = $common->version();
       

        //Authenticate the credentials
        $uid = $common->authenticate($db, $username, $password, array());
         
        //Get the models of the database
        $models = Ripcord::client($url_exec);
        $check = $models->execute_kw($db, $uid, $password, 'res.partner', 'check_access_rights', array('read'), array('raise_exception' => false));


        //Get the fields of the model
        $fields = $models->execute_kw($db, $uid, $password, 'res.partner', 'fields_get', array(), array('fields' => array('string', 'help', 'type')));

        $name = $request->input('name');
        $street = $request->input('street');
        $city = $request->input('city');
        $zip = $request->input('zip');
        $mobile = $request->input('mobile');
        $scheduled_date = date('Y-m-d H:i:s');

        $order_number = $request->input('order_number');
        $product_name = $request->input('product_name');
        //send array in request 


        //check if the partner exists
        $partner_id = $models->execute_kw($db, $uid, $password, 'res.partner', 'search_read', array(array(array('name', '=', $name))), array('fields' => array('id'), 'limit' => 1));
        $partner = $models->execute_kw($db, $uid, $password, 'res.partner', 'search_read', array(), array('fields' => array('name')));

        
        if ($partner_id == null) {
            $partner_id = $models->execute_kw($db, $uid, $password, 'res.partner', 'create', array(array('name' => $name, 'street' => $street, 'city' => $city, 'zip' => $zip, 'mobile' => $mobile)));
            $partner_id = $models->execute_kw($db, $uid, $password, 'res.partner', 'search_read', array(array(array('name', '=', $name))), array('fields' => array('id'), 'limit' => 1));

        }
        
        
        //type Operation
        $type_operation = $models->execute_kw($db, $uid, $password, 'stock.picking.type', 'search_read', array(array(array('name', '=', "Livraisons"))), array('fields'=>array('id'), 'limit'=>1));
        if($type_operation){
            $operation = $type_operation[0]['id'];
        }
        //location 
        $location = $models->execute_kw($db, $uid, $password, 'stock.location', 'search_read',array(array(array('barcode', '=', "WH-STOCK"))), array('fields'=>array('id'), 'limit'=>1));
        // $location = $location[0]['id'];

        $location_dest_id = $models->execute_kw($db, $uid, $password, 'stock.location', 'search_read',array(array(array('name', '=', "Stock"))), array('fields'=>array('id'), 'limit'=>1));

        //check if products exists
        $products = $models->execute_kw($db, $uid, $password, 'product.product', 'search_read', array(array(array('name', '=', $product_name))), array('fields'=>array('id'), 'limit'=>1));

        
        $delivery = $models->execute_kw($db, $uid, $password, 'stock.picking', 'create', array(array('location_id'=>(int)8,'location_dest_id'=>(int)5,'picking_type_id'=>(int)2,'product_id'=>(int)$products ,'partner_id'=>(int)$partner_id,'scheduled_date'=>$scheduled_date,'origin'=>$order_number)));

         
        if($delivery) {
            return response()->json(['success' => true, 'message' => 'Delivery created successfully','delivery:'=>$delivery,'partner_id' =>(int) $partner_id,"products:"=>$products,'picking_type_id'=>(int)$type_operation,'location'=>$location ,'location_dest_id'=>$location_dest_id,'partners'=>$partner,'operation'=>$operation]);
        } else {
            return response()->json(['success' => false, 'message' => 'Delivery not created']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\delivery  $delivery
     * @return \Illuminate\Http\Response
     */
    public function show(delivery $delivery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\delivery  $delivery
     * @return \Illuminate\Http\Response
     */
    public function edit(delivery $delivery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatedeliveryRequest  $request
     * @param  \App\Models\delivery  $delivery
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatedeliveryRequest $request, delivery $delivery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\delivery  $delivery
     * @return \Illuminate\Http\Response
     */
    public function destroy(delivery $delivery)
    {
        //
    }
}
