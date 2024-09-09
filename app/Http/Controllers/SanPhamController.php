<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AnhBienThe;
use App\Models\BienTheSanPham;
use App\Models\DanhMuc;
use App\Models\KichThuocBienThe;
use App\Models\MauSacBienThe;
use App\Models\SanPham;
use App\Models\The;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SanPhamController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    const PATH_VIEW = 'sanpham.';

    public function index()
    {
        $data = SanPham::query()
            ->with([
                'danhMuc',
                'bienTheSanPham.anhBienThe',
                'bienTheSanPham.mauBienThe',
                'bienTheSanPham.kichThuocBienThe',
                'theSanPham'
            ])
            ->orderByDesc('id')
            ->get();
//       dd($data);
        return view(self::PATH_VIEW . __FUNCTION__, compact('data'));
    }

    public function create()
    {
        $danhMuc = DanhMuc::query()->orderByDesc('id')->get();
        $the = The::query()->orderByDesc('id')->get();
        $mau = MauSacBienThe::query()->pluck('ma_mau_sac', 'id')->all();
        $kichThuoc = KichThuocBienThe::query()->pluck('ten_kich_thuoc', 'id')->all();
        return view(self::PATH_VIEW . __FUNCTION__, compact('danhMuc', 'the', 'mau', 'kichThuoc'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ten_san_pham' => 'required|string|max:255',
            'anh_san_pham' => 'required',
            'mo_ta_ngan' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'id_danh_muc' => 'required|integer',
            'the' => 'required|array',
            'bien_the' => 'required|array',
        ]);
        $dataSanPham = $request->except('bien_the', 'the');
        $dataSanPham['ma_san_pham'] = random_int(1000, 9999) . random_int(1000, 9999);
        $dataSanPham['duong_dan'] = Str::slug($dataSanPham['ten_san_pham']);
        $theSanPham = $request->the;
        $bienTheSanPhamTmp = $request->bien_the;

        $bienTheSanPham = [];
        $anhBienTheTmp = [];

        foreach ($bienTheSanPhamTmp as $key => $value) {
            $tmp = explode('-', $key);
            if ($value['gia_ban'] != null && $value['so_luong'] != null) {
                $bienTheSanPham[] = [
                    'id_mau_sac_bien_the' => $tmp[0],
                    'id_kich_thuoc_bien_the' => $tmp[1],
                    'gia_ban' => $value['gia_ban'],
                    'gia_khuyen_mai' => $value['gia_khuyen_mai'],
                    'so_luong' => $value['so_luong'],
                    'anh' => $value['anh'] // Lưu thông tin ảnh cho mỗi biến thể
                ];
            }
        }

        try {
            DB::beginTransaction();

            // Tạo sản phẩm
            $sanPham = SanPham::create($dataSanPham);

            // Tạo biến thể và lưu ảnh tương ứng
            foreach ($bienTheSanPham as $bienThe) {
                $anhBienThe = $bienThe['anh'];  // Lưu ảnh riêng trước khi xoá khỏi mảng
                unset($bienThe['anh']);  // Xoá ảnh khỏi mảng để lưu vào bảng `BienTheSanPham`

                // Tạo biến thể sản phẩm
                $bienTheSP = BienTheSanPham::query()->create(array_merge($bienThe, ['id_san_pham' => $sanPham->id]));

                // Lưu ảnh biến thể
                if (!empty($anhBienThe)) {
                    foreach ($anhBienThe as $anh) {
                        if ($anh != null) {
                            AnhBienThe::query()->create([
                                'id_bien_the' => $bienTheSP->id,
                                'duong_dan_anh' => $anh
                            ]);
                        }
                    }
                }
            }

            // Đồng bộ thẻ sản phẩm
            $sanPham->theSanPham()->sync($theSanPham);

            DB::commit();
            return redirect()->route('sanpham.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            return back()->withErrors($exception->getMessage())->withInput();
        }
    }



    /**
     * Display the specified resource.
     */
    public function edit(int $id)
    {
        $danhMuc = DanhMuc::query()->orderByDesc('id')->get();
        $the = The::query()->orderByDesc('id')->get();
        $mau = MauSacBienThe::query()->pluck('ma_mau_sac', 'id')->all();
        $kichThuoc = KichThuocBienThe::query()->pluck('ten_kich_thuoc', 'id')->all();

        // Sử dụng with() trước findOrFail()
        $sanPham = SanPham::with('danhMuc', 'bienTheSanPham.anhBienThe','bienTheSanPham.mauBienThe', 'bienTheSanPham', 'theSanPham')
            ->findOrFail($id);
        $mangThe = $sanPham->theSanPham->pluck('id')->all();
        return view(self::PATH_VIEW . __FUNCTION__, compact('danhMuc', 'the', 'mau', 'kichThuoc', 'sanPham', 'mangThe'));
    }



    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, int $id)
    {

        $validatedData = $request->validate([
            'ten_san_pham' => 'required|string|max:255',
            'anh_san_pham' => 'required',
            'mo_ta_ngan' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'id_danh_muc' => 'required|integer',
            'the' => 'required|array',
            'bien_the' => 'required|array',
        ]);
        // Tìm sản phẩm theo id
        $sanPham = SanPham::findOrFail($id);

        $dataSanPham = $request->except('bien_the', 'the');
        $dataSanPham['duong_dan'] = Str::slug($dataSanPham['ten_san_pham']);
        $theSanPham = $request->the;
        $bienTheSanPhamTmp = $request->bien_the;

        $bienTheSanPham = [];

        foreach ($bienTheSanPhamTmp as $key => $value) {
            $tmp = explode('-', $key);
            if ($value['gia_ban'] != null && $value['so_luong'] != null) {
                $bienTheSanPham[] = [
                    'id_mau_sac_bien_the' => $tmp[0],
                    'id_kich_thuoc_bien_the' => $tmp[1],
                    'gia_ban' => $value['gia_ban'],
                    'gia_khuyen_mai' => $value['gia_khuyen_mai'],
                    'so_luong' => $value['so_luong'],
                    'anh' => $value['anh'] // Giữ lại thông tin ảnh
                ];
            }
        }

        try {
            DB::beginTransaction();

            // Cập nhật sản phẩm
            $sanPham->update($dataSanPham);

            // Cập nhật biến thể và ảnh biến thể
            foreach ($bienTheSanPham as $bienThe) {
                $anhBienThe = $bienThe['anh'];  // Tách ảnh ra
                unset($bienThe['anh']);  // Xóa ảnh khỏi mảng

                // Cập nhật hoặc tạo mới biến thể sản phẩm
                $bienTheSP = BienTheSanPham::updateOrCreate(
                    [
                        'id_san_pham' => $sanPham->id,
                        'id_mau_sac_bien_the' => $bienThe['id_mau_sac_bien_the'],
                        'id_kich_thuoc_bien_the' => $bienThe['id_kich_thuoc_bien_the']
                    ],
                    $bienThe
                );

                // Xóa ảnh cũ và thêm ảnh mới
                AnhBienThe::where('id_bien_the', $bienTheSP->id)->delete();
                if (!empty($anhBienThe)) {
                    foreach ($anhBienThe as $anh) {
                        if ($anh != null) {
                            AnhBienThe::create([
                                'id_bien_the' => $bienTheSP->id,
                                'duong_dan_anh' => $anh
                            ]);
                        }
                    }
                }
            }

            // Cập nhật thẻ sản phẩm
            $sanPham->theSanPham()->sync($theSanPham);

            DB::commit();
            return redirect()->route('sanpham.index')->with('success', 'Cập nhật sản phẩm thành công!');
        } catch (\Exception $exception) {
            DB::rollBack();
            return back()->withErrors($exception->getMessage())->withInput();
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            DB::beginTransaction();

            $sanPham = SanPham::with(['bienTheSanPham.anhBienThe', 'theSanPham'])->findOrFail($id);

            foreach ($sanPham->bienTheSanPham as $bienThe) {
                foreach ($bienThe->anhBienThe as $anh) {
                    $anh->delete();
                }
                $bienThe->delete();
            }

            $sanPham->delete();

            DB::commit();

            return redirect()->route('sanpham.index')->with('success', 'Sản phẩm đã được xóa thành công.');
        } catch (\Exception $exception) {
            DB::rollBack();
            return back()->withErrors($exception->getMessage())->withInput();
        }
    }

    public function danhSachSanPhamDaXoa()
    {
        $sanPhamDaXoa = SanPham::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view(self::PATH_VIEW .'thungrac', compact('sanPhamDaXoa'));
    }

    public function khoiPhucSanPham(int $id)
    {
        try {
            DB::beginTransaction();

            // Tìm sản phẩm đã bị xóa mềm
            $sanPham = SanPham::onlyTrashed()->with(['bienTheSanPham', 'theSanPham'])->findOrFail($id);

            // Khôi phục sản phẩm
            $sanPham->restore();

            // Khôi phục các biến thể của sản phẩm
            foreach ($sanPham->bienTheSanPham()->onlyTrashed()->get() as $bienThe) {
                // Khôi phục biến thể
                $bienThe->restore();

                // Khôi phục các ảnh biến thể (nếu cần)
                $bienThe->anhBienThe()->onlyTrashed()->restore();
            }


            DB::commit();
            return redirect()->route('sanpham.index')->with('success', 'Sản phẩm đã được khôi phục thành công.');
        } catch (\Exception $exception) {
            DB::rollBack();
            return back()->withErrors($exception->getMessage())->withInput();
        }
    }



}
