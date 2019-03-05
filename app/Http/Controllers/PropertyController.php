<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PropertyService;

class PropertyController extends Controller
{


    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->propertyService->list();

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {



        $this->validate($request, [
            'address.address_line_1' => 'required',
            'address.address_line_2' => 'required',
            'address.city' => 'required',
            'address.post_code' => 'required',
        ]);

        return $this->propertyService->store($request);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return  $this->propertyService->show($id);

    }


}
