<?php

namespace App\Http\Controllers;

use Ripcord\Ripcord;

use App\Models\products;
use App\Http\Requests\StoreproductsRequest;
use App\Http\Requests\UpdateproductsRequest;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
    public function store(StoreproductsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\products  $products
     * @return \Illuminate\Http\Response
     */
    public function show(request $id)
    {

        //serch product by id
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
        $products = $models->execute_kw($db, $uid, $password, 'product.template', 'search_read', array(), array('fields' => array('name','qty_available')));
        $product = $models->execute_kw($db, $uid, $password, 'product.template', 'search_read', array(), array('fields' => array('name','qty_available'),'domain'=>array('id'=>array($id->id))));
        return response()->json($product);
   
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
