<!-- Name Field -->
<!-- <div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    <p>{{ $role->name }}</p>
</div> -->

<div class="form-group">
    {!! Form::label('Created_at', 'Created_At:') !!}
    <p>{{ $role->created_at->format('D d, M, Y') }}</p>
</div>

@include('users.table');

