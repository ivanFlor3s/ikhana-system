<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Boot the auditable trait for a model.
     */
    public static function bootAuditable()
    {
        // Log when a model is created
        static::created(function ($model) {
            $model->auditLog('created');
        });

        // Log when a model is updated
        static::updated(function ($model) {
            $model->auditLog('updated');
        });

        // Log when a model is deleted
        static::deleted(function ($model) {
            $model->auditLog('deleted');
        });
    }

    /**
     * Create an audit log entry.
     *
     * @param string $action
     * @return void
     */
    protected function auditLog(string $action)
    {
        // Solo registrar si hay un usuario autenticado
        if (!Auth::check()) {
            return;
        }

        $oldValues = null;
        $newValues = null;

        if ($action === 'created') {
            // Para creación, solo guardamos los valores nuevos
            $newValues = $this->getAuditableAttributes();
        } elseif ($action === 'updated') {
            // Para actualización, guardamos valores viejos y nuevos
            $oldValues = $this->getOriginal();
            $newValues = $this->getAttributes();

            // Filtrar solo los campos que cambiaron
            $changed = $this->getDirty();
            $oldValues = array_intersect_key($oldValues, $changed);
            $newValues = array_intersect_key($newValues, $changed);

            // Remover campos sensibles
            $oldValues = $this->filterSensitiveData($oldValues);
            $newValues = $this->filterSensitiveData($newValues);
        } elseif ($action === 'deleted') {
            // Para eliminación, guardamos los valores que tenía
            $oldValues = $this->getAuditableAttributes();
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => class_basename($this),
            'model_id' => $this->id,
            'model_name' => $this->getAuditName(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    /**
     * Get the name/identifier for audit log message.
     *
     * @return string|null
     */
    protected function getAuditName(): ?string
    {
        // Intentar obtener un nombre descriptivo del modelo
        if (isset($this->business_name)) {
            return $this->business_name;
        }

        if (isset($this->name)) {
            return $this->name;
        }

        if (isset($this->title)) {
            return $this->title;
        }

        return null;
    }

    /**
     * Get auditable attributes (exclude timestamps and sensitive data).
     *
     * @return array
     */
    protected function getAuditableAttributes(): array
    {
        $attributes = $this->getAttributes();

        // Remover timestamps
        unset($attributes['created_at'], $attributes['updated_at'], $attributes['deleted_at']);

        // Remover campos sensibles
        return $this->filterSensitiveData($attributes);
    }

    /**
     * Filter sensitive data from attributes.
     *
     * @param array $attributes
     * @return array
     */
    protected function filterSensitiveData(array $attributes): array
    {
        $sensitive = ['password', 'remember_token', 'api_token'];

        foreach ($sensitive as $field) {
            unset($attributes[$field]);
        }

        return $attributes;
    }
}
