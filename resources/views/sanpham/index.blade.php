@extends('layouts.master')
@section('title', 'San Pham')

@section('content')
    <a href="{{route('sanpham.create')}}" class="btn btn-primary">Thêm mới</a>
    <a href="{{route('sanpham.thungrac')}}" class="btn btn-danger">Thùng rác</a>
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
        @foreach($data as $item)
            <tr>
                <td>{{$item->id}}</td>
                <td>{{$item->ten_san_pham}}</td>
                <td>

                </td>
                <td>{{$item->danhMuc ? $item->danhMuc->ten_danh_muc : 'Không có danh mục'}}</td>
                <td>{{$item->noi_dung}}</td>
                <td>
                    @foreach($item->theSanPham as $the)
                        <li>{{$the->ten_the}}</li>
                    @endforeach
                </td>
                <td>
                    <a href="{{route('sanpham.edit', $item->id)}}" class="btn btn-primary">Sửa</a>
                    <form action="{{route('sanpham.destroy', $item->id)}}" method="post">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
