@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="user-wrapper">
                <ul class="users">
                    @foreach ($user as $users)
                    <li class="user" id="{{$users->id}}">
                        <span class="pending">1</span>

                        <div class="media">
                            <div class="media-left">
                                <img src="{{$users->avatar}}" alt="" class="media-object" style="height: 80px">
                            </div>

                            <div class="media-body">
                                <p class="name">{{$users->name}}</p>
                                <p class="email">{{$users->email}}</p>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="col-md-8" id="messages">

        </div>
    </div>
</div>
@endsection
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    var receiver_id = "";
    var my_id       = {{Auth::id()}};

    $(document).ready(function () {
        // ajax setup from csrf token

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('9625e2ea60c972431c47', {
        cluster: 'ap1'
        });

        var channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function(data) {
        alert(JSON.stringify(data));
        });

        $('.user').click(function () {
            $('.user').removeClass('active');
            $(this).addClass('active');

            receiver_id = $(this).attr('id')
            $.ajax({
                type: "get",
                url: "message/" + receiver_id,
                data: "",
                cache: false,
                success: function (data) {
                    $('#messages').html(data);
                }
            })
        })

        $(document).on('keyup', '.input-text input', function (e) {
            var message = $(this).val();

            // periksa apakah tombol enter ditekan dan pesan tidak nol juga penerima dipilih
            if (e.keyCode == 13 && message != '' && receiver_id != '') {
                $(this).val('');//saat ditekan masuk kotak teks akan kosong

                var datastr = "receiver_id=" + receiver_id + "&message=" + message

                $.ajax({
                    type: "post",
                    url: "message",  //need to create this post route
                    data: datastr,
                    cache: false,
                    success: function (data) {
                        
                    },
                    error: function(jqXHR, status, err) {

                    },
                    complete: function() {

                    }

                })
            }
        })
    })
</script>

@endsection






























