<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver; 

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Driver::query(); //bos bir query builder olusturdum SELECT * FROM drivers gibi ama calistirmiyorum daha 

        if ($request->has('status') && $request->status) { //eger requeste status parameter geldiyse filtre uygula
            $statuses = (array) $request->status;
            $query->whereIn('status', $statuses);
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate(20)); //en son eklenen en ustte, her pagede 20 record doner 
        //frontend'de data.data -> asil kayitlar, data.meta -> sayfalama infosu 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:150', //zorunlu 
            'phone'        => 'nullable|string|max:50',
            'license_type' => 'nullable|string|max:50',
            'status'       => 'nullable|in:active,inactive', //sadeece active ve inactive olabilir
        ]);

        $driver = Driver::create($data); //$fillable ile bu alanlar toplu atanabiliyor 

        return response()->json($driver, 201); //yeni olusan record frontende gonderiliyor 
    }

    /**
     * Display the specified resource.
     */
    public function show(Driver $driver)
    {
        //Route: GET /api/drivers/{id}
        //Parametre: Driver $driver
        return response()->json($driver); 
        //1- URL'deki id'yi aliyor
        //2- Driver modelinden id'ye gore record buluyor
        //3- otomatik olarak $driver parameter inject ediyor
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Driver $driver)
    {
        $data = $request->validate([
            'name'         => 'sometimes|required|string|max:150', //sometimes|required -> eger name field'ı request'te varsa bos olamaz ama hic gondermeyebiliriz o zaman degistirilmez 
            'phone'        => 'nullable|string|max:50',
            'license_type' => 'nullable|string|max:50',
            'status'       => 'nullable|in:active,inactive',
        ]);

        $driver->update($data); //sadece gelen field'lar update edilir 
                                //guncellenmis halini JSON doner 

        return response()->json($driver);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver)
    {
        $driver->delete(); //db'den siler 

        return response()->json(null, 204);
    }
}
