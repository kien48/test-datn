<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnhBienThe;
use App\Models\BienTheSanPham;
use App\Models\DanhMuc;
use App\Models\KichThuocBienThe;
use App\Models\MauSacBienThe;
use App\Models\SanPham;
use App\Models\The;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SanPhamController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm (API công khai).
     */
    public function index()
    {
        try {
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

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Lấy dữ liệu thành công',
                'data' => $data,
            ], 200);
        }catch (Exception $e){
            return response()->json([
                'status' => true,
                'status_code' => 500,
                'message' => 'Đã có lỗi xảy ra khi lấy dữ liệu',
                'data' => $data,
            ], 500);
        }
    }

    /**
     * Tạo sản phẩm mới (API công khai).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten_san_pham' => 'required|string|max:255',
            'anh_san_pham' => 'required',
            'mo_ta_ngan' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'id_danh_muc' => 'required|integer',
            'the' => 'required|array',
            'bien_the' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $dataSanPham = $request->except('bien_the', 'the');
        $dataSanPham['ma_san_pham'] = random_int(1000, 9999) . random_int(1000, 9999);
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
                    'anh' => $value['anh']
                ];
            }
        }

        try {
            DB::beginTransaction();

            // Tạo sản phẩm
            $sanPham = SanPham::create($dataSanPham);

            // Tạo biến thể sản phẩm và ảnh
            foreach ($bienTheSanPham as $bienThe) {
                $anhBienThe = $bienThe['anh'];
                unset($bienThe['anh']);

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

            return response()->json([
                'status' => true,
                'message' => 'Sản phẩm đã được tạo thành công!',
                'data' => $sanPham
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'Đã xảy ra lỗi khi tạo sản phẩm.',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Hiển thị thông tin sản phẩm (API công khai).
     */
    public function show($id)
    {
        try {
            $sanPham = SanPham::with('danhMuc', 'bienTheSanPham.anhBienThe', 'theSanPham')
                ->findOrFail($id);

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Lấy dữ liệu thành công',
                'data' => $sanPham
            ], 200);
        }catch (Exception $e){
            return response()->json([
                'status' => false,
                'status_code' => 200,
                'message' => 'Đã xảy ra lỗi khi lấy dữ liệu',
            ], 500);
        }
    }

    /**
     * Cập nhật sản phẩm (API công khai).
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ten_san_pham' => 'required|string|max:255',
            'anh_san_pham' => 'required',
            'mo_ta_ngan' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'id_danh_muc' => 'required|integer',
            'the' => 'required|array',
            'bien_the' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

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
                    'anh' => $value['anh']
                ];
            }
        }

        try {
            DB::beginTransaction();

            // Cập nhật sản phẩm
            $sanPham->update($dataSanPham);

            // Xử lý biến thể sản phẩm
            foreach ($bienTheSanPham as $bienThe) {
                $anhBienThe = $bienThe['anh'];
                unset($bienThe['anh']);

                // Cập nhật hoặc tạo mới biến thể
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
                foreach ($anhBienThe as $anh) {
                    if ($anh != null) {
                        AnhBienThe::create([
                            'id_bien_the' => $bienTheSP->id,
                            'duong_dan_anh' => $anh
                        ]);
                    }
                }
            }

            // Cập nhật thẻ sản phẩm
            $sanPham->theSanPham()->sync($theSanPham);

            DB::commit();

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Sản phẩm đã được cập nhật thành công!',
                'data' => $sanPham
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật sản phẩm.',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa sản phẩm (API công khai).
     */
    public function destroy($id)
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

            $sanPham->theSanPham()->sync([]);
            $sanPham->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Sản phẩm đã được xóa thành công.'
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Xóa sản phẩm thất bại!',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Hiển thị danh sách sản phẩm đã xóa (API công khai).
     */
    public function danhSachSanPhamDaXoa()
    {
        try {
            $sanPhamDaXoa = SanPham::onlyTrashed()->with(['danhMuc'])->orderBy('deleted_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'status_code' => 200,
                'message' => 'Lấy dữ liệu thành công',
                'data' => $sanPhamDaXoa,
            ], 200);
        }catch (\Exception $exception){
            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Lấy dữ liệu thất bại!',
                'error' => $exception->getMessage()
            ], 500);
        }
    }
}
