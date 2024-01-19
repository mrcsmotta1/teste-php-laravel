<!-- Em resources/views/import/import.blade.php -->

@extends('index')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Upload de Arquivo</div>

            <div class="card-body">
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
