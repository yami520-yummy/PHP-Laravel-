<!--所有心情隨筆的列表畫面-->

<!-- 指定繼承 layout.master 母模板 -->
@extends('layout.master')

<!-- 傳送資料到母模板，並指定變數為title -->
@section('title', $title)

<!-- 傳送資料到母模板，並指定變數為content -->
@section('content')
<!-- 自動產生 csrf_token 隱藏欄位-->
{!! csrf_field() !!}

<div class="normal_form">
    <div class="form_title">心情隨筆列表</div>
    <div class="btn_group">
        <button type="button" class="btn btn-primary btn_form" onclick="AddData()">新增</button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover form_label">
            <thead>
                <tr>
                    <th>日期</th>
                    <th>內容</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($mindPaginate as $data)
                <tr>
                    <td>{{ $data->created_at }}</td>
                    <td>{{ $data->content }}</td>
                    <td class="right">
                    <button type="button" class="btn btn-success btn_form" onclick="EditData('{{ $data->id }}')">修改</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{-- 分頁頁數按鈕 --}}
        {{ $mindPaginate->links() }}
    </div>
<div>

<link href="/css/iao-alert.css" rel="stylesheet" type="text/css" />
<script src="/js/iao-alert.jquery.js"></script>

<script>
    function AddData()
    {
        //按下"新增"按鈕 藉著link會路由到mind.blade.php(可新增或取消一筆資料)
        location.href = "/admin/mind/add";
    }
    //編輯心情隨筆
    function EditData(id)
    {
        location.href = "/admin/mind/" + id + "/edit";
    }

    $( document ).ready(function() {
        <?PHP
            if(isset($result) && $result == "success") // 這裡將能正確讀取到 $result
            {
                echo('Success("修改資料成功!")');
            }
        ?>
    });

    //顯示吐司訊息
    function Success(message)
    {
        $.iaoAlert({
            type: "success",
            mode: "dark",
            msg: message,
        })
    }
</script>
@endsection