<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateComment;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;

class CommentControler extends Controller
{
    protected $comment;
    protected $user;

    public function __construct(Comment $comment, User $user)
    {
        $this->comment = $comment;
        $this->user = $user;
    }

    public function index(Request $request, $userId)
    {
        if (!($user = $this->user->find($userId))) {
            return redirect()->back();
        }

        $comments = $user
            ->comments()
            ->where('body', 'LIKE', "%{$request->search}%")
            ->get();

        return view('users.comments.index', ['user' => $user, 'comments' => $comments]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($userId)
    {
        if (!($user = $this->user->find($userId))) {
            return redirect()->back();
        }

        return view('users.comments.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateComment $request, $userId)
    {
        if (!($user = $this->user->find($userId))) {
            return redirect()->back();
        }

        $user->comments()->create([
            'body' => $request->body,
            'visible' => isset($request->visible),
        ]);

        return redirect()->route('comments.index', $user->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($userId, $id)
    {
        if (!($comment = $this->comment->find($id))) {
            return redirect()->back();
        }

        $user = $comment->user;

        return view('users.comments.edit', compact('user', 'comment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateComment $request, $id)
    {
        if (!($comment = $this->comment->find($id))) {
            return redirect()->back();
        }

        $comment->update($request->only('body', 'visible'));

        $user = $comment->user;
        $comments = $user->comments;

        return view('users.comments.index', ['user' => $user, 'comments' => $comments]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
