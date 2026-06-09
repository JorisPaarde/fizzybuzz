<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'business_name', 'business_type', 'region', 'employees_count', 'purchase_size'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function priceSubmissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PriceSubmission::class);
    }

    public function priceImports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PriceImport::class);
    }

    public function wholesalers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Wholesaler::class)->withTimestamps();
    }

    public function wholesalersForSelect(): \Illuminate\Support\Collection
    {
        $linked = $this->wholesalers()->orderBy('name')->get();
        $others = Wholesaler::query()
            ->whereNotIn('id', $linked->pluck('id'))
            ->orderBy('name')
            ->get();

        return $linked->merge($others);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
