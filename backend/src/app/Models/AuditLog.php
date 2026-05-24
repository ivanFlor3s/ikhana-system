<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'model_name',
        'old_values',
        'new_values',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Disable updated_at timestamp.
     */
    public const UPDATED_AT = null;

    /**
     * Get the user that performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate human-readable message.
     * 
     * @return string
     */
    public function getMessageAttribute(): string
    {
        $userName = $this->user->name ?? 'Usuario desconocido';

        $actionMap = [
            'created' => 'alta',
            'updated' => 'modificación',
            'deleted' => 'baja',
        ];

        $actionText = $actionMap[$this->action] ?? $this->action;

        // Traducir nombres de modelos al español
        $modelMap = [
            'Provider' => 'Proveedor',
            'Agreement' => 'Convenio',
            'Category' => 'Categoría',
            'Broker' => 'Corredor',
            'TaxStatus' => 'Estado Fiscal',
            'RawMaterialEntry' => 'Entrada de Materia Prima',
            'MaterialTest' => 'Ensayo de Calidad',
        ];

        $modelText = $modelMap[$this->model_type] ?? $this->model_type;

        $identifier = $this->model_name ? ": {$this->model_name}" : " (ID: {$this->model_id})";

        return "{$userName} realizó una {$actionText} en {$modelText}{$identifier}";
    }

    /**
     * Scope to filter by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by action.
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by model type.
     */
    public function scopeByModelType($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $from, $to)
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }

    /**
     * Scope to search in message.
     */
    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        })->orWhere('model_name', 'like', "%{$search}%");
    }
}
