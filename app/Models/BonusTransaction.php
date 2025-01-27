<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $client_id
 * @property string $type
 * @property string $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\TFactory|null $use_factory
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BonusTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BonusTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BonusTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BonusTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BonusTransaction whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BonusTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BonusTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BonusTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BonusTransaction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BonusTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'type', 'amount'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}

