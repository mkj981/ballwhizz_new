<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{News, Player, Teams, Leagues};
use Illuminate\Support\Facades\File;

class NewsController extends Controller
{
    public function create()
    {
        return view('admin.news.create', [
            'players' => Player::orderBy('en_common_name')->get(),
            'teams' => Teams::orderBy('en_name')->get(),
            'leagues' => Leagues::orderBy('en_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'en_title' => 'required|string|max:255',
            'ar_title' => 'required|string|max:255',
            'en_text' => 'nullable|string',
            'ar_text' => 'nullable|string',
            'en_short_desc' => 'nullable|string|max:255',
            'ar_short_desc' => 'nullable|string|max:255',
            'hashtags' => 'nullable|string|max:255',
            'video' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        $news = News::create($data);
        $news->players()->sync($request->players ?? []);
        $news->teams()->sync($request->teams ?? []);
        $news->leagues()->sync($request->leagues ?? []);

        return redirect()->route('admin.news.index')->with('success', '📰 News added successfully!');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', [
            'news' => $news,
            'players' => Player::orderBy('en_common_name')->get(),
            'teams' => Teams::orderBy('en_name')->get(),
            'leagues' => Leagues::orderBy('en_name')->get(),
            'selectedPlayers' => $news->players->pluck('id')->toArray(),
            'selectedTeams' => $news->teams->pluck('id')->toArray(),
            'selectedLeagues' => $news->leagues->pluck('id')->toArray(),
        ]);
    }

    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'en_title' => 'required|string|max:255',
            'ar_title' => 'required|string|max:255',
            'en_text' => 'nullable|string',
            'ar_text' => 'nullable|string',
            'en_short_desc' => 'nullable|string|max:255',
            'ar_short_desc' => 'nullable|string|max:255',
            'hashtags' => 'nullable|string|max:255',
            'video' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($news->image) File::delete(public_path('storage/' . $news->image));
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        $news->update($data);
        $news->players()->sync($request->players ?? []);
        $news->teams()->sync($request->teams ?? []);
        $news->leagues()->sync($request->leagues ?? []);

        return redirect()->route('admin.news.index')->with('success', '💾 News updated successfully!');
    }
}
