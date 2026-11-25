<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['type', 'label', 'value', 'note', 'sort_order'])]
class Link extends Model
{
    /**
     * @var array<string, string>
     */
    public const array TYPES = [
        'teamspeak' => 'TeamSpeak',
        'discord' => 'Discord',
        'other' => 'Sonstiges',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? 'Sonstiges';
    }

    public function isClickableUrl(): bool
    {
        return str_starts_with($this->value, 'https://')
            || str_starts_with($this->value, 'http://')
            || str_starts_with($this->value, 'ts3server://');
    }
}
