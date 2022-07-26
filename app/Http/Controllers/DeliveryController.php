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

        $info_delivery = $request->input('info_delivery');
        $scheduled_date = $request->input('scheduled_date');
        $order_number = $request->input('order_number');
        $picking_type_id = "ui-id-74";
        $id = $models->execute_kw($db, $uid, $password, 'stock.picking', 'create', array(array('partner_id' => $info_delivery,'picking_type_id'=>$picking_type_id,'scheduled_date' => $scheduled_date, 'origin' => $order_number)));
         
        if($id) {
            return response()->json(['success' => true, 'message' => 'Delivery created successfully',$id]);
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
