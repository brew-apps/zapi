# Laravel Z-API Integration Package

Uma package Laravel para integração com a Z-API (WhatsApp API).

## Instalação

1. Adicione o repositório ao seu `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/brew-apps/zapi"
        }
    ]
}
```

2. Instale via Composer:

```bash
composer require brew/zapi
```

3. Publique os arquivos de configuração e migrations:

```bash
php artisan vendor:publish --provider="Brew\Zapi\Providers\ZapiServiceProvider"
```

4. Execute as migrations:

```bash
php artisan migrate
```

5. Configure suas credenciais no arquivo `.env`:

```env
ZAPI_API_URL=https://api.z-api.io
ZAPI_INSTANCE_ID=seu-instance-id
ZAPI_INSTANCE_TOKEN=seu-instance-token
ZAPI_CLIENT_TOKEN=seu-client-token
```

## Uso

### Enviando Mensagem de Texto

```php
use Brew\Zapi\Facades\Zapi;
use Brew\Zapi\DTO\Messages\TextMessageData;

// Crie o objeto de mensagem
$message = new TextMessageData(
    phone: '5511999999999',  // Formato: DDI + DDD + Número
    message: 'Sua mensagem aqui',
    delayMessage: 1  // Opcional: delay em segundos
);

// Envie a mensagem
try {
    $response = Zapi::sendText($message);
    
    if ($response->success) {
        echo "Mensagem enviada com sucesso! ID: {$response->messageId}";
    }
} catch (\Brew\Zapi\Exceptions\ZapiException $e) {
    echo "Erro ao enviar mensagem: " . $e->getMessage();
}
```

### Verificando Status da Mensagem

O status da mensagem pode ser:
- `MessageStatus::PENDING`: Mensagem aguardando envio
- `MessageStatus::SENT`: Mensagem enviada
- `MessageStatus::DELIVERED`: Mensagem entregue
- `MessageStatus::READ`: Mensagem lida
- `MessageStatus::ERROR`: Erro no envio

```php
use Brew\Zapi\Enums\MessageStatus;

if ($response->status === MessageStatus::SENT) {
    echo "Mensagem enviada com sucesso!";
}
```

### Logs

A package automaticamente registra todas as requisições e respostas na tabela `zapi_logs`. Você pode acessá-las através do modelo `ZapiLog`:

```php
use Brew\Zapi\Models\ZapiLog;

// Buscar todos os logs
$logs = ZapiLog::all();

// Buscar logs específicos
$logs = ZapiLog::where('endpoint', 'send-text')
    ->where('status_code', 200)
    ->get();
```

## Desenvolvimento Futuro

Esta package está preparada para receber novas funcionalidades, incluindo:

1. Mensagens:
    - Envio de áudio
    - Envio de imagens
    - Envio de documentos
    - Envio de links
    - etc.

2. Chamadas:
    - Fazer chamada
    - Atender chamada
    - Rejeitar chamada

3. Chats:
    - Listar chats
    - Arquivar chat
    - Marcar como lido

Para adicionar novas funcionalidades, siga a estrutura de namespaces existente:
- `Messages/` para funcionalidades relacionadas a mensagens
- `Calls/` para funcionalidades relacionadas a chamadas
- `Chats/` para funcionalidades relacionadas a chats

## Contribuindo

Contribuições são bem-vindas! Por favor, sinta-se à vontade para submeter um Pull Request.

## Licença

Esta package é um software open-source licenciado sob a [licença MIT](LICENSE.md).
