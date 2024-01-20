<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\ImportFile\ProcessQueueController;

class ProcessQueueControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $logs = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->logs = [];
        Log::shouldReceive('error')->andReturnUsing(function ($message) {
            $this->logs[] = $message;
        });
    }

    public function testProcessDocumentWithInvalidCategory()
    {

        new ProcessQueueController();

        $data = [
            'categoria' => 'CategoriaInválida',
            'titulo' => 'Título qualquer',
            'conteúdo' => 'Conteúdo do documento',
        ];

        $exercicio = '2023';

        // Criando um mock para simular a chamada ao método getCategoryId
        $mock = Mockery::mock(ProcessQueueController::class)->makePartial();
        $mock->shouldNotReceive('getCategoryId'); // Não deve chamar getCategoryId

        $result = $mock->processDocument($data, $exercicio);

        $this->assertFalse($result);

        $msg = "Categoria inválida: " . strtolower($data['categoria']);
        $this->assertStringContainsString($msg, end($this->logs));
    }


    public function testProcessDocumentWithInvalidRemessaTitle()
    {
        $controller = new ProcessQueueController();

        $data = [
            'categoria' => 'Remessa',
            'titulo' => 'Título sem Semestre',
            'conteúdo' => 'Conteúdo do documento',
        ];

        $exercicio = '2023';

        $result = $controller->processDocument($data, $exercicio);

        $this->assertFalse($result);
    }

    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
