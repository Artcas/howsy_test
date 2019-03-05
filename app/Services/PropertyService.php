<?php

namespace App\Services;

use App\Contracts\PropertyServiceInterface;
use App\Models\Property;
use App\Models\Address;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
class PropertyService implements PropertyServiceInterface
{

    /**
     * @return array|mixed
     */
    public function list()
    {

        $properties = Property::all();

        if ($properties->first()) {
            $entries = [];
            foreach ($properties as $property) {
                $entry = $this->parseData($property);
                $entries[] = $entry;
            }
            $response = [
                'msg' => 'Properties successfully retrieved.',
                'data' => $entries
            ];
        } else {
            $response = [
                'msg' => 'No properties found.'
            ];
        }
        return response()->json($response, 201);
    }


    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function store($request)
    {

        $address_line_1 = $request->input('address.address_line_1');

        $address_line_2 = $request->input('address.address_line_2');

        $city = $request->input('address.city');

        $post_code = $request->input('address.post_code');

        $address = new Address([
            'address_line_1' => $address_line_1,
            'address_line_2' => $address_line_2,
            'city' => $city,
            'post_code' => $post_code
        ]);

        $parsedAddress = $address_line_1 . '+' . $address_line_2 . '+' . $city . '+' . $post_code;

        $key = 'AIzaSyBoeCePI1TR155iXXn1ePy4JruJQq45sL8';

        $client = new Client();

        $result = $client->post("https://maps.googleapis.com/maps/api/geocode/json?address=$parsedAddress&key=$key")->getBody();

        $json = \GuzzleHttp\json_decode($result);



        $longitude = $json->results[0]->geometry->location->lat;

        $latitude = $json->results[0]->geometry->location->lng;

        $property = new Property([
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);

        DB::beginTransaction();

        try {
            $address->save();

            $savedAddress = Address::with('property')->findOrFail($address->id);
            if ($savedAddress->property === null) {
                $savedAddress->property()->save($property);
            }
            $data = [
                'latitude' => $property->latitude,
                'longitude' => $property->longitude,
                'address' => [
                    'address_line_1' => $address->address_line_1,
                    'address_line_2' => $address->address_line_2,
                    'city' => $address->city,
                    'post_code' => $address->post_code
                ]
            ];
            $response = [
                'msg' => 'Successfully saved the property.',
                'data' => $data
            ];
            $status = 201;
        }
        catch (\Exception $e) {
            $response = [
                'msg' => 'Something went wrong.',
                'error_msg' => $e->getMessage()
            ];
            $status = 404;
        }

        DB::commit();

        return response()->json($response, $status);
    }


    /**
     * @param $id
     * @return array|mixed
     */
    public function show($id)
    {
        $property = Property::find($id);
        if (!is_null($property)) {
            $entry = $this->parseData($property);
            $response = [
                'msg' => 'Property successfully retrieved.',
                'data' => $entry
            ];
        } else {
            $response = [
                'msg' => 'No property found with that property ID.'
            ];
        }

        return response()->json($response, 201);
    }


    /**
     * @param $property
     * @return array
     */
    private function parseData($property)
    {

        $address = Address::find($property->address_id);

        $parsedAddress = [
            'address_line_1' => $address->address_line_1,
            'address_line_2' => $address->address_line_2,
            'city' => $address->city,
            'post_code' => $address->post_code
        ];
        $longitude = $property->longitude;
        $latitude = $property->latitude;
        $entry = [
            'address' => $parsedAddress,
            'latitude' => $latitude,
            'longitude' => $longitude
        ];
        return $entry;
    }

}
