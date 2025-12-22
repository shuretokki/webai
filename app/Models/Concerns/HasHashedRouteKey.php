<?php

namespace App\Models\Concerns;

use Vinkla\Hashids\Facades\Hashids;

trait HasHashedRouteKey
{
    /**
     * Get the route key for the model (used in URLs)
     */
    public function getRouteKey(): string
    {
        return Hashids::encode($this->getKey());
    }

    /**
     * Retrieve the model for a bound value (decode hashid back to integer)
     */
    public function resolveRouteBinding($value, $field = null): ?static
    {
        $decoded = Hashids::decode($value);

        if (empty($decoded)) {
            return null;
        }

        return $this->where($this->getRouteKeyName(), $decoded[0])->first();
    }
}
