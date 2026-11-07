<?php

namespace App\Http\Middleware;

use App\Models\FamilyMember;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        $member = FamilyMember::where('user_id', $user->id)->first();

        $isComplete = !empty($user->name)
            && !empty($user->email)
            // && !empty(optional($member)->phone)
            && !empty(optional($member)->gender);

        if (!$isComplete) {
            return redirect()
                ->route('user.settings')
                ->with('warning', 'Please complete your profile first.');
        }

        return $next($request);
    }
}
