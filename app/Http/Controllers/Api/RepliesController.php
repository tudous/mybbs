<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Reply;
use App\Models\Topic;
use App\Transformers\ReplyTransformer;
use App\Http\Requests\Api\RepliesRequest;
use App\Policies\ReplyPolicy;

class RepliesController extends Controller
{
    public function store(RepliesRequest $request,Topic $topic,Reply $reply)
    {
        $reply->content=$request->content;
        $reply->user_id=$this->user()->id;
        $reply->topic_id=$topic->id;
        $reply->save();

        return $this->response->item($reply, new ReplyTransformer())
            ->setStatusCode(201);
    }

    public function destroy(Topic $topic,Reply $reply)
    {
        if($reply->topic_id != $topic->id){
            return $this->response->errorBadRequest();
        }

        $this->authorize('destroy', $reply);
        $reply->delete();

        return $this->response->noContent();
    }
}
