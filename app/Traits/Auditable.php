<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
    /**
     * If model has list of auditable attributes, take It
     */
    public function getAuditableAttributes(): array
    {
        if (!property_exists($this, 'auditable') || !is_array($this->auditable)) {
            return array_keys($this->getAttributes());
        }

        $attributes = [];
        foreach ($this->auditable as $key => $value) {
            if (is_numeric($key)) {
                // Common fields: ['name', 'email']
                $attributes[] = $value;
            } else {
                // Fields with options: ['category_id' => ['relation' => 'category', 'display_field' => 'name']]
                $attributes[] = $key;
            }
        }

        return $attributes;
    }

    /**
     * Get the audits for the model.
     *
     * @return array
     */
    private function transformForeignKeys(array $data): array
    {
        if (!property_exists($this, 'auditable') || !is_array($this->auditable)) {
            return $data;
        }

        foreach ($this->auditable as $key => $value) {
            // Skip numeric keys
            if (is_numeric($key)) {
                continue;
            }

            // Check if the key exists in the data and is not null
            if (isset($data[$key]) && $data[$key] !== null && is_array($value)) {
                $relationName = $value['relation'] ?? null;
                $displayField = $value['display_field'] ?? 'name';

                if ($relationName) {
                    $relatedModel = $this->findRelatedModel($relationName, $data[$key]);

                    if ($relatedModel) {
                        $data[$key] = $relatedModel->{$displayField} ?? 'Unknown';
                    } else {
                        $data[$key] = 'Deleted or Not Found';
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Get related model by relation name and ID.
     */
    private function findRelatedModel(string $relationName, $id)
    {
        // First, check if the relation is loaded
        if ($this->relationLoaded($relationName)) {
            return $this->getRelation($relationName);
        }

        // If not loaded, try to load it
        try {
            $this->load($relationName);
            return $this->getRelation($relationName);
        } catch (\Exception $e) {
            // If loading fails, try to find the related model directly
            $relatedModelClass = $this->{$relationName}()->getRelated();
            return $relatedModelClass::find($id);
        }
    }

    /**
     * Get auditable data for the model.
     */
    public function getAuditableData(): array
    {
        $data = $this->only($this->getAuditableAttributes());
        return $this->transformForeignKeys($data);
    }

    /**
     * Get original auditable data for the model.
     */
    public function getOriginalAuditableData(): array
    {
        $original = $this->getOriginal();
        $auditableFields = $this->getAuditableAttributes();
        $data = array_intersect_key($original, array_flip($auditableFields));
        return $this->transformForeignKeys($data);
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function getAudits()
    {
        return $this->audits()
            ->with('user')
            ->latest()
            ->get();
    }
}
