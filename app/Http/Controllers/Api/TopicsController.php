<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use App\Transformers\TopicTransformer;
use App\Http\Requests\Api\TopicRequest;
use App\Policies\TopicPolicy;
use Illuminate\Http\Request;
use App\Models\User;

class TopicsController extends Controller
{
    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
        //fill 方法会将传参的键值数组填充到模型的属性中
        $topic->user_id = $this->user()->id;
        $topic->save();

        return $this->response->item($topic, new TopicTransformer())
            ->setStatusCode(201);
    }

    public function update(TopicRequest $request, Topic $topic)
    {

        $this->authorize('update',$topic);
        $topic->update($request->all());
        return $this->response->item($topic, new TopicTransformer())
            ->setStatusCode(201);
    }

    public function destroy(Topic $topic)
    {
        $this->authorize('destroy',$topic);
        $topic->delete();
        return $this->response->noContent();
    }

    public function index(Request $request,Topic $topic)
    {

        $topic=$topic->WithOrder($request->order)->paginate(20);
        return $this->response->paginator($topic, new TopicTransformer());

    }


    public function userIndex(User $user,Topic $topic)
    {
        $topics = $user->topics()->recent()->paginate(20);

        return $this->response->paginator($topics, new TopicTransformer());
    }
}
