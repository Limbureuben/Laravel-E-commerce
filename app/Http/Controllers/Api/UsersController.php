<?php

namespace App\Http\Controllers\Api;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
     public function index()
        {
            $users = User::paginate(4);

            return response()->json([
                'success' => true,
                'Users' => $users
            ]);
        }

    public function destroy($id) {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
        }

        $user->delete();

        return response()->json([
        'success' => true,
        'message' => 'User deleted successfully'
    ]);
    }
}
