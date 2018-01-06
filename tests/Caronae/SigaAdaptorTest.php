<?php

namespace Caronae;

use stdClass;

class SigaAdaptorTest extends \PHPUnit_Framework_TestCase
{
    private $sigaUser;

    private $adaptor;

    public function setUp()
    {
        $sigaUser = new stdClass();
        $sigaUser->nivel = 'Graduação';
        $sigaUser->nomeCurso = 'Engenharia Ambiental';
        $sigaUser->IdentificacaoUFRJ = '1234';
        $sigaUser->nome = 'FULANO DA SILVA';
        $sigaUser->urlFoto = 'http://example.com/jpg';

        $this->sigaUser = $sigaUser;

        $this->adaptor = new SigaToCaronaeAdaptor();
    }

    public function testConvertUser()
    {
        $this->sigaUser->alunoServidor = '0';
        $caronaeUser = $this->adaptor->convertToCaronaeUser($this->sigaUser);

        $this->assertEquals([
            'name' => 'Fulano Da Silva',
            'id_ufrj' => '1234',
            'course' => 'Engenharia Ambiental',
            'profile' => 'Graduação',
            'profile_pic_url' => 'http://example.com/jpg'
        ], $caronaeUser);
    }

    public function testConvertsEmployeeProfile()
    {
        $this->sigaUser->alunoServidor = '1';
        $caronaeUser = $this->adaptor->convertToCaronaeUser($this->sigaUser);

        $this->assertEquals('Servidor', $caronaeUser['profile']);
    }

}
