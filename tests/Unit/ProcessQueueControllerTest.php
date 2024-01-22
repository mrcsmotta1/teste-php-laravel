<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
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

        Log::shouldReceive('info')->andReturnUsing(function ($message) {
            $this->logs[] = $message;
        });
    }


    public function testProcessDocumentWithInvalidCategory()
    {
        new ProcessQueueController();

        $data = [
            'categoria' => 'CategoriaInválida',
            'titulo' => 'Semestre 1',
            'conteúdo' => 'Conteúdo do documento',
        ];

        $exercicio = '2023';

        $mock = Mockery::mock(ProcessQueueController::class)->makePartial();
        $mock->shouldNotReceive('getCategoryId'); // Não deve chamar getCategoryId

        $result = $mock->processDocument($data, $exercicio);

        $this->assertFalse($result);

        $msg = "Categoria inválida: " . mb_strtolower($data['categoria']);
        $this->assertStringContainsString($msg, end($this->logs));
    }



    public function testProcessDocumentWithValidCategoryAndTitle()
    {
        Category::factory(2)->create();

        new ProcessQueueController();

        $data = [
            'categoria' => 'Remessa',
            'titulo' => 'semestre 1',
            'conteúdo' => 'Conteúdo do documento',
        ];

        $exercicio = '2023';

        $mock = Mockery::mock(ProcessQueueController::class)->makePartial();

        $result = $mock->processDocument($data, $exercicio);

        $resultBool = (bool)$result;

        $this->assertTrue($resultBool);

        $msg = "Documento id: {$result} salvo com sucesso!";
        $this->assertStringContainsString($msg, end($this->logs));
    }



    public function testProcessDocumentWithInvalidRemessaTitle()
    {
        $controller = new ProcessQueueController();

        $data = [
            'categoria' => 'Remessa',
            'titulo' => 'Título qualquer',
            'conteúdo' => 'Conteúdo do documento',
        ];

        $exercicio = '2023';

        $result = $controller->processDocument($data, $exercicio);

        $this->assertFalse($result);

        $titulo = mb_strtolower($data['titulo']);
        $msg = "Categoria Remessa inválida sem Semestre, Categoria: remessa -- Titulo: {$titulo}";
        $this->assertStringContainsString($msg, end($this->logs));
    }

    public function testProcessPartialDocumentWithValidTitle()
    {
        Category::factory(2)->create();

        new ProcessQueueController();

        $controller = new ProcessQueueController();

        $data = [
            'categoria' => 'Remessa Parcial',
            'titulo' => 'Janeiro',
            'conteúdo' => 'Conteúdo do documento',
        ];

        $exercicio = '2023';

        $result = $controller->processDocument($data, $exercicio);

        $resultBool = (bool)$result;
        $this->assertTrue($resultBool);

        $msg = "Documento id: {$result} salvo com sucesso!";
        $this->assertStringContainsString($msg, end($this->logs));
    }

    public function testProcessPartialDocumenWithtInvalidTitle()
    {
        Category::factory(2)->create();

        $controller = new ProcessQueueController();

        $data = [
            'categoria' => 'Remessa Parcial',
            'titulo' => 'Título qualquer',
            'conteúdo' => 'Conteúdo do documento',
        ];

        $exercicio = '2023';

        $result = $controller->processDocument($data, $exercicio);

        $this->assertFalse($result);

        $categoria = mb_strtolower($data['categoria']);
        $msg = "Categoria Remessa Parcial inválida sem nome de um mês válido, Categoria: {$categoria}";
        $this->assertStringContainsString($msg, end($this->logs));
    }

    public function testContentFieldHasMaximumCharactersValidation()
    {
        $maxSize = env('SIZE_MAX_CONTENT', 1);

        $maxTestSize = $maxSize + 1;

        $jsonData = [
            "documentos" => [
                [
                    "categoria" => "Remessa",
                    "titulo" => "Eget mi proin sed libero enim",
                    'conteúdo' => str_repeat('A', $maxTestSize)
                ]
            ]
        ];

        $fileContent = json_encode($jsonData);

        $file = UploadedFile::fake()->createWithContent('seu_arquivo.json', $fileContent);

        $response = $this->json('POST', '/import/upload',  ['file' => $file]);

        $response->assertStatus(302);

        $sessionErrors = session('errors')->getBag('default')->getMessages();

        $this->assertCount(1, $sessionErrors);

        $this->assertEquals(["O campo conteúdo deve ter no máximo {$maxSize} caracteres."], $sessionErrors[0]);
    }

    public function testContentFieldHasSentWithAllowedCharacterAmount()
    {
        $maxSize = env('SIZE_MAX_CONTENT', 2);

        $maxTestSize = $maxSize - 1;

        $jsonData = [
            "documentos" => [
                [
                    "categoria" => "Remessa",
                    "titulo" => "Eget mi proin sed libero enim",
                    'conteúdo' => str_repeat('A', $maxTestSize)
                ]
            ]
        ];

        $fileContent = json_encode($jsonData);

        $file = UploadedFile::fake()->createWithContent('seu_arquivo.json', $fileContent);

        $response = $this->json('POST', '/import/upload',  ['file' => $file]);

        $response->assertStatus(302);


        $successMessage = session('success');

        $response->assertStatus(302);
        $this->assertEquals('JSON enviado para processamento.', $successMessage);
    }

    public function testShouldNotAcceptFleOtherThanJson()
    {
        $maxSize = env('SIZE_MAX_CONTENT', 1);

        $maxTestSize = $maxSize + 1;

        $jsonData = [
            "documentos" => [
                [
                    "categoria" => "Remessa",
                    "titulo" => "Eget mi proin sed libero enim",
                    'conteúdo' => str_repeat('A', $maxTestSize)
                ]
            ]
        ];

        $fileContent = json_encode($jsonData);

        $file = UploadedFile::fake()->createWithContent('seu_arquivo.txt', $fileContent);

        $response = $this->json('POST', '/import/upload',  ['file' => $file]);

        $errorResponse = json_decode($response->getContent());

        $response->assertStatus(422);

        $this->assertEquals(["O arquivo deve ser um JSON válido."], $errorResponse->errors->file);
    }


    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
