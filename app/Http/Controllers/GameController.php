<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Game;


class GameController extends Controller{

    public function play() {
        $authUser = Auth::user(); 
        
        //Rolling the dice, checking and save the game.
        $dice1 = random_int(1, 6);
        $dice2 = random_int(1, 6);
        
        $sum = $dice1 + $dice2; 
        $result = ($sum === 7);

        $game = new Game();
        $game->user_id = $authUser->id;
        $game->dice1 = $dice1;
        $game->dice2 = $dice2;
        $game->result = $result;
        $game->save();
        
        //Show game results.
        return response()->json([
            'message' => 'The die has been cast. Look at the result.',
            'Dice 1' => $dice1,
            'Dice 2' => $dice2,
            'Sum' => ($result) ? 7 : $sum,
            'Result' => $result ? "You won!" : "You lose!",
            ], 200);
    }

    public function index_games(){

        $authUser = Auth::user();
        $games = $authUser->games;
    
        //Game counter
        if ($games->count() > 0) {
            $gamesList = [];
            $wonCount = 0; 
    
            foreach ($games as $game) {
                $result = $game->result ? 'You won' : 'You lose';
    
                //Checking the sum for a 7 and add to the array.
                $is_Seven = $game->dice1 + $game->dice2 === 7;
    
                if ($is_Seven) {
                    $wonCount++;
                }
    
                $allGames = [
                    'Dice 1' => $game->dice1,
                    'Dice 2' => $game->dice2,
                    'Result' => $result,
                ];
    
                $gamesList[] = $allGames;
            }
    
            //Success Percentage Rate calculation
            $successPct = $games->count() > 0 ? ($wonCount / $games->count()) * 100 : 0;
    
            return response()->json([
                'Number of games' => $gamesList,
                'Success Percentage Rate' => $successPct,
            ], 200);

        } else {

            return response()->json(['message' => 'This player does not have games at the moment.'], 202);
        }
    }

    public function destroy(){
        $authID = Auth::id();
        
        $user = User::find($authID);

        //After finding the user verify if has any game and delete them.
        if ($user->games()->count() > 0) {
            $user->games()->delete();
            return response()->json(['message' => 'All your games have being deleted.'], 200);

        } else {
            return response()->json(['message' => 'You do not have games. Play first.'], 204);
        }
    }
}
