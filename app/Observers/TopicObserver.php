<?php

namespace App\Observers;

use App\Models\Topic;
use App\Handlers\SlugTranslateHandler;
use App\Jobs\TranslateSlug;
use Log;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function creating(Topic $topic)
    {
        //
    }

    public function updating(Topic $topic)
    {
        //
    }

    public function saving(Topic $topic)
    {
        $topic->body=clean($topic->body,'user_topic_body');
        $topic->excerpt=make_excerpt($topic->body);
        // 如 slug 字段无内容，即使用翻译器对 title 进行翻译
        if ( ! $topic->slug) {

            //app() 允许我们使用 Laravel 服务容器 ，此处我们用来生成 SlugTranslateHandler 实例
            //$topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);
        }

    }

    public function saved(Topic $topic)
    {
         if ( ! $topic->slug) {
         //数据创建后推送到任务队列
         dispatch(new TranslateSlug($topic));
         }
    }

    //话题删除后，清空这个话题所有的回复
    public function deleted(Topic $topic)
    {
        \DB::table('replies')->where('topic_id',$topic->id)->delete();
    }
}