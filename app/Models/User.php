<?php

namespace App\Models;

use App\Library\APIToken;
use App\Library\BuyOwnExClientAPI;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rate_limit',
        'last_login_at',
        'invite_code',
        'blocked',
        'language'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'last_ip'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * @throws \App\Exceptions\ExceptionBuyOwnExAPI
     * @throws ValidationException
     */
    public function markEmailAsVerified()
    {
        $api = new BuyOwnExClientAPI(config('app.api-public-key'), config('app.api-secret-key'));
        $res = $api->registerTrader($this);
        if($res->status() === 200)
        {
            $result = json_decode($res->content(), true);
            if($result['success'])
            {
                return $this->forceFill([
                    'email_verified_at' => $this->freshTimestamp(),
                ])->save();
            }
            throw ValidationException::withMessages([
                'message' => __('app.auth.verification_error'),
            ]);
        }
        throw ValidationException::withMessages([
            'message' => __('app.auth.verification_error'),
        ]);
    }

    public function createToken(string $name, array $abilities = ['*'])
    {
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(80)),
            'secret' => $secretTextToken = Str::random(80),
            'abilities' => $abilities,
        ]);
        return new APIToken($token, $plainTextToken, $secretTextToken);
    }

    public function createTokenWithDelete(string $name, array $abilities = ['*'])
    {
        $this->tokens()->where('name','=', $name)->delete();
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(80)),
            'secret' => $secretTextToken = Str::random(80),
            'abilities' => $abilities,
        ]);
        return new APIToken($token, $plainTextToken, $secretTextToken);
    }

    /**
     * Get the access tokens that belong to model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function tokens()
    {
        return $this->morphMany('App\Models\PersonalAccessToken', 'tokenable');
    }
}
