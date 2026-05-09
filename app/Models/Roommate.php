<?php

namespace App\Models;

use Database\Factories\RoommateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Roommate extends Model
{
    /** @use HasFactory<RoommateFactory> */
    use HasFactory;

    protected $fillable = ['coloc_id', 'first_name', 'avatar_slug', 'avatar_url', 'order'];

    public const AVATARS = [
        'personnage-01', 'personnage-02', 'personnage-03', 'personnage-04',
        'personnage-05', 'personnage-06', 'personnage-07', 'personnage-08',
        'personnage-09', 'personnage-10', 'personnage-11', 'personnage-12',
        'personnage-13', 'personnage-14', 'personnage-15', 'personnage-16',
    ];

    /** @return BelongsTo<Coloc, $this> */
    public function coloc(): BelongsTo
    {
        return $this->belongsTo(Coloc::class);
    }
}
