<?php

namespace App\Contracts;

use Illuminate\Http\Request;;

interface PropertyServiceInterface
{


    /**
     * @return mixed
     */
    public function list();


    /**
     * @param $request
     * @return mixed
     */
    public function store($request);


    /**
     * @param $id
     * @return mixed
     */
    public function show($id);


}
