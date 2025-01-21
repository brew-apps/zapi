<?php

namespace Brew\Zapi\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ZapiLog extends Model
{
    protected $table = 'zapi_logs';

    protected $fillable = [
        'endpoint',
        'request_data',
        'response_data',
        'status_code',
        'execution_time',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'status_code' => 'integer',
        'execution_time' => 'float',
    ];

    /**
     * Scope para filtrar logs por endpoint
     */
    public function scopeEndpoint(Builder $query, string $endpoint): Builder
    {
        return $query->where('endpoint', $endpoint);
    }

    /**
     * Scope para filtrar logs por status code
     */
    public function scopeStatusCode(Builder $query, int $statusCode): Builder
    {
        return $query->where('status_code', $statusCode);
    }

    /**
     * Scope para filtrar logs bem sucedidos (2xx)
     */
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->whereBetween('status_code', [200, 299]);
    }

    /**
     * Scope para filtrar logs com erro (4xx, 5xx)
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status_code', '>=', 400);
    }

    /**
     * Scope para filtrar logs por período
     */
    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope para filtrar logs lentos (acima de um determinado tempo de execução)
     */
    public function scopeSlow(Builder $query, float $seconds = 1.0): Builder
    {
        return $query->where('execution_time', '>=', $seconds);
    }

    /**
     * Accessor para formatar o tempo de execução em milissegundos
     */
    protected function executionTimeMs(): Attribute
    {
        return Attribute::make(
            get: fn () => round($this->execution_time * 1000, 2)
        );
    }

    /**
     * Accessor para verificar se a requisição foi bem sucedida
     */
    protected function wasSuccessful(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status_code >= 200 && $this->status_code < 300
        );
    }

    /**
     * Retorna um resumo formatado do log
     */
    public function summary(): string
    {
        return sprintf(
            '[%s] %s - Status: %d - Time: %.2fms',
            $this->created_at->format('Y-m-d H:i:s'),
            $this->endpoint,
            $this->status_code,
            $this->execution_time_ms
        );
    }

    /**
     * Limpa logs antigos
     */
    public static function cleanOldLogs(int $days = 30): int
    {
        return static::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Retorna estatísticas dos logs
     */
    public static function getStats(?string $startDate = null, ?string $endDate = null): array
    {
        $query = static::query();

        if ($startDate && $endDate) {
            $query->betweenDates($startDate, $endDate);
        }

        return [
            'total_requests' => $query->count(),
            'successful_requests' => $query->successful()->count(),
            'failed_requests' => $query->failed()->count(),
            'average_time' => $query->avg('execution_time'),
            'slowest_request' => $query->max('execution_time'),
            'fastest_request' => $query->min('execution_time'),
            'status_codes' => $query->selectRaw('status_code, COUNT(*) as count')
                ->groupBy('status_code')
                ->pluck('count', 'status_code')
                ->toArray(),
        ];
    }
}
