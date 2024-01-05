<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Game;

//BCS3453 [PROJECT]-SEMESTER 2324/1
// Student ID: CB21132
// Student Name: SHATTHIYA GANES A/L SIVAKUMARAN 

class GameController extends Controller
{
    public function addGameView()
    {
        return view('adminAddGame');
    }
    
    public function addGame(Request $request)
    {
        
        try {
            $request->validate([
                'game_title' => 'required|string',
                'game_store_type' => 'required|string',
                'game_price' => 'required|numeric',
                'game_discount' => 'required|integer',
                'game_image' => 'required',
                'game_video_link' => 'required',
                'game_description' => 'required|string',
                'game_developer' => 'required|string',
                'game_publisher' => 'required|string',
                'game_release_date' => 'required|date',
            ]);
    
            // Create a new game instance with the validated data
            $game = new Game([
                'game_title' => $request->input('game_title'),
                'game_rating' => 0,
                'game_store_type' => $request->input('game_store_type'),
                'game_price' => $request->input('game_price'),
                'game_discount' => $request->input('game_discount'),
                'game_image' => $request->input('game_image'),
                'game_video_link' => $request->input('game_video_link'),
                'game_description' => $request->input('game_description'),
                'game_developer' => $request->input('game_developer'),
                'game_publisher' => $request->input('game_publisher'),
                'game_release_date' => $request->input('game_release_date'),
            ]);
    
            $game->save();
            
            return redirect()->route('dashboard');
        } catch (\Throwable $th) {
            return view('error', [
                'error' => $th->getMessage()
            ]);
        }
    }

    public function deleteGameView($gameID)
    {
        Game::where('id', $gameID)->delete();

        return redirect()->route('adminDashboard');
    }

    public function getAllGames()
    {

        $allGames = Game::all();;

        if(auth()->user()->role == 'admin'){
            return view('adminDashboard', [
                'allGames' => $allGames,
            ]);
        }else{
            return view('dashboard', [
                'allGames' => $allGames,
            ]);
        }
    }

    public function getGameItem($gameID)
    {
        $gameItem = Game::where('id', $gameID)->first();

        return view('viewItem', [
            'gameItem' => $gameItem,
        ]);
    }
}