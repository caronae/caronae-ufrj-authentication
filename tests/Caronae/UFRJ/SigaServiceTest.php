<?php

namespace Caronae\UFRJ;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class SigaServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function should_throw_when_is_student_and_does_not_have_active_enrollment()
    {
        $service = $this->sigaServiceWithFakeResponse('Trancada');

        $this->expectException(SigaUserNotEnrolledException::class);
        $service->getProfileById(1);
    }

    /** @test */
    public function should_return_student_with_active_enrollment()
    {
        $acceptedStatuses = ['Ativa', 'ATIVA', 'ativa', 'Ativo', 'ATIVO', 'ativo'];

        foreach ($acceptedStatuses as $status) {
            $service = $this->sigaServiceWithFakeResponse($status);
            $user = $service->getProfileById(1);
            $this->assertNotNull($user);
        }
    }

    /** @test */
    public function should_return_employee_with_inactive_enrollment()
    {
        $service = $this->sigaServiceWithFakeResponse('Trancada', true);
        $user = $service->getProfileById(1);
        $this->assertNotNull($user);
    }

    private function sigaServiceWithFakeResponse($enrollmentStatus, $isEmployee = false)
    {
        $employeeStatus = $isEmployee ? 1 : 0;
        $body = '{
            "took":356,
            "timed_out":false,
            "_shards":{"total":86,"successful":86,"skipped":0,"failed":0},
            "hits":{
            "total":1,
            "max_score":9.458981,
            "hits":[
                {"_index":"alunos_regularmente_matriculados",
                "_type":"aluno",
                "_id":"123456789",
                "_score":9.458981,
                "_source":{
                    "nivel":"Mestrado",
                    "codCurso":"123456789",
                    "nomeCurso":"Curso",
                    "matriculadre":"123456",
                    "IdentificacaoUFRJ":"123456789",
                    "nome":"Fulando da Silva",
                    "situacaoMatricula":"' . $enrollmentStatus . '",
                    "urlFoto":"http://example.com/picture.jpg",
                    "alunoServidor":"' . $employeeStatus . '"
                }
            }]
            }
        }';
        $mock = new MockHandler([new Response(200, [], $body)]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        return new SigaService($client, 'http://example.com/search');
    }


}
