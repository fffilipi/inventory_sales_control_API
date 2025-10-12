<?php

namespace App\DTOs;

/**
 * Classe base para Data Transfer Objects (DTOs)
 * 
 * Esta classe fornece funcionalidades comuns para todos os DTOs,
 * incluindo conversão de arrays, validação e serialização.
 * 
 * @package App\DTOs
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
abstract class BaseDTO
{
    /**
     * Construtor do DTO
     * 
     * @param array $data Dados para popular o DTO
     */
    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    /**
     * Preenche o DTO com dados de um array
     * 
     * @param array $data Dados para popular o DTO
     * @return static
     */
    public function fill(array $data): static
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        
        return $this;
    }

    /**
     * Converte o DTO para array
     * 
     * @return array
     */
    public function toArray(): array
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        
        $array = [];
        foreach ($properties as $property) {
            $array[$property->getName()] = $property->getValue($this);
        }
        
        return $array;
    }

    /**
     * Converte o DTO para JSON
     * 
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Cria uma instância do DTO a partir de um array
     * 
     * @param array $data Dados para criar o DTO
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static($data);
    }

    /**
     * Valida se o DTO está preenchido corretamente
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        
        foreach ($properties as $property) {
            $value = $property->getValue($this);
            if ($value === null && !$this->isNullable($property)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Verifica se uma propriedade pode ser nula
     * 
     * @param \ReflectionProperty $property
     * @return bool
     */
    private function isNullable(\ReflectionProperty $property): bool
    {
        $type = $property->getType();
        return $type && $type->allowsNull();
    }
}
