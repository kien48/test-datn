<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use Illuminate\Http\Request;

class DanhMucController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $dataCatelogue = $request->all();
            DanhMuc::query()->create($dataCatelogue);
            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Thêm mới dữ liệu thành công',
            ], 200);
        }catch (\Exception $exception){
            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'Thêm mới dữ liệu thất bại',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DanhMuc $danhMuc)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DanhMuc $danhMuc)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DanhMuc $danhMuc)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DanhMuc $danhMuc)
    {
        //
    }
}
