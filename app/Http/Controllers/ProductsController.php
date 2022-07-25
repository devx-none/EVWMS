<?php

namespace App\Http\Controllers;

use Ripcord\Ripcord;

use App\Models\products;
use App\Http\Requests\StoreproductsRequest;
use App\Http\Requests\UpdateproductsRequest;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //create token for the request
        

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


            
    
            //get list products from odoo
            $products = $models->execute_kw($db, $uid, $password, 'product.template', 'search_read', array(), array('fields' => array('name','qty_available')));
            
           return response()->json($products);


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
     * @param  \App\Http\Requests\StoreproductsRequest  $request
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
            $image = $request->input('image_1920');
            $description = $request->input('description');
            $price = $request->input('list_price');
            $cost = $request->input('standard_price');
            $weight = $request->input('weight');
            $volume = $request->input('volume');
            $quantity = $request->input('qty_available');
            $category = $request->input('categ_id');
            $reference = $request->input('default_code');
            $barcode = $request->input('barcode');
            $tags = $request->input('product_tag_ids');
            $uom = $request->input('uom_id');
            $uom_po = $request->input('uom_po_id');
            $expiration_date = $request->input('euse_expiration_date');
            $alert_time = $request->input('alert_time');
            $removal_time = $request->input('removal_time');
            $tracking = $request->input('tracking');
            $description_pickingin = $request->input('description_pickingin');
            $responsible = $request->input('responsible_id');



            $id = $models->execute_kw($db, $uid, $password, 'product.template', 'create', array(array('name'=>$name,'image'=>$image,'description'=>$description,'list_price'=>$price,'standard_price'=>$cost,'weight'=>$weight,'volume'=>$volume,'qty_available'=>$quantity,'categ_id'=>$category,'default_code'=>$reference,'barcode'=>$barcode,'product_tag_ids'=>$tags,'uom_id'=>$uom,'uom_po_id'=>$uom_po,'euse_expiration_date'=>$expiration_date,'alert_time'=>$alert_time,'removal_time'=>$removal_time,'tracking'=>$tracking,'description_pickingin'=>$description_pickingin,'responsible_id'=>$responsible)));

            return response()->json("product created");

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\products  $products
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        //Quantity available each product by id
        $url = env('RPC_URL');
        $db = env('RPC_DB');
        $username = env('RPC_USERNAME');
        $password = env('RPC_PASSWORD');
        $url_auth = $url . '/xmlrpc/2/common';
        $url_exec = $url . '/xmlrpc/2/object';
        $info = Ripcord::client('https://demo.odoo.com/start')->start();
        $common = Ripcord::client($url_auth);
        $ver = $common->version();
        $uid = $common->authenticate($db, $username, $password, array());
        $models = Ripcord::client($url_exec);
        $check = $models->execute_kw($db, $uid, $password, 'res.partner', 'check_access_rights', array('read'), array('raise_exception' => false));
        $fields = $models->execute_kw($db, $uid, $password, 'res.partner', 'fields_get', array(), array('fields' => array('string', 'help', 'type')));
        // $ids = $models->execute_kw($db, $uid, $password, 'product.template', 'search', array(array(array('id', '=', '1'))), array('fields'=>array('name', 'qty_available')));

        // $records = $models->execute_kw($db, $uid, $password, 'product.template', 'read', array($ids));
        
        $records  = $models->execute_kw($db, $uid, $password, 'product.template', 'search_read', array(array(array('id', '=', $id))), array('fields'=>array('qty_available'), 'limit'=>1));
      

        return response()->json($records);
   
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\products  $products
     * @return \Illuminate\Http\Response
     */
    public function edit(products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateproductsRequest  $request
     * @param  \App\Models\products  $products
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateproductsRequest $request, products $products)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\products  $products
     * @return \Illuminate\Http\Response
     */
    public function destroy(products $products)
    {
        //
    }
}
