<!-- Em resources/views/import/import.blade.php -->

@extends('index')



@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Upload de Arquivo</div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('import.upload.process') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="file">Selecione o Arquivo</label>
                        <input type="file" class="form-control-file" id="file" name="file" accept="json">
                    </div>

                    <button type="submit" class="btn btn-primary">Enviar para Fila</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@error('file')
    <div class="alert alert-danger">{{ $message }}</div>
@enderror
