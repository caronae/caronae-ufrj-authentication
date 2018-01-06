<?php

namespace Caronae;

class SigaToCaronaeAdaptor
{
    public function convertToCaronaeUser($sigaUser)
    {
        if ($sigaUser->alunoServidor == '1') {
            $profile = 'Servidor';
        } else {
            $profile = $sigaUser->nivel;
        }

        return [
            'name' => mb_convert_case($sigaUser->nome, MB_CASE_TITLE, "UTF-8"),
            'id_ufrj' => $sigaUser->IdentificacaoUFRJ,
            'course' => $sigaUser->nomeCurso,
            'profile' => $profile,
            'profile_pic_url' => $sigaUser->urlFoto
        ];
    }
}
