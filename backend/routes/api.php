<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;


Route::get('/test', function (Request $request) {
    return response()->json([
        'message' => 'Backend çalışıyor 🎉',
    ]);
});


Route::get('/redis-test', function () {
    try {
        // 60 saniyelik bir key yaz
        Redis::set('test-key', 'Merhaba Redis', 'EX', 60);

        // key reread 
        $value = Redis::get('test-key');

        return response()->json([
            'status' => 'ok',
            'value'  => $value,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});
