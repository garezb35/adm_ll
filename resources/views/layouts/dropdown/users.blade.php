<ul class="dropdown-menu">
    <li><a class="dropdown-item"
           href="javascript: popupCenter('/user/edit?id={{$user->id}}', '회원정보수정', 800, 600)">{{$user->userid}}
            정보수정</a></li>
    <li>
        <hr class="dropdown-divider">
    </li>
    <li><a class="dropdown-item"
           href="javascript: popupCenter( '/user/send_message?id={{$user->id}}', '쪽지보내기', 800, 600)">쪽지보내기</a>
    </li>
    <li><a class="dropdown-item"
           href="javascript: popupCenter('/user/charge?id={{$user->id}}', '충전', 800, 600)">충전</a>
    </li>
    <li><a class="dropdown-item"
           href="javascript: popupCenter('/user/exchange?id={{$user->id}}', '환전', 800, 600)">환전</a>
    </li>
    @if (config('app.no_mini') == 0 && config('app.site_type') != 'PWB')
        <li><a class="dropdown-item"
               href="javascript: popupCenter('/user/casino/charge?id={{$user->id}}', '카지노알지급', 800, 600)">카지노알지급</a>
        </li>
        <li><a class="dropdown-item"
               href="javascript: popupCenter('/user/casino/excharge?id={{$user->id}}', '카지노알회수', 800, 600)">카지노알회수</a>
        </li>
    @endif
    <li>
        <hr class="dropdown-divider">
    </li>
    <li><a class="dropdown-item"
           href="javascript: popupCenter('/charge?username={{$user->userid}}', '충전내역', 800, 600)">충전내역</a>
    </li>
    <li><a class="dropdown-item"
           href="javascript: popupCenter('/excharge?username={{$user->userid}}', '충전내역', 800, 600)">환전내역</a>
    </li>
    @if (config('app.site_type') == 'BS')
        <li><a class="dropdown-item" href="javascript: popupCenter('/analysis/live/bet/info?userid={{$user->userid}}'
                                                    + '&gameType=live'
                                                    + '&gameItem=', '상세내역', 1200, 700)">배팅내역</a></li>
    @else
        <li><a class="dropdown-item" href="javascript: popupCenter('/analysis/live/bet/info?userid={{$user->userid}}'
                                                    + '&gameType=pwb'
                                                    + '&gameItem=', '상세내역', 1200, 700)">배팅내역</a></li>
    @endif
    <li><a class="dropdown-item" href="javascript: popupCenter( '/user/login_log?id={{$user->id}}', '로그인내역', 800, 600)">로그인내역</a></li>
    <li><a class="dropdown-item" href="javascript: popupCenter( '/user/info_log?id={{$user->id}}', '정보수정내역', 800, 600)">정보수정내역</a></li>
</ul>
