<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'bike_number',
        'role',
        'branch_id',
        'is_active',
        'wallet_balance',
        'profile_completed',
        'username',
        'referral_code',
        'referred_by',
        'dob',
        'gender',
        'is_working_today',
        'last_status_update',
        'profile_image'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'dob' => 'date',
        'wallet_balance' => 'decimal:2',
        'profile_completed' => 'boolean',
        'is_active' => 'boolean',
        'is_working_today' => 'boolean',
        'last_status_update' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDataEntry(): bool
    {
        return $this->role === 'dataentry';
    }

    public function isBranchManager(): bool
    {
        return $this->role === 'branch_manager';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Get the branch associated with the user.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function activeOrders()
    {
        return $this->hasMany(Order::class, 'delivery_boy_id')
            ->whereIn('status', ['assigned', 'picked_up']);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function currentSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest()
            ->first();
    }

    public function isShopOwner(): bool
    {
        return $this->role === 'shop_owner';
    }

    public function hasRole($role)
    {
        if ($this->role === 'admin') {
            return true;
        }
        
        return $this->role === $role;
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function addToWallet($amount, $description, $referenceType = null, $referenceId = null, $metadata = [])
    {
        return DB::transaction(function () use ($amount, $description, $referenceType, $referenceId, $metadata) {
            $transaction = WalletTransaction::create([
                'user_id' => $this->id,
                'amount' => $amount,
                'type' => 'credit',
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'status' => 'completed',
                'metadata' => $metadata
            ]);

            $this->increment('wallet_balance', $amount);

            return $transaction;
        });
    }

    public function deductFromWallet($amount, $description, $referenceType = null, $referenceId = null, $metadata = [])
    {
        if ($this->wallet_balance < $amount) {
            throw new \Exception('Insufficient wallet balance');
        }

        return DB::transaction(function () use ($amount, $description, $referenceType, $referenceId, $metadata) {
            $transaction = WalletTransaction::create([
                'user_id' => $this->id,
                'amount' => $amount,
                'type' => 'debit',
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'status' => 'completed',
                'metadata' => $metadata
            ]);

            $this->decrement('wallet_balance', $amount);

            return $transaction;
        });
    }

    public function monthlyLists()
    {
        return $this->hasMany(MonthlyList::class);
    }
}