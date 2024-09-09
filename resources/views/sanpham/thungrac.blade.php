@extends('layouts.master')
@section('title', 'San Pham')

@section('content')
    <a href="{{route('sanpham.index')}}" class="btn btn-primary">Danh sách</a>
    @if($errors->any())
        @foreach($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{$error}}
            </div>
        @endforeach
    @endif
    <table class="table">
        <tr>
            <th>ID</th>
            <th>Tên sản phẩm</th>
            <th>Ảnh</th>
            <th>Danh mục</th>
            <th>Nội dung</th>
            <th>Thẻ</th>
            <th>Thao tác</th>
        </tr>
        @foreach($sanPhamDaXoa as $item)
            <tr>
                <td>{{$item->id}}</td>
                <td>{{$item->ten_san_pham}}</td>
                <td>

                </td>
                <td>{{$item->danhMuc->ten_danh_muc}}</td>
                <td>{{$item->noi_dung}}</td>
                <td>
                    @foreach($item->theSanPham as $the)
                        <li>{{$the->ten_the}}</li>
                    @endforeach
                </td>
                <td>
                    <form action="{{route('sanpham.khoiphuc', $item->id)}}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-danger">Khôi phục</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
