<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\RentedGame;
use Illuminate\Support\Facades\Validator;

class RentingGameController extends Controller
{
    public function addRentingGameView($gameID)
    {
        return view('rentGame', [
            'gameID' => $gameID,
        ]);
    }
    
    public function addRentingDetails(Request $request, $gameID)
    {

        $rules = [
            'fromDate' => 'required|date|after_or_equal:today',
            'toDate' => 'required|date|after:fromDate',
            'cardHolderName' => 'required|string|max:255',
            'cardNumber' => 'required|integer|digits_between:16,16',
            'expiration' => 'required|numeric|digits_between:4,4',
            'securityCode' => 'required|numeric|digits_between:3,3',
        ];
        
        $messages = [
            'fromDate.after_or_equal' => 'Rent from date must be today or in the future.',
            'toDate.after' => 'Rent to date must be after the rent from date.',
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);
       
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $from = strtotime($request->input('fromDate'));
        $to = strtotime($request->input('toDate'));
        $diff = $to - $from;
        $days = round($diff / (60 * 60 * 24));
        $totalPrice = $days * 5;

        $userID = auth()->user()->id;
        $rentedGame = new RentedGame([
            'userID' => $userID,
            'gameID' => $gameID,
            'rentFrom' => $request->input('fromDate'),
            'rentTo' => $request->input('toDate'),
            'totalPrice' => $totalPrice,
            'cardHolderName' => $request->input('cardHolderName'),
            'cardNumber' => $request->input('cardNumber')
        ]);

        $rentedGame->save();
        return redirect()->back()->with('success', 'Rent the game successfully!');
    }

    public function getRentedGames()
    {
        $userID = auth()->user()->id;

        $rentedGames = RentedGame::join('game', 'rentedgame.gameID', '=', 'game.id')
            ->where('userID', $userID)->select(
                'rentedgame.id AS rentID',
                'rentedgame.gameID',
                'rentedgame.rentFrom',
                'rentedgame.rentTo',
                'rentedgame.totalPrice',
                'rentedgame.cardHolderName',
                'rentedgame.cardNumber',
                'rentedgame.status',
                'game.*'
            )->get();

        return view('myGame', [
            'rentedGames' => $rentedGames,
        ]);
    }

    public function getRentedGameItem($gameID)
    {
        $userID = auth()->user()->id;

        $rentedGame = RentedGame::join('game', 'rentedgame.gameID', '=', 'game.id')
            ->where('userID', $userID)->where('rentedgame.gameID', $gameID)->first();


        return view('viewMyGameItem', [
            'rentedGame' => $rentedGame,
        ]);
    }

    public function updateStatusRentedGameItem($gameID)
    {
        try {
            $userID = auth()->user()->id;

            RentedGame::join('game', 'rentedgame.gameID', '=', 'game.id')
                ->where('userID', $userID)->where('rentedgame.gameID', $gameID)->update(
                    array(
                        'status' => 'Cancelled'
                    )
                );


            return redirect()->route('myGame');
        } catch (\Throwable $th) {
            return view('error', [
                'error' => $th->getMessage()
            ]);
        }
    }

    public function deleteStatusRentedGameItem($rentID)
    {
        try {

            $rentedGame = RentedGame::find($rentID);
            $rentedGame->delete();

            return redirect()->route('myGame');
        } catch (\Throwable $th) {
            return view('error', [
                'error' => $th->getMessage()
            ]);
        }
    }
}
