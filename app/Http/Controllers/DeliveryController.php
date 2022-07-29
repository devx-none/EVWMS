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
        $barcode_product = $request->input('barcode_product');
        $quantity = $request->input('quantity');
        $loc = $request->input('location');



        //check if the partner exists
        $partner_id = $models->execute_kw($db, $uid, $password, 'res.partner', 'search_read', array(array(array('name', '=', $name),array('company_type','=','person'))), array('fields' => array('id'), 'limit' => 1));
        // $partner = $models->execute_kw($db, $uid, $password, 'res.partner', 'search_read', array(), array('fields' => array('name')));

        
        if ($partner_id == null) {
            $partner_id = $models->execute_kw($db, $uid, $password, 'res.partner', 'create', array(array('name' => $name, 'street' => $street, 'city' => $city, 'zip' => $zip, 'mobile' => $mobile)));
            $partner_id = $models->execute_kw($db, $uid, $password, 'res.partner', 'search_read', array(array(array('name', '=', $name,'AND','company_type','=','person'))), array('fields' => array('id'), 'limit' => 1));

        }
        
        
        //type Operation
        $type_operation = $models->execute_kw($db, $uid, $password, 'stock.picking.type', 'search_read', array(array(array('barcode', '=', "WH-DELIVERY"))), array('fields'=>array('id'), 'limit'=>1));
       
        //location 
        $location = $models->execute_kw($db, $uid, $password, 'stock.location', 'search_read',array(array(array('barcode', '=', $loc))), array('fields'=>array('id'), 'limit'=>1));

        //location destination
        $location_dest_id = $models->execute_kw($db, $uid, $password, 'stock.location', 'search_read',array(array(array('name', '=', "Customers"))), array('fields'=>array('id'), 'limit'=>1));

        //check if products exists
        foreach ($barcode_product as $key => $value) {
            $product_exist = $models->execute_kw($db, $uid, $password, 'product.product', 'search_read', array(array(array('barcode', '=', $value))), array('fields'=>array('id'), 'limit'=>1));
            if ($product_exist == null) {
                return response()->json(['error' => 'Product not found'], 400);
            }
            $products[] = $product_exist[0]['id'];

        }

        $arr_products = array();
        for($i=0;$i<count($products);$i++){

            $arr_products[] =  array('product_id'=>$products[$i],'product_uom_qty'=>$quantity[$i],'product_uom'=>1,'location_id'=>$location[0]['id'],'location_dest_id'=>$location_dest_id[0]['id'],'company_id'=>1,'name'=>'test');
        }
       

        $delivery = $models->execute_kw($db, $uid, $password, 'stock.picking', 'create', array(array(
            'location_id'=>$location[0]['id'],
            'location_dest_id'=>$location_dest_id[0]['id'],
            'picking_type_id'=>$type_operation[0]['id'] ,
            'partner_id'=>$partner_id[0]['id'],
            'scheduled_date'=>$scheduled_date,
            'origin'=>$order_number,
            'move_ids_without_package'=>$arr_products
        )));

       
        //create new fields for the model picking
        $fields = $models->execute_kw($db, $uid, $password, 'stock.move', 'fields_get', array());


        $id_model = $models->execute_kw($db, $uid, $password, 'ir.model', 'search_read', array(), array( 'fields'=>array()));

        // $id_model = $models->execute_kw($db, $uid, $password, 'ir.model.fields', 'create', array(array(
            // 'model_id'=> 403, 'name'=> 'x_location_test', 'field_description'=> 'Delivery',  'relation'=> 'stock.move', 'required'=> false,'ttype'=>'char' ,'readonly'=> false, 'index'=> false, 'store'=> true, 'selectable'=> true, 'translate'=> false, 'selectable'=> true )));



        //read all models
        $models = $models->execute_kw($db, $uid, $password, 'ir.model', 'search_read', array(), array('fields'=>array()));
       
        //read all fields of the model
        $fields = $models->execute_kw($db, $uid, $password, 'ir.model', 'search_read', array(), array('fields'=>array()));

        //create new field for the model stock.move
        $add_fields = $models->execute_kw($db, $uid, $password, 'ir.model.fields', 'create', array(array(
            'model_id'=> 396, 'name'=> 'x_location_test', 'field_description'=> 'Delivery',  'relation'=> 'stock.picking', 'required'=> false,'ttype'=>'char' ,'readonly'=> false, 'index'=> false, 'store'=> true, 'selectable'=> true, 'translate'=> false, 'selectable'=> true )));


        $new_record = $models->execute_kw($db, $uid, $password, 'stock.picking', 'create', array(array('x_location_test' => "test location",'location_id'=>$location[0]['id'],'location_dest_id'=>$location_dest_id[0]['id'],'picking_type_id'=>$type_operation[0]['id'],'partner_id'=>$partner_id[0]['id'],'scheduled_date'=>$scheduled_date,'origin'=>$order_number,'move_ids_without_package'=>$arr_products)));

        // $record = $models->execute_kw($db, $uid, $password, 'stock.picking', 'read', array(array(($record_id)));



        // $get_new_fields = $models->execute_kw($db, $uid, $password, 'stock.picking', 'read', array(array()));


        if($delivery) {
            return response()->json(['success' => true, 'message' => 'Delivery created successfully','add_fields'=>$add_fields,'new_record'=>$new_record,'id_model'=>$id_model,'arr_products',$arr_products,'delivery:'=>$delivery,'partner_id' =>$partner_id[0]['id'],'picking_type_id'=>$type_operation[0]['id'],'location'=>$location ,'location_dest_id'=>$location_dest_id,'partner_id'=>$partner_id[0]['id']]);

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
