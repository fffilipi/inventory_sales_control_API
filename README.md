# Sistema de Controle de Estoque e Vendas - API

Uma API RESTful desenvolvida em Laravel para gerenciamento completo de estoque e vendas, com autenticação via Laravel Sanctum e arquitetura baseada em padrões de design como Repository, Service e DTO.

## 📋 Índice

- [Requisitos](#-requisitos)
- [Instalação](#-instalação)
- [Configuração](#-configuração)
- [Executando o Projeto](#-executando-o-projeto)
- [Testes](#-testes)
- [API Endpoints](#-api-endpoints)
- [Usuários Padrão](#-usuários-padrão)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Documentação Técnica](#-documentação-técnica)

## 🔧 Requisitos

### Sistema Operacional
- **Linux** (Ubuntu 20.04+ recomendado)
- **macOS** (10.15+)
- **Windows** (com WSL2 recomendado)

### Software Necessário
- **Docker** (20.10+)
- **Docker Compose** (2.0+)
- **Git** (2.30+)
- **Node.js** (16+ - para ferramentas de desenvolvimento)

### Verificar Instalações
```bash
# Verificar Docker
docker --version
docker-compose --version

# Verificar Git
git --version

# Verificar Node.js (opcional)
node --version
```

## 🚀 Instalação

### 1. Clone o Repositório
```bash
# Clone o projeto
git clone https://github.com/seu-usuario/inventory_sales_control_API.git

# Entre no diretório
cd inventory_sales_control_API
```

### 2. Configurar Variáveis de Ambiente
```bash
# Copiar arquivo de configuração
cp .env.example .env

# Editar configurações (opcional - já configurado para desenvolvimento)
nano .env
```

### 3. Instalar Dependências
```bash
# Instalar dependências do PHP via Composer
composer install

# Instalar dependências do Node.js (se necessário)
npm install
```

### 4. Configurar Permissões
```bash
# Dar permissões para o diretório storage
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## ⚙️ Configuração

### Configuração do Banco de Dados
O projeto está configurado para usar MySQL com Laravel Sail:

```bash
# Configuração já definida no .env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=inventory_sales
DB_USERNAME=sail
DB_PASSWORD=password
```

**Nota**: Se preferir usar SQLite para desenvolvimento local, altere no `.env`:
```bash
DB_CONNECTION=sqlite
# Comente ou remova as outras configurações de DB
```

### Configuração do Redis
O Redis já está configurado para cache e filas:

```bash
# Verificar configuração no .env
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

## 🏃‍♂️ Executando o Projeto

### 1. Subir os Containers
```bash
# Subir todos os serviços em background
./vendor/bin/sail up -d

# Verificar se os containers estão rodando
./vendor/bin/sail ps
```

### 2. Configurar o Banco de Dados
```bash
# Gerar chave da aplicação
./vendor/bin/sail artisan key:generate

# Executar migrações
./vendor/bin/sail artisan migrate

# Executar seeders (usuários e dados de teste)
./vendor/bin/sail artisan db:seed
```

### 3. Verificar Funcionamento - opcional
```bash
# Testar conexão com Redis
./vendor/bin/sail artisan tinker
# Dentro do tinker:
# cache()->store('redis')->put('teste', 'ok', 10);
# cache()->store('redis')->get('teste');
# exit
```

### 4. Iniciar o Servidor de Desenvolvimento
```bash
# Iniciar o servidor Laravel (necessário para acessar a API)
./vendor/bin/sail artisan serve --host=0.0.0.0 --port=8080
```

### 5. Acessar a API
```bash
# A API estará disponível em:
# http://localhost:8080

# Testar endpoint de produtos (sem autenticação retornará 401)
curl http://localhost:8080/api/products

# Testar com autenticação (exemplo)
curl -H "Authorization: Bearer SEU_TOKEN_AQUI" http://localhost:8080/api/products
```

**Nota**: O comando `php artisan serve` é necessário para que a API seja acessível via HTTP. Sem ele, apenas os containers estarão rodando, mas não haverá servidor web ativo.

## 🧪 Testes

### Executar Todos os Testes
```bash
# Executar todos os testes
./vendor/bin/sail artisan test

# Executar com relatório de cobertura
./vendor/bin/sail artisan test --coverage

# Executar apenas testes unitários
./vendor/bin/sail artisan test --testsuite=Unit

# Executar apenas testes de integração
./vendor/bin/sail artisan test --testsuite=Feature
```

### Resultados Esperados
- ✅ **40 testes** executados com sucesso
- ✅ **288 assertions** validadas
- ✅ **50.5% de cobertura** de código

## 🔗 API Endpoints

### Autenticação
```bash
# Login
POST /api/auth/login
{
    "email": "admin@inventory.com",
    "password": "password123"
}

# Registro
POST /api/auth/register
{
    "name": "Novo Usuário",
    "email": "usuario@exemplo.com",
    "password": "senha123",
    "password_confirmation": "senha123"
}

# Logout
POST /api/auth/logout
Headers: Authorization: Bearer {token}

# Informações do usuário
GET /api/auth/me
Headers: Authorization: Bearer {token}
```

### Produtos
```bash
# Listar produtos
GET /api/products
Headers: Authorization: Bearer {token}

# Criar produto
POST /api/products
Headers: Authorization: Bearer {token}
{
    "sku": "PROD001",
    "name": "Produto Exemplo",
    "description": "Descrição do produto",
    "cost_price": 100.00,
    "sale_price": 150.00
}
```

### Estoque
```bash
# Consultar estoque
GET /api/inventory
Headers: Authorization: Bearer {token}

# Adicionar estoque (individual)
POST /api/inventory
Headers: Authorization: Bearer {token}
{
    "product_id": 1,
    "quantity": 10
}

# Adicionar estoque (lote)
POST /api/inventory
Headers: Authorization: Bearer {token}
[
    {"product_id": 1, "quantity": 5},
    {"product_id": 2, "quantity": 3}
]
```

### Vendas
```bash
# Criar venda
POST /api/sales
Headers: Authorization: Bearer {token}
{
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        },
        {
            "product_id": 2,
            "quantity": 1
        }
    ]
}

# Consultar venda
GET /api/sales/{id}
Headers: Authorization: Bearer {token}
```

## 👥 Usuários Padrão

Após executar os seeders, os seguintes usuários estarão disponíveis:

| Email | Senha | Função |
|-------|-------|--------|
| `admin@inventory.com` | `password123` | Administrador |
| `teste@inventory.com` | `teste123` | Usuário de Teste |
| `vendedor@inventory.com` | `vendedor123` | Vendedor |

## 📁 Estrutura do Projeto

```
inventory_sales_control_API/
├── app/
│   ├── DTOs/                    # Data Transfer Objects
│   ├── Events/                  # Eventos do sistema
│   ├── Http/
│   │   ├── Controllers/         # Controllers da API
│   │   ├── Middleware/          # Middlewares customizados
│   │   ├── Requests/            # Form Requests de validação
│   │   └── Responses/           # Respostas padronizadas
│   ├── Interfaces/              # Contratos/Interfaces
│   ├── Listeners/               # Listeners de eventos
│   ├── Models/                  # Modelos Eloquent
│   ├── Providers/               # Service Providers
│   └── Services/                # Lógica de negócio
├── database/
│   ├── factories/               # Factories para testes
│   ├── migrations/              # Migrações do banco
│   └── seeders/                 # Seeders de dados
├── tests/
│   ├── Feature/                 # Testes de integração
│   └── Unit/                    # Testes unitários
├── routes/
│   └── web.php                  # Rotas da aplicação
└── bootstrap/
    └── app.php                  # Configuração da aplicação
```

## 📚 Documentação Técnica

Para documentação técnica completa sobre arquitetura, padrões de design, modelagem de dados e regras de negócio, consulte:

- **[DOCUMENTATION.md](DOCUMENTATION.md)** - Documentação técnica completa
- **[TESTING.md](TESTING.md)** - Documentação dos testes

## 🛠️ Comandos Úteis

### Desenvolvimento
```bash
# Iniciar servidor de desenvolvimento
./vendor/bin/sail artisan serve --host=0.0.0.0 --port=8080

# Ver logs da aplicação
./vendor/bin/sail logs

# Acessar container principal
./vendor/bin/sail shell

# Executar comandos Artisan
./vendor/bin/sail artisan [comando]

# Limpar cache
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
```

### Manutenção
```bash
# Parar containers
./vendor/bin/sail down

# Reconstruir containers
./vendor/bin/sail build --no-cache

# Reset completo do banco
./vendor/bin/sail artisan migrate:fresh --seed
```

## 🐛 Solução de Problemas

### Problemas Comuns

1. **API não responde (erro de conexão)**
   ```bash
   # Verificar se o servidor está rodando
   ./vendor/bin/sail artisan serve --host=0.0.0.0 --port=8080
   ```

2. **Erro de permissão no storage**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

3. **Container não inicia**
   ```bash
   ./vendor/bin/sail down
   ./vendor/bin/sail up -d
   ```

4. **Erro de conexão com banco**
   ```bash
   ./vendor/bin/sail artisan migrate:fresh
   ```

5. **Cache não funciona**
   ```bash
   ./vendor/bin/sail artisan cache:clear
   ./vendor/bin/sail artisan config:clear
   ```

6. **Porta 8080 já está em uso**
   ```bash
   # Usar outra porta
   ./vendor/bin/sail artisan serve --host=0.0.0.0 --port=8081
   ```

## 📄 Licença

Este projeto está licenciado sob a [Licença MIT](LICENSE).

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📞 Suporte

Para suporte e dúvidas:
- Abra uma [Issue](https://github.com/seu-usuario/inventory_sales_control_API/issues)
- Consulte a [documentação técnica](DOCUMENTATION.md)
- Verifique os [testes](TESTING.md) para exemplos de uso