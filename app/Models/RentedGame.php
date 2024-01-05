<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Game;

class RentedGame extends Model
{
    use HasFactory;

    protected $table = 'rentedgame';

    protected $primaryKey = 'id';   

    protected $fillable = [
        'userID',
        'gameID',
        'rentFrom',
        'rentTo',
        'totalPrice',
        'cardHolderName',
        'cardNumber',
        'status'
    ];

    public function game()
    {
        return $this->belongsTo(Game::class, 'gameID');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
