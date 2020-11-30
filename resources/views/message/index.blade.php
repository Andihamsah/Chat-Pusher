<div class="message-wrapper">
    <ul class="messages">
        @foreach ($message as $pesan)
        {{-- jika message from id sama dengan auth id maka itu "pengirim" dari user yg login --}}
        <li class="message clearfix">
            <div class="{{($pesan->from == Auth::id() ? 'send':'received')}}">
                <p>{{$pesan->message}}</p>
                <p class="date">{{date("D, d M Y"), strtotime($pesan->created_at)}}</p>
            </div>
        </li>
        @endforeach
    </ul>
</div>
<div class="input-text">
    <input type="text" name="message" class="submit">
</div>