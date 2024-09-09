@extends('layouts.master')
@section('title', 'Thêm mới')

@section('content')
    <form action="{{ route('sanpham.store') }}" method="post" >
        @csrf
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="alert alert-danger" role="alert">
                    {{$error}}
                </div>
            @endforeach
        @endif
        <div class="card">
            <h3>Thông tin sản phẩm</h3>
            <div class="mt-3">
                <label for="ten_san_pham" class="form-label">Tên sản phẩm</label>
                <input type="text" name="ten_san_pham" class="form-control" id="ten_san_pham" placeholder="Tên sản phẩm">
            </div>
            <div class="mt-3">
                <label for="anh_san_pham" class="form-label">Ảnh sản phẩm</label>
                <input type="text" name="anh_san_pham" class="form-control" id="anh_san_pham" placeholder="Ảnh sản phẩm">
            </div>
            <div class="mt-3">
                <label for="anh_san_pham" class="form-label">Danh mục sản phẩm</label>
                <select name="id_danh_muc" id="" class="form-control">
                    @foreach($danhMuc as $item)
                        <option value="{{$item->id}}">{{$item->ten_danh_muc}}</option>
                    @endforeach
                </select>
            </div>
            <div class="mt-3">
                    <label for="ten_san_pham" class="form-label">Mô tả ngắn</label>
                <input type="text" name="mo_ta_ngan" class="form-control" id="ten_san_pham" placeholder="Mô tả ngắn">
            </div>
            <div class="mt-3">
                <label for="ten_san_pham" class="form-label">Nội dung</label>
                <textarea name="noi_dung" id="" cols="10" rows="5" class="form-control"></textarea>
            </div>

        </div>
        <div class="card">
            <h3>Biến thể sản phẩm</h3>
            <table class="table">
                <tr>
                    <th>Màu</th>
                    <th>Kích cỡ</th>
                    <th>Số lượng</th>
                    <th>Giá bán</th>
                    <th>Giá khuyến mãi</th>
                    <th>Ảnh</th>
                </tr>
                    <div>
                        @foreach($mau as $idMau=>$maMau)
                            @foreach($kichThuoc as $idKichThuoc=>$tenKichThuoc)
                                <tr>
                                    <td>
                                        <div style="height: 50px;width: 50px;background-color: {{$maMau}};border-radius: 5px;box-shadow: #051b11 0 0 5px"></div>
                                    </td>
                                    <td>
                                        {{$tenKichThuoc}}
                                    </td>
                                    <td>
                                        <input type="number" name="bien_the[{{$idMau.'-'.$idKichThuoc}}][so_luong]" class="form-control">
                                    </td>
                                    <td>
                                        <input type="number" name="bien_the[{{$idMau.'-'.$idKichThuoc}}][gia_ban]" class="form-control">
                                    </td>
                                    <td>
                                        <input type="number" name="bien_the[{{$idMau.'-'.$idKichThuoc}}][gia_khuyen_mai]" class="form-control">
                                    </td>
                                    <td>
                                        @for($y=1; $y<=5; $y++)
                                            <input type="text" name="bien_the[{{$idMau.'-'.$idKichThuoc}}][anh][{{$y}}]" class="form-control">
                                        @endfor
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </div>
            </table>
        </div>
        <div class="card">
            <h3>Thẻ sản phẩm</h3>
            <div class="mt-3">
                <select name="the[]" id="" class="form-control" multiple>
                    @foreach($the as $item)
                        <option value="{{$item->id}}">{{$item->ten_the}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <a href="{{route('sanpham.index')}}">Quay lại</a>
        <button type="submit" class="btn btn-primary">Tạo</button>
    </form>
@endsection
