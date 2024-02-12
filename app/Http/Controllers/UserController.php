<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;



class UserController extends Controller
{
    
    public function update(Request $request) {
        
        //Validation rules
        $validator = Validator::make($request->all(), [
            'newname' => 'nullable|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        //Authenticate the user
        $user = Auth::guard('api')->user();
    
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        //Anonymous by default if user don't type any name or the name typed.
        $newname = $request->filled('newname') ? $request->input('newname') : 'Anonymous'; 
            
        //Update name
        User::where('id', $user->id)->update(['name' => $newname]);
        //$user->update(['name' => $newname]);

        return response()->json(['message' => 'Name update completed'], 200);
    } 
    
    public function index() {

        //Search for all players and show theirs information
        $users = User::all()->map(function ($user) {
            $totalGames = $user->games->count();
            $totalWins = $user->games->where('result', 1)->count();
                
            $successRate = $totalGames > 0 ? ($totalWins / $totalGames) * 100 : 0;
    
            return [
                'message' => 'Player Data',
                'Id' => $user->id,
                'Name' => $user->name,
                'E-mail' => $user->email,
                'Success Rate' => $successRate,
            ];
        });
    
        return response()->json(['users' => $users], 200);
    }

    public function getRanking(){
    
        //Search for all players and count victories
        $players = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->withCount(['games', 'games as wins_count' => function ($query) {
            $query->where('result', true);
        }])->get();
    
        if ($players->isEmpty()) {
            return response()->json(['message' => 'There are no players at the moment'], 404);
        }
    
        //Calculation of victories and success rate. Finally, show players in descending order.
        $playersData = $players->map(function ($player) {
            $game_won = $player->wins_count;
            $wins_rate = $player->games_count ? round(($game_won / $player->games_count) * 100, 2) : 0;
    
            return [
                'message' => 'Player Data',
                'Name' => $player->name,
                'Win Rate' => $wins_rate,
                'Wins' => $game_won,
                'Total Games' => $player->games_count
            ];
        });

        $ranking = $playersData->sortByDesc('Wins');
    
        return response()->json($ranking->values()->all(), 200);
    }

    public function getWinner() {
        
        //Search for all players and count victories. Finally, return the player with the highest win rate.
        $winner = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->withCount(['games', 'games as wins_count' => function ($query) {
            $query->where('result', true);
        }])->get()->sortByDesc(function ($user) {
            return $user->games_count ? $user->wins_count / $user->games_count : 0;
        })->first();
    
        if ($winner) {
            $game_won = $winner->wins_count;
            $wins_rate = $winner->games_count ? round(($game_won / $winner->games_count) * 100, 2) : 0;
    
            return response()->json([
                'message' => 'Player Data',
                'Name' => $winner->name,
                'Wins Rate' => $wins_rate,
                'Wins' => $game_won,
                'Total Games' => $winner->games_count
            ], 200);

        } else {

            return response()->json(['message' => 'There is no winner at all'], 404);
        }
    }
    
    public function getLoser() {

            //Search for all players and count victories. Finally, return the player with the worst win rate.
            $loser = User::whereHas('roles', function ($query) {
                $query->where('name', 'player');
            })->withCount(['games', 'games as wins_count' => function ($query) {
                $query->where('result', true);
            }])->get()->sortBy(function ($user) {
                return $user->games_count ? $user->wins_count / $user->games_count : 0;
            })->first();
    
            if ($loser) {
                $game_won = $loser->wins_count;
                $wins_rate = $loser->games_count ? round(($game_won / $loser->games_count) * 100, 2) : 0;
    
                return response()->json([
                    'message' => 'Player Data',
                    'Name' => $loser->name,
                    'Wins Rate' => $wins_rate,
                    'Wins' => $game_won,
                    'Total Games' => $loser->games_count
                ], 200);

            } else {

                return response()->json(['message' => 'There is no loser at all'], 404);
            }
        }
}
