<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>@yield('title')</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"> </script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        @vite(['resources/js/app.js', 'resources/sass/app.scss'])
    </head>
    <body>
        <div class="toolbar_section">
            <span class="toolbar_title">@yield('title')</span>
            <span class="toolbar_title2">小魚</span>
            <div class="toolbar_right">
                <span class="toolbar_text">
                    {{ $User != null ? $User->name."，您好！" : "未登入" }}
                </span>
            </div>
        </div>

        <div class="container">
            <div class="col-sm-1 form background_white">
                <ul class="nav nav-pills nav-stacked">
                <!-- 已登入且在後台 $page來判斷是否是後台,-->
                @if($page == "admin" && session()->has('user_id'))
                    <li 
                        @if($name == "user")
                            class="active"
                        @endif
                    >
                        <a href="/admin/user">自我介紹</a>
                    </li>

                    <!-- 心情隨筆 -->
                    <li 
                        @if($name == "mind")
                            class="active"
                        @endif
                    >
                        <a href="/admin/mind">心情隨筆</a>
                    </li>

                    <!-- 回到前台 -->
                    <li>
                        <a href="/">部落格(回到前台)</a>
                    </li>

                <!--進入部落格中的某一使用者之畫面，單一使用者的畫面我們用$page="user"來辨識-->
                @elseif($page == "user")
                    <!-- 首頁 -->
                    <li>
                        <a href="/">部落格</a>
                    </li>
                    <!-- 自我介紹 -->
                    <li 
                    @if($name == "user")
                        class="active"
                    @endif
                    >
                        <a href="/{{ $userData->id }}/user">自我介紹</a>
                    </li>
                    <!-- 心情隨筆 -->
                    <li 
                    @if($name == "mind")
                        class="active"
                    @endif
                    >
                        <a href="/{{ $userData->id }}/mind">心情隨筆</a>
                    </li>
                    <!-- 留言板 -->
                    <li 
                    @if($name == "board")
                        class="active"
                    @endif
                    >
                        <a href="/{{ $userData->id }}/board">留言板</a>
                    </li>
                @else <!-- 已登入在前台or尚未登入 -->
                    <!-- 首頁=部落格 -->
                    <li 
                        @if($name == "home")
                            class="active"
                        @endif
                    >
                    <a href="/">部落格</a>
                    </li>

                    @if(session()->has('user_id'))
                    <!-- 自我介紹 -->
                    <li>
                        <a href="/admin/user">進入後台</a>
                    </li>
                    @endif
                @endif 

                <!-- 登入後 無論是在哪 都要有"登出" -->
                @if(session()->has('user_id'))
                     <!-- 登出 -->
                    <li>
                        <a href="/user/auth/sign_out">登出</a>
                    </li>
                <!-- 尚未登入 -->
                @else 
                     <!-- 註冊 -->
                    <li 
                        @if($name == "sign_up")
                            class="active"
                        @endif
                        >
                        <a href="/user/auth/sign_up">註冊</a>
                    </li>
                    <!-- 登入 -->
                    <li 
                        @if($name == "sign_in")
                            class="active"
                        @endif
                    >
                        <a href="/user/auth/sign_in">登入</a>
                    </li>
                @endif
                
                </ul>
            </div>
            <!-- 以上都是所有共用的側邊列表和上方列表  -->
             <!-- 以下的內容都寫在各自的前端中  @section('content')..所有的內容..@endsection  -->
            <div class="col-sm-11 background_white2">
                @yield('content')
            </div>
        </div> <!-- container end-->
    </body>
</html>