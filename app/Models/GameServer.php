<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'game', 'host', 'port', 'password', 'description', 'notes', 'is_active', 'sort_order'])]
class GameServer extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'password' => 'encrypted',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function connectionAddress(): string
    {
        if ($this->port === null) {
            return $this->host;
        }

        return "{$this->host}:{$this->port}";
    }
}
