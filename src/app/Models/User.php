<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
  ];

  public function posts() {
    return $this->hasMany(Post::class);
  }

  public function createUser($registerData) {
    return $this->create([
      "name" => $registerData["name"],
      "email" => $registerData["email"],
      "password" => Hash::make($registerData["password"]),
    ]);
  }

  public function loginUser($loginData) {
    return $this->where("email", $loginData["email"])->first();
  }

  public function generateAuthToken() {
    $this->tokens()->delete();
    $token = $this->createToken("login:user{$this->id}")->plainTextToken;
    return $token;
  }

  public function deleteAuthTokens() {
    $this->tokens()->delete();
  }
}
