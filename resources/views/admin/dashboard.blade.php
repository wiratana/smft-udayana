@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
        <div class="col-md-8 my-5">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-item-center">
                        <p>{{ __('List Commite') }}</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#committeeForm" id="BtnCreate" data-service="create">Add User</button>
                    </div>
                </div>

                <div class="card-body">
                    @if($committees)
                        <ul>
                            @foreach($committees as $committee)
                                <li>
                                    <div class="d-flex justify-content-between">
                                        <p>{{$committee->name}}</p>
                                        <div>
                                            <button class="btn btn-primary button-edit" data-id="{{ $committee->id }}" data-service="edit" data-bs-toggle="modal" data-bs-target="#committeeForm">Edit</button>
                                            <button class="btn-delete btn btn-danger" data-id="{{ $committee->id }}">Delete</button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-12">
    <div class="modal" tabindex="-1" id="changePasswordModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Ganti Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{route('admin.password.change')}}" method="POST">
            @csrf
            <div class="modal-body">
              <p>Harap melakukan pergantian password saat selesai Login</p>
              <div class="my-3">
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" class="form-control" id="password" name="password">
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>

<div class="col-12">
    <div class="modal" tabindex="-1" role="dialog" id="committeeForm" data-form="#formData">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Committee</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>    
          <form method="POST" id="formData">
            <input type="hidden" name="role" value="committee">
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Name : </label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="input your name ..." value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label for="email">Email : </label>
                    <input type="text" name="email" id="email" class="form-control" placeholder="input your email ..." value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label for="nik">NIK : </label>
                    <input type="text" name="nik" id="nik" class="form-control" placeholder="input your nik ..." value="{{ old('nik') }}">
                </div>
                <div class="form-group">
                    <label for="password">Password : </label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="input your password ..." value="{{ old('password') }}">
                </div>
                <div class="form-group">
                    <label for="image">Image : </label>
                    <input type="file" name="image" id="image" class="form-control-file">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="BtnSubmit">Save changes</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>
@endsection

@section('scripts')
    @if(Hash::check(Auth::user()->nik, Auth::user()->password) && Auth::user()->role == 'user')
        <script>
            $(document).ready(function(){
                $("#changePasswordModal").show();
            });
        </script>
    @endif

    <script>
        const committeeData = JSON.parse('{{$committees->toJson()}}'.replace(/&quot;/g,'"'));

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#committeeForm").on("show.bs.modal", function(e){
            var caller  = $(e.relatedTarget);
            var form    = $($(this).data("form"));
            form.attr("data-service", caller.data("service"));
            if (caller.data("service") == "edit") 
                form.attr("data-id", caller.data("id"));
        });

        $("#BtnCreate").click(function(e){
            e.preventDefault();
            $("#formData input[name='name']").val('');
            $("#formData input[name='email']").val('');
            $("#formData input[name='nik']").val('');
        });

        $(".button-edit").click(function(e){
            e.preventDefault();
            for(i = 0; i < committeeData.length; i++){
                if(committeeData[i]['id'] == $(this).data('id')){
                    $("#formData input[name='name']").val(committeeData[i]['name']);
                    $("#formData input[name='email']").val(committeeData[i]['email']);
                    $("#formData input[name='nik']").val(committeeData[i]['nik']);
                    break;
                }
            }
        });

        $("#BtnSubmit").click(function(e){
            e.preventDefault();

            var form = $("#formData");
            var data = new FormData(form[0]);
            var url  = "";

            if (form.data("service") == "create"){
                url  = "{{route('admin.user.store')}}";;
            } else {
                url  = "{{route('admin.user.update','')}}" + "/" + form.data("id");
                data.set('_method', 'PUT')
            }

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                cache: false,
                contentType:false,
                processData: false,
                success: function(data){
                    $("#committeeForm").modal("hide");
                    swal({
                        title: "Success!",
                        text: data.message,
                        icon: "success",
                        value: true
                    }).then(function(confirmed){
                        location.reload();
                    });
                },
                error: function(data){
                    var errors = data.responseJSON.errors;
                    var element = '<div class="error-message alert alert-danger m-2" role="alert">';
                    for(const error in errors){
                        element += '<li>' + errors[error] + '</li>';
                    }
                    element += '</div>';
                    $("#formData").prepend(element);
                }
            });
        });

        $(".btn-delete").click(function(e){
            $.ajax({
                type: 'DELETE',
                url: "{{ route('admin.user.destroy', '')}}" + '/' + $(this).data("id"),
                success: function(data){
                    swal({
                        title: "Success!",
                        text: data.message,
                        icon: "success",
                        value: true
                    }).then(function(confirmed){
                        location.reload();
                    });
                }
            });
        });
        
    </script>
@endsection
