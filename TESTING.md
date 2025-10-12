# Documentação de Testes - Sistema de Controle de Estoque e Vendas

## Visão Geral

Este documento descreve a estrutura de testes implementada para o sistema de controle de estoque e vendas, incluindo testes unitários e de integração usando PHPUnit.

## Estrutura de Testes

### Testes Unitários (`tests/Unit/`)

#### 1. ProductServiceTest
- **Objetivo**: Testa a lógica de negócio do serviço de produtos
- **Cobertura**: 100%
- **Testes implementados**:
  - Criação de produtos com sucesso
  - Listagem de todos os produtos
  - Busca de produto por ID
  - Tratamento de produto não encontrado
  - Cálculo de margem de lucro
  - Cálculo de lucro por unidade

#### 2. InventoryServiceTest
- **Objetivo**: Testa a lógica de negócio do serviço de estoque
- **Cobertura**: 100%
- **Testes implementados**:
  - Adição de estoque para produto novo
  - Adição de estoque para produto existente (soma quantidades)
  - Adição de estoque em lote
  - Consulta de estoque consolidado
  - Agrupamento de múltiplos registros do mesmo produto

#### 3. SalesServiceTest
- **Objetivo**: Testa a lógica de negócio do serviço de vendas
- **Cobertura**: 100%
- **Testes implementados**:
  - Criação de venda com sucesso
  - Criação de venda com múltiplos itens
  - Validação de estoque insuficiente
  - Validação de produto sem estoque
  - Busca de detalhes de venda
  - Tratamento de venda não encontrada

### Testes de Integração (`tests/Feature/`)

#### 1. ProductApiTest
- **Objetivo**: Testa a API completa de produtos
- **Cobertura**: 73.3%
- **Testes implementados**:
  - Listagem de produtos com autenticação
  - Listagem de produtos sem autenticação
  - Criação de produto com dados válidos
  - Criação de produto com dados inválidos
  - Criação de produto com SKU duplicado
  - Criação de produto sem autenticação

#### 2. InventoryApiTest
- **Objetivo**: Testa a API completa de estoque
- **Cobertura**: 75.0%
- **Testes implementados**:
  - Consulta de estoque com autenticação
  - Consulta de estoque sem autenticação
  - Adição de estoque individual com dados válidos
  - Adição de estoque em lote com dados válidos
  - Soma de quantidades para produtos existentes
  - Validação de dados inválidos
  - Validação de autenticação

#### 3. SalesApiTest
- **Objetivo**: Testa a API completa de vendas
- **Cobertura**: 83.3%
- **Testes implementados**:
  - Criação de venda com dados válidos
  - Criação de venda com múltiplos itens
  - Validação de estoque insuficiente
  - Consulta de detalhes de venda
  - Tratamento de venda não encontrada
  - Validação de autenticação

#### 4. CompleteWorkflowTest
- **Objetivo**: Testa o fluxo completo da aplicação
- **Cobertura**: Fluxo end-to-end
- **Testes implementados**:
  - Fluxo completo: produtos → estoque → vendas
  - Adição de estoque em lote
  - Validação de estoque insuficiente com dados reais

## Dados de Teste

### Produtos Mocados (TestDataSeeder)

O sistema inclui 5 produtos pré-definidos para testes:

1. **Smartphone Samsung Galaxy S24**
   - SKU: TEST001
   - Custo: R$ 800,00
   - Venda: R$ 1.200,00
   - Margem: 33,33%

2. **Notebook Dell Inspiron 15**
   - SKU: TEST002
   - Custo: R$ 1.500,00
   - Venda: R$ 2.200,00
   - Margem: 31,82%

3. **Tablet iPad Air 5**
   - SKU: TEST003
   - Custo: R$ 1.200,00
   - Venda: R$ 1.800,00
   - Margem: 33,33%

4. **Fone de Ouvido Sony WH-1000XM4**
   - SKU: TEST004
   - Custo: R$ 300,00
   - Venda: R$ 450,00
   - Margem: 33,33%

5. **Smartwatch Apple Watch Series 9**
   - SKU: TEST005
   - Custo: R$ 600,00
   - Venda: R$ 900,00
   - Margem: 33,33%

## Factories

### ProductFactory
- Gera produtos com dados realistas
- Suporte a margens de lucro específicas
- Preços customizáveis

### InventoryFactory
- Cria registros de estoque
- Quantidades configuráveis
- Associação com produtos

### SaleFactory
- Gera vendas com valores calculados
- Status configurável (pending/completed)

### SaleItemFactory
- Cria itens de venda
- Associação com vendas e produtos
- Quantidades e preços realistas

## Execução dos Testes

### Comandos Disponíveis

```bash
# Executar todos os testes
./vendor/bin/sail artisan test

# Executar apenas testes unitários
./vendor/bin/sail artisan test --testsuite=Unit

# Executar apenas testes de integração
./vendor/bin/sail artisan test --testsuite=Feature

# Executar com relatório de cobertura
./vendor/bin/sail artisan test --coverage

# Executar teste específico
./vendor/bin/sail artisan test --filter=ProductServiceTest
```

### Resultados Atuais

- **Total de Testes**: 40
- **Assertions**: 288
- **Cobertura Geral**: 50.5%
- **Status**: ✅ Todos os testes passando

## Estratégias de Teste

### Testes Unitários
- **Isolamento**: Cada teste é independente
- **Mocking**: Uso de Event::fake() para eventos
- **Dados**: RefreshDatabase para limpeza entre testes
- **Foco**: Lógica de negócio pura

### Testes de Integração
- **Autenticação**: Testes com e sem autenticação
- **Validação**: Testes de dados válidos e inválidos
- **Fluxo Completo**: Testes end-to-end
- **Eventos**: Processamento real de eventos

### Padrões Utilizados

1. **Arrange-Act-Assert**: Estrutura clara dos testes
2. **Dados Realistas**: Uso de factories com dados plausíveis
3. **Cenários de Erro**: Testes de validação e tratamento de erros
4. **Autenticação**: Testes de segurança da API
5. **Fluxo Completo**: Testes que simulam uso real

## Melhorias Futuras

### Cobertura de Código
- Implementar testes para AuthController (0% cobertura)
- Adicionar testes para DTOs não testados
- Melhorar cobertura de Form Requests

### Testes Adicionais
- Testes de performance
- Testes de concorrência
- Testes de carga
- Testes de integração com banco de dados

### Ferramentas
- Integração com CI/CD
- Relatórios de cobertura automatizados
- Testes de regressão automatizados

## Conclusão

A estrutura de testes implementada garante a qualidade e confiabilidade do sistema, cobrindo tanto a lógica de negócio quanto a integração entre componentes. Os testes são executados rapidamente e fornecem feedback imediato sobre a saúde do código.

A cobertura atual de 50.5% é adequada para o escopo atual, com foco nas funcionalidades críticas do sistema. Os testes unitários garantem que a lógica de negócio está funcionando corretamente, enquanto os testes de integração validam o funcionamento completo da API.
