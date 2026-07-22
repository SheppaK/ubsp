<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Modules\UniversitySocial\Post;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UniversitySocialController extends Controller
{
    protected string $slug = 'university-social';

    protected string $resource = 'posts';

    public function __construct(protected ModuleManager $modules) {}

    public function dashboard(): View
    {
        return view("modules.{$this->slug}.dashboard", [
            'config' => $this->modules->get($this->slug),
            'stats' => ['count' => Post::count()],
        ]);
    }

    public function index(): View
    {
        return view("modules.{$this->slug}.index", [
            'config' => $this->modules->get($this->slug),
            'posts' => Post::latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view("modules.{$this->slug}.create", [
            'config' => $this->modules->get($this->slug),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'group_id' => ['nullable', 'exists:us_groups,id'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
        ]);

        Post::create([...$validated, 'user_id' => $request->user()->id]);

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'Post created successfully.');
    }

    public function edit(Post $post): View
    {
        return view("modules.{$this->slug}.edit", [
            'config' => $this->modules->get($this->slug),
            'post' => $post,
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'group_id' => ['nullable', 'exists:us_groups,id'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
        ]);

        $post->update($validated);

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'Post deleted successfully.');
    }
}
