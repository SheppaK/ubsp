<?php

namespace App\Http\Controllers\Modules\BoardingHouse;

use App\Http\Controllers\Controller;
use App\Models\Modules\BoardingHouse\RoommatePost;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoommateController extends Controller
{
    public function __construct(protected ModuleManager $modules) {}

    public function index(Request $request): View
    {
        $query = RoommatePost::active()->with('user')->latest();

        if ($request->filled('city')) {
            $query->where('preferred_city', 'like', '%'.$request->city.'%');
        }

        if ($request->filled('type') && $request->type !== 'any') {
            $query->where(fn ($q) => $q->where('preferred_type', $request->type)->orWhere('preferred_type', 'any'));
        }

        if ($request->filled('max_budget')) {
            $query->where('budget', '<=', $request->max_budget);
        }

        return view('modules.boarding-house.roommates.index', [
            'config' => $this->modules->get('boarding-house'),
            'posts' => $query->paginate(12)->withQueryString(),
            'myPosts' => RoommatePost::where('user_id', auth()->id())->latest()->get(),
            'filters' => $request->only(['city', 'type', 'max_budget']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'bio' => ['required', 'string', 'max:2000'],
            'budget' => ['nullable', 'integer', 'min:0'],
            'preferred_type' => ['required', 'in:single,double,shared,studio,any'],
            'preferred_city' => ['nullable', 'string', 'max:255'],
        ]);

        RoommatePost::create([
            ...$validated,
            'user_id' => auth()->id(),
            'is_active' => true,
        ]);

        return back()->with('success', 'Roommate profile posted!');
    }

    public function destroy(RoommatePost $post): RedirectResponse
    {
        abort_unless($post->user_id === auth()->id(), 403);
        $post->delete();

        return back()->with('success', 'Post removed.');
    }
}
