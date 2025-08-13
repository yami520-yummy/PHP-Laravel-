<?PHP use App\Enum\ESexType; ?>
<!-- 後台-自我介紹頁面 -->
<!-- 指定繼承 layout.master 母模板 -->
@extends('layout.master')

<!-- 傳送資料到母模板，並指定變數為title -->
@section('title', $title)

<!-- 傳送資料到母模板，並指定變數為content -->
@section('content')
<form id="form1" method="post" action="" enctype="multipart/form-data">
<!-- 自動產生 csrf_token 隱藏欄位-->
{!! csrf_field() !!}

<div class="normal_form">
    <div class="form_title">自我介紹</div>

    <div class="col-sm-6">
    <div class="form_label">帳號</div>
        <div class="form_textbox_region">
            <input name="account" class="form_textbox" type="text" value="{{ $User->account }}" readonly="true" placeholder="請輸入帳號"/>
        </div>
    </div>

    <div class="div_clear"/>
    <div class="col-sm-2">
        <div class="form_label">性別</div>
        <div class="form_textbox_region">
            <select class="form_select" id="sex" name="sex" placeholder="請選擇性別">
                <option value="{{ ESexType::MALE }}" 
                @if(old('sex', $User->sex) == ESexType::MALE)
                    selected
                @endif
                >男性</option>
                <option value="{{ ESexType::FEMALE }}" 
                @if(old('sex', $User->sex) == ESexType::FEMALE)
                    selected
                @endif
                >女性</option>
            </select>
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form_label">身高</div>
        <div class="form_textbox_region">
            <input name="height" class="form_textbox" type="number" value="{{ old('height') }}" placeholder="請輸入身高"/>
        </div>
    </div>

     <div class="col-sm-2">
        <div class="form_label">體重</div>
        <div class="form_textbox_region">
            <input name="weight" class="form_textbox" type="number" value="{{ old('weight') }}" placeholder="請輸入體重"/>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form_label">興趣</div>
        <div class="form_textbox_region">
            <input name="interest" class="form_textbox" type="text" value="{{ old('interest') }}"  placeholder="請輸入興趣"/>
        </div>
    </div>

    <div class="div_clear"/>

     <div class="col-sm-6">
        <div class="form_label">
            圖片
            <input type="file" name="file" id="file" class="inputfile" />
            <label for="file">上傳圖片</label>
        </div>
        <div class="form_textbox_region">
            <img id="file_review" class="upload_img" 
            @if($User->picture == "")
                src="/images/nopic.png" 
            @else
                src="{{ $User->picture }}"
            @endif
            />
        </div>
     </div>
    
    <div class="col-sm-6">
        <div class="form_label">自我介紹</div>
        <div class="form_textbox_region">
            <textarea name="introduce" class="form_textarea"  placeholder="請輸入自我介紹">{{ old('introduce', $User->introduce) }}</textarea>
        </div>
    </div>

    
    <div class="form_error">
        <!-- 錯誤訊息模板元件 -->
        @include('layout.ValidatorError')
    </div>

    <div class="btn_group">
        <button type="submit" class="btn btn-primary btn_form">儲存</button>
    </div>

</div>
</form>

<link href="/css/iao-alert.css" rel="stylesheet" type="text/css" />
<script src="/js/iao-alert.jquery.js"></script>

<script>
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

//預覽圖片
$("#file").change(function(){//是一個事件監聽器。它表示當 id="file" 的檔案輸入框的選取檔案發生變化時（即使用者選擇了一個新的檔案），就執行後面的匿名函式。
      //當檔案改變後，做一些事 
     readURL(this);   // this代表<input id="file">
});

function readURL(input){
  if(input.files && input.files[0]){ //檢查檔案輸入框中是否有選取檔案，並且至少有一個檔案 
    var reader = new FileReader();//讀取使用者電腦上的檔案內容。
    reader.onload = function (e) {
        //e.target.result 會包含讀取到的檔案內容，對於圖片來說，這是一個 Base64 編碼的 Data URL
       $("#file_review").attr('src', e.target.result); //再次使用 jQuery，選取 id="file_review" 的圖片元素，並將其 src 屬性設定為 e.target.result，從而實現圖片的即時預覽。
    }
    reader.readAsDataURL(input.files[0]);// 這是實際開始讀取檔案的方法
  }
}
/*這個頁面包含性別、身高、體重、興趣、自我介紹及圖片上傳,
另外還有加上圖片預覽的功能,
讓使用者確認有沒有上傳錯檔案.*/

</script>

@endsection
