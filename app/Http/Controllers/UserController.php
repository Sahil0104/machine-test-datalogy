<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // ─── List page ───────────────────────────────────────────────────────────

    public function index()
    {
        return view('users.index');
    }

    // ─── AJAX DataTable data ─────────────────────────────────────────────────

    public function ajaxData(Request $request)
    {
        $draw   = $request->input('draw', 1);
        $start  = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value', '');

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name',  'like', "%{$search}%")
                  ->orWhere('email',      'like', "%{$search}%");
            });
        }

        $total    = User::count();
        $filtered = $query->count();
        $users    = $query->skip($start)->take($length)->get();

        $data = $users->map(function ($user, $index) use ($start) {
            return [
                'DT_RowId' => 'row_' . $user->id,
                'sr'       => $start + $index + 1,
                'first_name' => e($user->first_name),
                'last_name'  => e($user->last_name),
                'email'      => e($user->email),
                'actions'    => $user->id,
            ];
        });

        return response()->json([
            'draw'            => intval($draw),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }

    // ─── Add user ────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
        ], [
            'email.unique' => 'This email address already exists.',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make('password123'), // default password
        ]);

        return response()->json(['success' => true, 'message' => 'User added successfully.', 'user' => $user]);
    }

    // ─── Get single user for edit ─────────────────────────────────────────────

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    // ─── Update user ──────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $id,
        ], [
            'email.unique' => 'This email address already exists.',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
        ]);

        return response()->json(['success' => true, 'message' => 'User updated successfully.']);
    }

    // ─── Delete user (AJAX) ───────────────────────────────────────────────────

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $isCurrentUser = (int) session('user_id') === (int) $user->id;

        $user->delete();

        if ($isCurrentUser) {
            $request->session()->flush();

            return response()->json([
                'success' => true,
                'message' => 'Your account has been deleted. Please sign in again.',
                'redirect' => route('login'),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
    }
}
