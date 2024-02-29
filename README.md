# Instalação do Package Supernova

Este documento tem como objetivo orientar a instalação do package Supernova em seu projeto utilizando o Composer e o Laravel.

## Passo 1: Adicionar o repositório ao composer.json

Para iniciar o processo de instalação do package Supernova, é necessário adicionar o repositório correspondente ao seu composer.json. Para isso, siga as instruções abaixo:

1. Abra o arquivo `composer.json` localizado na raiz do seu projeto Laravel.

2. Dentro do arquivo `composer.json`, adicione o seguinte código dentro do array `"repositories"`:

```json
"repositories": [
    {
        "type": "git",
        "url": "https://github.com/marcusvbda/supernova.git"
    }
]
```

Certifique-se de que a estrutura do seu arquivo `composer.json` permaneça válida, ou seja, mantenha a formatação JSON correta e verifique se não há erros de sintaxe.

## Passo 2: Executar o comando composer require

Após adicionar o repositório ao `composer.json`, você pode prosseguir com a instalação do package Supernova. Para isso, execute o seguinte comando no terminal, na raiz do seu projeto Laravel:

```bash
composer require marcusvbda/supernova:dev-master
```

Este comando irá baixar e instalar o package Supernova em seu projeto Laravel.

## Passo 3: Criar o banco de dados

Antes de prosseguir com a instalação, é necessário criar o banco de dados que será utilizado pelo Supernova. Certifique-se de configurar corretamente as credenciais de acesso ao banco de dados no arquivo `.env` do seu projeto Laravel.

## Passo 4: Executar o comando de instalação do Supernova

Após a conclusão da instalação via Composer e a criação do banco de dados, você pode prosseguir com a instalação final do Supernova. Execute o seguinte comando no terminal, na raiz do seu projeto Laravel:

```bash
php artisan supernova:install
```

Este comando irá executar o processo de instalação do Supernova, configurando-o corretamente para uso em seu projeto Laravel. Além disso, esse comando realizará as seguintes ações:

- Criará módulos de usuários, permissões e grupos de acesso.
- Migrará as tabelas necessárias para o funcionamento desses módulos.
- Criará models correspondentes aos usuários, permissões e grupos de acesso.

Após a execução bem-sucedida do comando `php artisan supernova:install`, o package Supernova estará pronto para ser utilizado em seu projeto Laravel.

Se você encontrar quaisquer problemas durante o processo de instalação, verifique se todos os passos foram seguidos corretamente e se as dependências do seu projeto Laravel estão atualizadas.

Em caso de dúvidas ou problemas adicionais, consulte a documentação oficial do package Supernova ou entre em contato com o desenvolvedor responsável pelo package.

Espero que este guia seja útil para você instalar o package Supernova em seu projeto Laravel. Boa sorte!