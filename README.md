# Caronaê - Autenticação com a UFRJ

Serviço de login com a UFRJ do Caronaê.


## Funcionamento

Este app é utilizado para autenticar usuários da UFRJ e autorizá-los a utilizar o Caronaê.

O usuário pede pra fazer login no Caronaê e o Caronaê redireciona para a instituição selecionada, onde começa o fluxo deste app. 

1. O usuário é redirecionado para a Intranet UFRJ, que redireciona de volta quando o usuário é autenticado
2. Buscamos os dados do aluno no SIGA (nome, curso, foto, se é Servidor, se está na Graduação, Mestrado etc.)
3. Enviamos para a API do Caronaê esses dados, que cria o perfil, caso ele ainda não existe, e retorna um token temporário de autenticação
4. Redirecionamos de volta para a página de login do Caronaê passando o token obtido

A partir daí o próprio Caronaê é responsável pela autenticação no app.

## Documentação rápida

- **phpCAS**: biblioteca que faz a integração com o [CAS](https://cas.ufrj.br/) (Central Authentication Service) da Intranet UFRJ, que valida que o usuário é vinculado à UFRJ
-  **SigaService**: comunica com o SIGA UFRJ, de onde são buscados os dados dos usuários
- **CaronaeService**: parte do [caronae-sdk-php](https://github.com/caronae/caronae-sdk-php), é a classe que faz a comunicação com a API do Caronaê e é usada para enviar os dados do usuário para o Caronaê
- **CaronaeSigaAdaptor**: faz a conversão dos dados no formato que vem do SIGA para o formato da API do Caronaê
- **CaronaeUFRJAgent**: faz o fluxo de autenticação e autorização integrando todos os serviços acima