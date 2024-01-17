@extends('index')

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif


@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Processar Fila</div>
            <div class="card-body">
                <form id="process-form" action="{{ route('import.process.queue') }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-primary">Verificar e Processar Fila</button>

                </form>
            </div>
        </div>
    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#process-form').submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: "{{ route('import.check.queue') }}",
                type: 'GET',
                success: function(response) {
                    console.log('response: ', response);
                    if (response.jobsCount === 0) {
                        alert('Nenhum arquivo na fila para processar.');
                    } else {
                        alert(`${response.jobsCount} arquivo na fila para processar.`);
                        $('#process-form')[0].submit();
                    }
                },
                error: function() {
                    alert('Erro ao verificar a fila.');
                }
            });
        });
    });
</script>
